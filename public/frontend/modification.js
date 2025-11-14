// =================================================================================================
// == récupération des éléments d'un item afin de remplir les champs sur modification-item.php =====
// =================================================================================================

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
            const itemId = item.id;
            const itemNom = item.nom;
            const imageUrl = item.image_url;
            const itemModel= item.model;
            const etat = item.etat;
            const categorie = item.categorie;
            const qrCode = item.qr_code; //?????????????


            // Injecter les données dans la page =>remplir les champs
            if (document.getElementById("modificationForm")) {
                const itemNameReturn = document.getElementById("itemNameReturn");
                if (itemNameReturn) {
                  itemNameReturn.textContent = itemNom;
                }
            }
            const nomInputNew = document.getElementById("nomItemModif");
            if (nomInputNew) nomInputNew.value = itemNom;

            const modelInput = document.getElementById("modeleItemModif");
            if (modelInput) {

              // si le modèle est définie => on affiche
              if(itemModel !== null && itemModel !== undefined && itemModel.trim() !== ""){
                modelInput.value = itemModel;
              }else if(itemModel === ""){
                // si c'est "" 
                modelInput.value = "Modèle non défini"; 
              }
            }
            
            // Afficher l'état du prêt avec un badge coloré
            const etatPretBadge = document.getElementById("etatPretBadge");
            if (etatPretBadge) {
              etatPretBadge.textContent = etat;
              etatPretBadge.className = "badge-etat " + etat.toLowerCase();
            }

            // Retirer tous les checked existants et pré-sélectionner le bon état
            const tousLesBoutonsModif = document.querySelectorAll('input[name="etatModif"]');
            tousLesBoutonsModif.forEach(btn => {
              btn.removeAttribute('checked');
              btn.checked = false;
            });

            // Pré-sélectionner le bouton radio correspondant
            const etatRadios = document.querySelectorAll('input[name="etatModif"]');
            etatRadios.forEach(radio => {
                // "etat?" => permet de vérifier si "etat" existe sinon il provoque des problèmes
                if (radio.value.toLowerCase() === item.etat?.toLowerCase()) {
                    radio.checked = true;
                }
            });

           
            // Afficher l’icône
            const iconWrap = document.getElementById("productImageWrapReturn");
            if (imageUrl && imageUrl.startsWith("fa-")) {
                iconWrap.innerHTML = `<i class="${imageUrl} fa-5x" style="color: #333;"></i>`;
            } else if (imageUrl) {
                iconWrap.innerHTML = `<img src="${imageUrl}" alt="${itemNom}" class="img-fluid" style="max-height: 120px;">`;
            }

            // préselectionner la catégorie de l'item
            const categorieInput = document.getElementById("categorieItemModif");
            if (categorieInput) {
                const options = Array.from(categorieInput.options);
                const match = options.find(option => 
                    option.value.toLowerCase() === categorie.toLowerCase());
                
                if(match){
                    match.selected = true;
                }else{
                    console.log("Erreur pendant la récupération de la catégorie de l'item.");
                }
            }
 
             genererQRCodeDynamique(itemId);

        } else {
        alert("Matériel introuvable : " + result.message);
        }
    } catch (error) {
        console.error("Erreur lors de la récupération du matériel :", error);
        alert("Une erreur est survenue lors du chargement du matériel.");
    }
});

/**
 * Générer le QR code dynamiquement dans la fiche produit
 */
async function genererQRCodeDynamique(materielId) {
  try {
    const ficheQRCode = document.getElementById('ficheQRCode');
    
    // Nettoyer le conteneur
    ficheQRCode.innerHTML = '';
    
    // Créer un conteneur pour le QR code
    const qrContainer = document.createElement('div');
    qrContainer.style.display = 'flex';
    qrContainer.style.justifyContent = 'center';
    qrContainer.style.alignItems = 'center';
    ficheQRCode.appendChild(qrContainer);
    
    // Générer le QR code avec l'ID du matériel (même logique que materiel_test.js)
    new QRCode(qrContainer, {
      text: materielId.toString(),
      width: 150,
      height: 150,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
    
    console.log('QR Code généré pour l\'ID:', materielId);
    
  } catch (error) {
    console.error('Erreur lors de la génération du QR Code:', error);
    document.getElementById('ficheQRCode').innerHTML = 
      '<div class="alert alert-danger small">Erreur génération QR Code</div>';
  }
}



// ============================================================
// ==========  update de l'item dans la BDD     ===============
// ============================================================

document.getElementById("categorieItemModif").addEventListener("change", function () {
  const nouvelleCategorie = this.value;
  const nouvelleImageUrl = getImageUrlByCategorie(nouvelleCategorie);

  const iconWrap = document.getElementById("productImageWrapReturn");
  if (iconWrap) {
    iconWrap.innerHTML = `<i class="${nouvelleImageUrl} fa-5x" style="color: #333;" title="${nouvelleCategorie}"></i>`;
  }
});



const formModif = document.getElementById("modificationForm");
    if (formModif) {
      formModif.addEventListener("submit", async function (e) {
        e.preventDefault();

        // Vérifier si le formulaire est valide
        if (!formModif.checkValidity()) {
          e.stopPropagation();
          formModif.classList.add("was-validated");
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

            // récpération les valeurs de champs du formulaire
            const nomModif = document.getElementById("nomItemModif").value;
            const modelModif = document.getElementById("modeleItemModif").value;
            const etatModif = document.querySelector('input[name="etatModif"]:checked')?.value;
            const categorieModif = document.getElementById("categorieItemModif").value;
            
            const categorieIdModif = await getCategorieIdFromName(categorieModif);

            if (!categorieIdModif) {
                alert("Impossible de récupérer l'ID de la catégorie.");
                return;
            }

            const imageUrlModif = getImageUrlByCategorie(categorieModif);

            
            // Mettre à jour l'état de l'item, si la clôture est réussie =>POST
            const updateEtatPayload = {
                id: itemId,
                nom: nomModif,
                model: modelModif,
                etat: etatModif,
                categorie_id: categorieIdModif,
                image_url: imageUrlModif,
                qr_code: itemId
            };

            console.log("Payload envoyé à updateitem.php :", updateEtatPayload);

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
            const modal = new bootstrap.Modal(document.getElementById("successModal"));
            modal.show();

            document.getElementById("successModal").addEventListener("hidden.bs.modal", () => {
                window.location.href = "index.php";
            }, { once: true });

        }catch (err){
            console.error("Erreur JS :", err);
            alert("Erreur : " + err.message);
            return; // =>Empêche le modal même en cas d'erreur JS
        }
    });
}


// ============================================================
// ==========  récupération id du catégorie     ===============
// ============================================================
async function getCategorieIdFromName(nomCategorie) {
    try {
      const response = await fetch(`/api/getidbynamecat.php?nom=${(nomCategorie)}`);
      const result = await response.json();

      if (result.success && result.categorie_id) {
        const id = result.categorie_id;
        return  id;// l'ID de la catégorie
      } else {
        console.warn("Catégorie non trouvée :", result.message);
        return null;
      }
    } catch (error) {
      console.error("Erreur lors de la récupération de l'ID de catégorie :", error);
      return null;
    }
}


/**
 * Retourne l'image_url associée à une catégorie donnée
 * @param {string} categorie - Nom de la catégorie (ex: "Audio", "Connectique")
 * @returns {string} - Classe FontAwesome correspondant à l'icône
 */
function getImageUrlByCategorie(categorie) {
    const mapping = {
        "informatique": "fa-solid fa-desktop",
        "audio": "fa-solid fa-volume-high",
        "connectique": "fa-solid fa-plug",
        "autres": "fa-solid fa-server"
    };

    const key = categorie.trim().toLowerCase();
    
    //renvoie la valeur du key   
    return mapping[key] || "fa-solid fa-box"; // valeur par défaut si non trouvée
}

// ============================================================
// ========== Affiche le message de succès      ===============
// ============================================================

// function afficherMessageSucces(materielId) {
//     const messageSucces = document.getElementById('messageSucces');
//     const messageTexte = document.getElementById('messageTexte');
    
//     messageTexte.textContent = `Matériel ajouté avec succès ! ID: ${materielId}`;
//     messageSucces.classList.remove('d-none');
    
//     // Masquer le message après 5 secondes
//     setTimeout(() => {
//       messageSucces.classList.add('d-none');
//     }, 5000);
// }