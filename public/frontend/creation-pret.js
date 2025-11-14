
// Configuration de la langue française pour Flatpickr => était dans le .html
flatpickr.localize(flatpickr.l10ns.fr);


// ===============================================================
// == récupération des éléments d'un item (QRCode, nom, icon) ====
// ===============================================================

document.addEventListener("DOMContentLoaded", async () => {
    // Récupérer le code QR depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const qrCode = urlParams.get("code");

    if (!qrCode || isNaN(qrCode)) {
        console.warn("QR code invalide ou manquant :", qrCode);
        return;
    }
    
    try {
        const response = await fetch(`/../api/getoneitem.php?id=${encodeURIComponent(qrCode)}`);
        const result = await response.json();

        if (result.success && result.data) {
             const item = result.data;

            // Injecter les données dans la page
            document.getElementById("itemName").textContent = item.nom || "Nom inconnu";

            // Pré-sélectionner le bouton radio correspondant
            const etatRadios = document.querySelectorAll('input[name="etat"]');
            etatRadios.forEach(radio => {
                // "etat?" => permet de vérifier si "etat" existe sinon il provoque des problèmes
                if (radio.value.toLowerCase() === item.etat?.toLowerCase()) {
                    radio.checked = true;
                }
            });

            // Afficher l’icône ou image
            const iconWrap = document.getElementById("productImageWrap");
            if (item.image_url && item.image_url.startsWith("fa-")) {
                iconWrap.innerHTML = `<i class="${item.image_url} fa-5x" style="color: #333;"></i>`;
            } else if (item.image_url) {
                iconWrap.innerHTML = `<img src="${item.image_url}" alt="${item.nom}" class="img-fluid" style="max-height: 120px;">`;
            }
        } else {
        alert("Matériel introuvable : " + result.message);
        }
    } catch (error) {
        console.error("Erreur lors de la récupération du matériel :", error);
        alert("Une erreur est survenue lors du chargement du matériel.");
    }
});

// =====================================================
// ==========   création d'un prêt   ===================
// =====================================================
document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("loanForm");
    const datePicker = document.getElementById("datePicker");
    const notes = document.getElementById("notes");
    const notesCount = document.getElementById("notesCount");
    const classeSelect = document.getElementById("classe");
    const calendarContainer = document.querySelector(".calendar-container");

    // Initialiser Flatpicker pour la période de prêt
    if (datePicker && typeof flatpickr === "function") {
        flatpickr(datePicker, {
            mode: 'range',
            inline: true,
            appendTo: calendarContainer || undefined,
            altInput: true,
            altFormat: 'j F Y',
            dateFormat: 'Y-m-d',
            locale: 'fr',
            minDate: 'today',
            disableMobile: true,
            conjunction: ' au ',
            rangeSeparator: ' au ',
            minDate: 'today',
            locale: 'fr',
            defaultHour: 12
        });
    }

    // Initialiser compteur de notes
    if (notes && notesCount) {
        notes.addEventListener("input", () => {
        notesCount.textContent = `${notes.value.length} / 500`;
        });
        notesCount.textContent = `${notes.value.length} / 500`;
    }

//   // Compteur de caractères pour les notes
//   notes.addEventListener("input", () => {
//     notesCount.textContent = `${notes.value.length} / 500`;
//   });

    
    // Limiter le menu déroulant des classes à 4 options visibles
    if (classeSelect) {
        classeSelect.addEventListener("mousedown", function() {
        this.size = 4;
        });
        classeSelect.addEventListener("blur", function() {
        this.size = 1;
        });
        classeSelect.addEventListener("change", function() {
        this.size = 1;
        this.blur();
        });
    }

    // Récupérer l'ID de l'item depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const itemId = parseInt(urlParams.get("code"));

    if (!itemId || isNaN(itemId)) {
        alert("QR code invalide ou manquant.");
        return;
    }

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        // Validation HTML5
        if (!form.checkValidity()) {
        form.classList.add("was-validated");
        return;
        }

        // Récupérer les valeurs du formulaire
        // const intervenant = document.getElementById("intervenant").value.trim();
        const nom = document.getElementById("emprunteurNom").value.trim();
        const prenom = document.getElementById("emprunteurPrenom").value.trim();
        const classe = document.getElementById("classe").value;
        const etat = document.querySelector("input[name='etat']:checked").value; 
        const note = notes.value.trim();
        // const periode = datePicker.value.split(" à ");
        const dateValue = datePicker.value;

        // Accepter " au " ou " to " comme séparateur
        let periode = dateValue.split(" au ");
        if (periode.length !== 2) {
        periode = dateValue.split(" to ");
        }

        //il attend 2 dates:; début et fin
        if (periode.length !== 2 || !periode[0] || !periode[1]) {
        alert("Veuillez sélectionner une date de prêt ET une date de retour");
        return;
        }

        console.log("Valeur du champ datePicker:", datePicker.value);

        const [dateSortie, dateRetour] = periode.map(d => d.trim());

        try {
            let formationId = null;
            let role = "etudiant(e)";
            
            if(classe === "INTERVENANT"){
                role = "intervenant";
            }else{
                // récuperer l'id de la formation si ce n'est pas un intervenant
                const resFormation = await fetch(`/api/getidbynameformation.php?nom=${(classe)}`);

                const rawFormation = await resFormation.text();
                console.log("Réponse brute de getidbynameformation.php :", rawFormation);

                let resultFormation;
                try {
                    resultFormation = JSON.parse(rawFormation);
                } catch (err) {
                    console.error("Erreur de parsing JSON (formation) :", err);
                    alert("Réponse invalide du serveur : " + rawFormation);
                    return;
                }

                 if (!resultFormation.success || resultFormation.formation_id === null) {
                    alert("Impossible de récupérer l'ID de la formation : " + resultFormation.message);
                    return;
                }

                // console.log("réponse de getidbynameformation.php", resultFormation);

                formationId = parseInt(resultFormation.formation_id);
            }

 
            // Création de l’emprunteur
            const emprunteurPayload = {
                emprunteur_nom: nom,
                emprunteur_prenom: prenom,
                role: role,
                formation_id: formationId
            };

            console.log("Payload envoyé à addemprunteur.php :", emprunteurPayload);
            const resEmprunteur = await fetch("/api/addemprunteur.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(emprunteurPayload)
            });

            // const resultEmprunteur = await resEmprunteur.json();

            // if (!resultEmprunteur.success || !resultEmprunteur.emprunteur_id) {
            //     alert("Erreur lors de la création de l'emprunteur : " + resultEmprunteur.message);
            //     return;
            // }

            const rawEmprunteur = await resEmprunteur.text();
            console.log("Réponse brute de addemprenteur.php :", rawEmprunteur);

            let resultEmprunteur;
            try {
                resultEmprunteur = JSON.parse(rawEmprunteur);
            } catch (err) {
                console.error("Erreur de parsing JSON (emprunteur) :", err);
                alert("Réponse invalide du serveur : " + rawEmprunteur);
                return;
            }

            // console.log("réponse de addemprunteur.php", resultEmprunteur);

            const emprunteurId = resultEmprunteur.emprunteur_id;


            // Création du prêt
            const pretPayload = {
                item_id: itemId,
                emprunteur_id: emprunteurId,
                preteur_id: 1, // à adapter selon ton système de session
                date_sortie: dateSortie,
                date_retour_prevue: dateRetour,
                note_debut: note,
                note_fin: etat
            };

            console.log("Payload envoyé à addpret.php :", pretPayload);
            const resPret = await fetch("/api/addpret.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(pretPayload)
            });

            // const resultPret = await resPret.json();

            const rawText = await resPret.text();
            console.log("Réponse brute de addpret.php :", rawText);

            let resultPret;

            try {
                resultPret = JSON.parse(rawText);
            } catch (err) {
                console.error("Erreur de parsing JSON :", err);
                alert("Réponse invalide du serveur : " + rawText);
                return;
            }


            // Mettre à jour l'état de l'item =>POST
            const updateEtatPayload = {
                id: itemId,
                etat: etat 
            };

            const resUpdate = await fetch("/api/updateitem.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(updateEtatPayload)
            });

            const rawUpdate = await resUpdate.text();
            console.log("Réponse brute de updateitem.php :", rawUpdate);

            const resultUpdate = JSON.parse(rawUpdate);

            if (!resultUpdate.success) {
                alert("Erreur lors de la mise à jour de l'état de l'item : " + resultUpdate.message);
                return;
            }

            
            if (resultPret.success) {
                // Afficher le modal de succès
                const modal = new bootstrap.Modal(document.getElementById("successModal"));
                modal.show();

                document.getElementById("successModal").addEventListener("hidden.bs.modal", () => {
                    window.location.href = "index.php";
                }, { once: true });

                form.reset();
                form.classList.remove("was-validated");
                notesCount.textContent = "0 / 500";
                } else {
                    alert("Erreur lors de la création du prêt : " + resultPret.message);
                }
        } catch (error) {
        console.error("Erreur lors de la création du prêt :", error);
        alert("Une erreur est survenue lors de la création du prêt.");
        }
    });
});