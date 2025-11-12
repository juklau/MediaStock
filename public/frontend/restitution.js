// Configuration de la langue française pour Flatpickr => était dans le .html
flatpickr.localize(flatpickr.l10ns.fr);

// ============================================================================================
// == récupération des éléments d'un prêt afin de remplir les champs sur restitution.php =====
// ============================================================================================

// 
document.addEventListener("DOMContentLoaded", async () => {
    // Récupérer le code QR depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    const qrCode = urlParams.get("code");

    if (!qrCode || isNaN(qrCode)) {
        console.warn("QR code invalide ou manquant :", qrCode);
        return;
    }
    
    try {
        const response = await fetch(`/../api/getonepret.php?id=${encodeURIComponent(qrCode)}`);
        const result = await response.json();

        if (result.success && result.data) {
             const pret = result.data;
             const imageUrl = pret.image_url;
             const itemNom = pret.nom;
             const emprunteurNom = pret.emprunteur_nom;
             const emprunteurPrenom = pret.emprunteur_prenom;
             const classe = pret.formation;
             const etat = pret.etat;
             const noteDebut = pret.note_debut;
             const dateRetourPrevue = pret.date_retour_prevue;


            // Injecter les données dans la page =>remplir les champs
            if (document.getElementById("returnForm")) {
                const itemNameReturn = document.getElementById("itemNameReturn");
                if (itemNameReturn) {
                  itemNameReturn.textContent = itemNom;
                }
            }
            const nomInput = document.getElementById("emprunteurNomReturn");
            if (nomInput) nomInput.value = emprunteurNom;

            const prenomInput = document.getElementById("emprunteurPrenomReturn");
            if (prenomInput) prenomInput.value = emprunteurPrenom;
 

            const classeInput = document.getElementById("classeReturn");
            if (classeInput) {

              // si la formation est définie => on affiche
              if(classe !== null && classe !== undefined && classe.trim() !== ""){
                classeInput.value = classe;
              }else if(classe === null){
                // si c'est null => Intervenant
                classeInput.value = "INTERVENANT"; 
              }
            }

            // Afficher l'état du prêt avec un badge coloré
            const etatPretBadge = document.getElementById("etatPretBadge");
            if (etatPretBadge) {
              etatPretBadge.textContent = etat;
              etatPretBadge.className = "badge-etat " + etat.toLowerCase();
            }

            // Retirer tous les checked existants et pré-sélectionner le bon état
            const tousLesBoutonsReturn = document.querySelectorAll('input[name="etatReturn"]');
            tousLesBoutonsReturn.forEach(btn => {
              btn.removeAttribute('checked');
              btn.checked = false;
            });

            // Pré-sélectionner le bouton radio correspondant
            const etatRadios = document.querySelectorAll('input[name="etat"]');
            etatRadios.forEach(radio => {
                // "etat?" => permet de vérifier si "etat" existe sinon il provoque des problèmes
                if (radio.value.toLowerCase() === pret.etat?.toLowerCase()) {
                    radio.checked = true;
                }
            });

            // Remplir les notes
            const notesTextarea = document.getElementById("notesReturn");
            const notesCount = document.getElementById("notesCountReturn");
            if (notesTextarea && notesCount) {
              notesTextarea.value = noteDebut;
              notesCount.textContent = `${noteDebut.length} / 500`;
            }

            // Afficher l’icône
            const iconWrap = document.getElementById("productImageWrapReturn");
            if (imageUrl && imageUrl.startsWith("fa-")) {
                iconWrap.innerHTML = `<i class="${imageUrl} fa-5x" style="color: #333;"></i>`;
            } else if (imageUrl) {
                iconWrap.innerHTML = `<img src="${imageUrl}" alt="${itemNom}" class="img-fluid" style="max-height: 120px;">`;
            }

            // Compteur pour le commentaire de retour
            const commentaireTextarea = document.getElementById("commentaireReturn");
            const commentaireCount = document.getElementById("commentaireCountReturn");
            if (commentaireTextarea && commentaireCount) {
              commentaireTextarea.addEventListener("input", () => {
                commentaireCount.textContent = `${commentaireTextarea.value.length} / 500`;
              });
            }

            // Initialiser le calendrier avec la date de retour prévue
            try {
              const dpReturn = document.getElementById('datePickerReturn');
              const calendarContainerReturn = document.querySelector('.calendar-container-return');
              
              if (dpReturn && typeof flatpickr === 'function') {
                const parsedDate = new Date(dateRetourPrevue);
                const isoDate = parsedDate.toISOString().split('T')[0];

                flatpickr(dpReturn, {
                  inline: true,
                  appendTo: calendarContainerReturn || undefined,
                  altInput: true,
                  altFormat: 'j F Y',
                  dateFormat: 'Y-m-d',
                  locale: 'fr',
                  disableMobile: true,
                  defaultDate: isoDate,
                  onDayCreate: function(dObj, dStr, fp, dayElem) {
                    const dayDate = dayElem.dateObj.toISOString().split('T')[0];
                    if (dayDate === isoDate) {
                      dayElem.classList.add("highlighted-date");
                    }
                  }
                });
              }
            } catch (err) {
              console.warn('flatpickr init failed for return page', err);
            }
        } else {
        alert("Prêt introuvable : " + result.message);
        }
    } catch (error) {
        console.error("Erreur lors de la récupération du prêt :", error);
        alert("Une erreur est survenue lors du chargement du prêt.");
    }

    // ================================================================
    // ========== clôturer un prêt actif ==============================
    // ================================================================
    
    
    const formReturn = document.getElementById("returnForm");
    if (formReturn) {
      formReturn.addEventListener("submit", async function (e) {
        e.preventDefault();

        // Vérifier si le formulaire est valide
        if (!formReturn.checkValidity()) {
          e.stopPropagation();
          formReturn.classList.add("was-validated");
          return;
        }

        try {
          // Récupérer l'ID de l'item depuis l'URL
          const urlParams = new URLSearchParams(window.location.search);
          const itemId = parseInt(urlParams.get("code"));

          if (!itemId || isNaN(itemId)) {
            alert("QR code invalide ou manquant.");
            return;
          }


          // Vérifier si l'item est dans les prêts actifs => GET
          const resActive = await fetch("/api/getactiveloans.php");
          const rawActive = await resActive.text();
          const resultActive = JSON.parse(rawActive);

          if (!resultActive.success || !Array.isArray(resultActive.data)) {
            alert("Impossible de vérifier les prêts actifs : " + resultActive.message);
            return;
          }

          const isItemActive = resultActive.data.some(pret => parseInt(pret.item_id) === itemId);
          if (!isItemActive) {
            alert("Impossible de clôturer ce prêt : aucun prêt actif trouvé pour cet article.");
            return;
          }


          // Récupérer l'état à la restitution
          const etatFin = document.querySelector('input[name="etatReturn"]:checked')?.value;
          if (!etatFin) {
            alert("Veuillez sélectionner l'état à la restitution.");
            return;
          }


          //récupérer le commentaire
          const commentaire = document.getElementById("commentaireReturn")?.value.trim() || "";


          // Clôturer le prêt avec un commentaire => POST
          const cloturePayload = {
            id: itemId,
            note_fin: commentaire
          };

          console.log("Payload envoyé à cloturepret.php :", cloturePayload);

          const resCloture = await fetch("/api/cloturepret.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(cloturePayload)
          });

          const rawCloture = await resCloture.text();
          console.log("Réponse brute de cloturepret.php :", rawCloture);

          let resultCloture;
          try {
            resultCloture = JSON.parse(rawCloture);
          } catch (parseErr) {
            console.error("Erreur de parsing JSON :", parseErr);
            alert("Réponse invalide du serveur : " + rawCloture);
            return;
          }
          
          if (!resultCloture.success) {

            // Empêcher la restitution si le prêt est déjà clôturé
            alert("Impossible de clôturer ce prêt : " + resultCloture.message);
            return;
          }


           // Mettre à jour l'état de l'item, si la clôture est réussie =>POST
          const updateEtatPayload = {
            id: itemId,
            etat: etatFin 
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

          
          // Afficher le modal de succès si tout se passe bien
          const modal = new bootstrap.Modal(document.getElementById("successModalReturn"));
          modal.show();

          document.getElementById("successModalReturn").addEventListener("hidden.bs.modal", () => {
              window.location.href = "index.php";
          }, { once: true });
          
        } catch (err) {
          console.error("Erreur JS :", err);
          alert("Erreur : " + err.message);
          return; // =>Empêche le modal même en cas d'erreur JS
          }
      });
    }
  
});