//.............choix materiel.js.............//
function choisirMateriel(icon) {
      localStorage.setItem("selectedIcon", icon);
      window.location.href = "materiel.html";
}






const selectedIcon = localStorage.getItem("selectedIcon");
const iconContainer = document.getElementById("icon-container");

if (selectedIcon && iconContainer) {
  iconContainer.innerHTML = `
    <i class="fas fa-${selectedIcon}" 
       style="font-size: 5rem; color: #00; opacity: 0.8;"></i>
  `;
} else {
  iconContainer.innerHTML = `
    <p class="text-center text-muted">Aucun matériel sélectionné.</p>
  `;
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

// ============================================================
// ========== Génération du QRcode              ===============
// ============================================================

function genererQRCode(materielId) {
    const qrcodeDisplay = document.getElementById('qrcodeDisplay');
    
    // Nettoyer l'affichage précédent
    qrcodeDisplay.innerHTML = '';
    
    // Créer un conteneur pour le QR code
    const qrContainer = document.createElement('div');
    qrContainer.id = 'qrcode';
    qrContainer.style.padding = '20px';
    qrContainer.style.backgroundColor = 'white';
    qrContainer.style.borderRadius = '10px';
    qrContainer.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
    qrcodeDisplay.appendChild(qrContainer);
    
    
    // Générer le QR code avec l'ID
    qrcodeInstance = new QRCode(qrContainer, {
      text: materielId.toString(),
      width: 256,
      height: 256,
      colorDark: "#000000",
      colorLight: "#ffffff",
      correctLevel: QRCode.CorrectLevel.H
    });
    
    console.log('QR Code généré pour l\'ID:', materielId);
}


/**
 * Affiche le message de succès
 */
function afficherMessageSucces(materielId) {
    const messageSucces = document.getElementById('messageSucces');
    const messageTexte = document.getElementById('messageTexte');
    
    messageTexte.textContent = `Matériel ajouté avec succès ! ID: ${materielId}`;
    messageSucces.classList.remove('d-none');
    
    // Masquer le message après 5 secondes
    setTimeout(() => {
      messageSucces.classList.add('d-none');
    }, 5000);
}

/**
 * Affiche les boutons d'actions (télécharger, partager, imprimer)
 */
function afficherActions() {
  const actionsDiv = document.getElementById('qrcodeActions');
  actionsDiv.style.display = 'flex';
}







document.addEventListener('DOMContentLoaded', async () => {
  const category = localStorage.getItem('selectedCategory');
  console.log("Catégorie sélectionnée :", category);

  if (!category) return;

  const categorieId = await getCategorieIdFromName(category);

  if (categorieId) {
    console.log("ID de la catégorie :", categorieId);
  } else {
    console.warn("Impossible de récupérer l'ID de la catégorie.");
  }
});


// ============================================================
// ========== Ajoute dans la bdd                ===============
// ============================================================


document.getElementById('btnAjouterBD').addEventListener('click', async () => {
    const nomInput = document.getElementById('materielNom');
    const nom = nomInput.value.trim();
    const icon = localStorage.getItem("selectedIcon");
    const categorie = localStorage.getItem("selectedCategory");

    if (!nom || !icon ) {
      alert("Veuillez saisir le nom du matériel.");
      return;
    }

    //récuperer il du catégorie
    const categorieId = await getCategorieIdFromName(categorie);
    if(!categorieId){
      alert("Impossible de récuperer l'Id");
    }

    // Construction des données à envoyer
    const payload = {
      nom: nom,
      model: null,
      qr_code: "temporaire", // sera remplacé par l'ID retourné
      image_url: `/images/icons/${icon}.png`, // ou autre logique
      etat: "bon", // par défaut
      categorie_id: categorieId
    };

    try {
      // Envoi à l'API PHP
      const response = await fetch('/api/additem.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      // réponse à transformer en objet js
      const result = await response.json();

      if (result.success && result.item_id) {
        const itemId = result.item_id;

        // Mettre à jour le QR code avec l'ID réel
        genererQRCode(itemId);
        afficherMessageSucces(itemId);
        afficherActions();

        // Mémoriser l’ID pour les actions suivantes
        currentMaterielId = itemId;

        // Désactiver le bouton
        const btn = document.getElementById('btnAjouterBD');
        btn.disabled = true;
        btn.textContent = 'Matériel ajouté ✓';
        btn.style.opacity = '0.7';

      } else {
        alert("Erreur : " + result.message);
      }

    } catch (error) {
      console.error("Erreur lors de l'ajout :", error);
      alert("Une erreur est survenue lors de l'ajout.");
    }
});