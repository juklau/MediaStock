 // ********************************************************** js pour la **********************************************************************

//.............pour index.php.............//



// Variable globale pour stocker les matériels chargés depuis l'API
let items = [];

/**
 * Charger les matériels
 */
async function chargerMateriels() {
  try {
    const response = await fetch('/../api/getitemsavailability.php');
    const data = await response.json();
    items = data.data || [];

    renderItems();
    attachDeleteHandlers();
  } catch (error) {
    console.error('Erreur lors du chargement des matériels:', error);

    // Afficher un message d'erreur à l'utilisateur
    const container = document.getElementById("inventoryList");
    if (container) {
      container.innerHTML = `
        <div class="alert alert-warning" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Erreur lors du chargement des données. Veuillez rafraîchir la page.
        </div>
      `;
    }
  }
}

// ouvrir la fenêtre pour scanner le QRcode => création de prêt et restitution
function startQrScan() {
  const qrContainer = document.getElementById("qr-reader-container");
  qrContainer.style.display = "block";

  const html5QrCode = new Html5Qrcode("qr-reader");

  html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    async (decodedText) => {
      // Stop le scan
      await html5QrCode.stop();
      qrContainer.style.display = "none";

      try {
        const resp = await fetch(
          `../api/getPageByQRCode.php?code=${encodeURIComponent(decodedText)}`
        );
        console.log("Response Status:", resp.status);
        const data = await resp.json();

        if (data.success && data.targetPage) {
          const finalUrl = `/frontend/${data.targetPage}?code=${encodeURIComponent(decodedText)}`;
          console.log("URL :", finalUrl);
          window.location.href = finalUrl;
        } else {
          alert("QR code non reconnu !");
        }
      } catch (err) {
        console.error(err);
        alert("Erreur réseau ou QR code invalide");
      }
    },
    (errorMessage) => {
      // Scan en cours, pas une erreur
    }
  )
  .catch((err) => {
    console.error("Impossible d'accéder à la caméra :", err);
    alert("Impossible d'accéder à la caméra. Vérifiez les permissions et HTTPS.");
  });
}

document.getElementById("scanPretBtn").addEventListener("click", startQrScan);
document.getElementById("scanRestitutionBtn").addEventListener("click", startQrScan);

/** 
 * Afficher les matériels depuis l'API
 */
function renderItems() {
  const categoryFilter = document.getElementById("categoryFilter").value;
  const statusFilter = document.getElementById("statusFilter").value;
  const etatFilter = document.getElementById("etatFilter").value;
  const container = document.getElementById("inventoryList");
  container.innerHTML = "";

  // Créer le tableau filtré pour correspondre à l'ordre d'affichage
  const filteredItems = items.filter(item => {
    const matchCategorie= !categoryFilter || item.categorie.toLowerCase() === categoryFilter;
    const matchStatut = !statusFilter || item.statut === statusFilter;
    const matchEtat = !etatFilter || item.etat === etatFilter;
    const nonArchived = item.archived === 0;
    return matchCategorie && matchStatut && matchEtat && nonArchived ;
  });


filteredItems.forEach(item => {
    const statusClass = `status-${item.statut.toLowerCase()}`;
    const etatClass = `etat-${item.etat.toLowerCase()}`;

    const listItem = document.createElement("div");
    listItem.className = "list-group-item";
    listItem.dataset.itemId = item.id;

    listItem.innerHTML = `
        <div class="left">
          <div class="item-icon"><i class="${item.image_url}"></i></div>
          <div class="item-meta">
            <div><strong>${item.nom}</strong> ${item.model !== null ? item.model : ''}</div>
            <div><span class="status-dot ${statusClass}"></span>${item.statut}</div>
            <div><span class="etat-button ${etatClass}">${item.etat} état</span></div>
          </div>
        </div>
        <div class="item-right">
          ${item.statut === 'disponible' ? '' : `<div class="text-muted small">${item.dateAjout || ''}</div>`}

          <button class="change-btn" title="Modifier" data-id="${item.id}"><i class="fas fa-file-lines fa-lg"></i></button>
          <button class="trash-btn" title="Supprimer" data-id="${item.id}"><i class="fas fa-trash-alt fa-lg"></i></button>
        </div>
      `;

      container.appendChild(listItem);
});

  // Attacher les gestionnaires de clic après le rendu
  attachClickHandlers(filteredItems);

  console.log("Catégories disponibles :", items.map(i => i.categorie));
}


//attacher les gestionnaires de clic sur les items
function attachClickHandlers(filteredItems) {
  const listItems = document.querySelectorAll('#inventoryList .list-group-item');
  
  listItems.forEach((listItem, index) => {
    listItem.style.cursor = 'pointer';
    
    listItem.addEventListener('click', function(e) {

      // Si on clique sur le bouton de modification, on redirige vers la page de modif
      const modifBtn = e.target.closest('.change-btn');
      if (modifBtn) {
        const itemId = parseInt(modifBtn.dataset.id);
        if (itemId) {
          const finalUrl = `/frontend/modification-item.php?code=${encodeURIComponent(itemId)}`;
          window.location.href = finalUrl;
        }
        e.stopPropagation();
        return;
      }

      // Si on clique sur le bouton de suppression, on appelle le handler de suppression
      const deleteBtn = e.target.closest('.trash-btn');
      if (deleteBtn) {
        const itemId = parseInt(deleteBtn.dataset.id);
        if (itemId) {
          attachDeleteHandlers(itemId); 
        }
        e.stopPropagation();
        return;
      }
      
       // Sinon, clic sur l’élément lui-même => ouvrir la fiche
      // Utiliser l'ID réel depuis l'attribut data-item-id
      const itemId = parseInt(listItem.dataset.itemId);
      if (itemId) {
        console.log('Clic sur item ID:', itemId);
        
        // Créer un objet item temporaire avec juste l'ID pour la fonction ouvrirFicheProduit
        const itemTemp = { id: itemId };
        ouvrirFicheProduit(itemTemp, 0); // Index non utilisé dans la nouvelle version
      } else {
        console.error('ID de l\'item non trouvé dans data-item-id');
      }
    });
  });
}


// Après rendu, attache les gestionnaires de suppression
function attachDeleteHandlers(){
  const deleteBtns = document.querySelectorAll('.trash-btn');
  const deleteModalEl = document.getElementById('deleteModal');

  if(!deleteModalEl) return;

  const bsModal = new bootstrap.Modal(deleteModalEl);
  const deleteIcon = document.getElementById('deleteIcon');
  const deleteName = document.getElementById('deleteName');
  const confirmBtn = document.getElementById('confirmDeleteBtn');
  let currentItemId = null;

  deleteBtns.forEach((btn) => {
    btn.addEventListener('click', async (e) => {
      e.stopPropagation(); // Empêcher l'ouverture de l'offcanvas
      const itemId = parseInt(btn.dataset.id);
      currentItemId = itemId;
      
      // Trouver l'item dans le tableau
      const item = items.find(i => i.id === itemId);
      if (item) {
        deleteIcon.innerHTML = `<i class="${item.image_url} fa-3x"></i>`;
        deleteName.textContent = item.nom;
        bsModal.show();
      }
    });
  });
  
  // Gestionnaire de confirmation
  if (confirmBtn) {

    // Retirer les anciens listeners pour éviter les doublons
    const newConfirmBtn = confirmBtn.cloneNode(true);
    confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
    
    newConfirmBtn.addEventListener('click', async () => {
      if (currentItemId !== null) {
        try {

          const response = await fetch(`/api/archiveitembyid.php?id=${currentItemId}`);
          const result = await response.json();

          const container = document.getElementById("inventoryList");

          if(result.success){
            bsModal.hide();

            if (container) {
              container.innerHTML = `
                <div class="alert alert-success text-center mt-3" role="alert">
                  Matériel archivé avec succès.
                </div>
              `;
            }

            // Attendre 3s avant de recharger la liste
            setTimeout(() => {
              chargerMateriels();
            }, 3000);

            console.log("Archivage du matériel est réussi.");
          }else{
            console.error("Erreur d'archivage : ", result.message);
            alert("Échec de l'archivage : " + result.message);
          }
        } catch (error) {
          console.error("Erreur lors de la l'archivage: ", error);
          alert("Une erreur est survenue lors de l'archivage du matériel.");
        }
      }
    });
  }
}

const catFilterEl = document.getElementById('categoryFilter');
if (catFilterEl) catFilterEl.addEventListener('change', renderItems);
const statusFilterEl = document.getElementById('statusFilter');
if (statusFilterEl) statusFilterEl.addEventListener('change', renderItems);
const etatFilterEl = document.getElementById('etatFilter');
if (etatFilterEl) etatFilterEl.addEventListener('change', renderItems);

// Charger les données au chargement de la page
window.onload = function(){
  chargerMateriels();
};

// archivage d'un item
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.btn-trash');
  if (!btn) return;

  const id = btn.dataset.id;
  if (!id) return;

  if (!confirm('Voulez-vous vraiment archiver cet item ?')) return;

  btn.disabled = true;

  fetch(`/api/archiveitembyid.php?id=${encodeURIComponent(id)}`, {
    method: 'GET',
    headers: { 'Accept': 'application/json' }
  })
  .then(resp => resp.json())
  .then(data => {
    if (data.success) {
      // supprimer la ligne ou marquer comme archivé
      const row = btn.closest('.item-row') || btn.closest('tr');
      if (row) 
        row.remove();
      else 
        btn.remove();
      alert(data.message);
    } else {
      alert('Erreur : ' + (data.message || 'Archiver impossible'));
      btn.disabled = false;
    }
  })
  .catch(err => {
    console.error(err);
    alert('Erreur réseau');
    btn.disabled = false;
  });
});

// **************************************************** fin js page principale **********************************************************************



//  ************************************************** js page création de prêt *********************************************************************
   
//se trouve sur la page creation-pret.js

// ****************************************************** fin js page création de prêt **************************************************************


// ******************************************************  js page de restitution *****************************************************************

//se trouve sur la page restitution.js

// ****************************************************** fin js page de restitution **************************************************************


// ********************************************************** js fiche produit (offcanvas) **********************************************************

/**
 * Récupérer l'historique des prêts via l'API
 */
async function getHistoriquePrets(materielId) {
  try {
    return await API.getPretsByMaterielId(materielId);
  } catch (error) {
    console.error('Erreur lors du chargement de l\'historique:', error);
    return [];
  }
}


/**
 * =====================================
 * GESTION DYNAMIQUE DE L'OFFCANVAS 
 * =====================================
 * Ouvrir l'offcanvas avec la fiche produit - VERSION DYNAMIQUE
 * Récupère les données depuis la base de données via les APIs
 */
async function ouvrirFicheProduit(item, itemIndex) {
  try {
    // ========== ÉTAPE 1: Récupération des données détaillées de l'item ==========
    console.log('Chargement des détails pour l\'item ID:', item.id);
    
    // Affichage d'un loader pendant le chargement
    afficherLoaderOffcanvas();
    
    // Récupérer les détails complets de l'item depuis l'API
    const itemDetails = await recupererDetailsItem(item.id);
    
    if (!itemDetails) {
      console.error('Impossible de récupérer les détails de l\'item');
      afficherErreurOffcanvas('Impossible de charger les détails du matériel');
      return;
    }

    // ========== ÉTAPE 2: Remplissage des informations de base ==========
    remplirInformationsBase(itemDetails);

    // ========== ÉTAPE 3: Génération du QR Code ==========
    await genererQRCodeDynamique(itemDetails.id);

    // ========== ÉTAPE 4: Chargement de l'historique des prêts ==========
    await chargerHistoriquePrets(itemDetails.id);

    // ========== ÉTAPE 5: Ouverture de l'offcanvas ==========
    const offcanvas = new bootstrap.Offcanvas(document.getElementById('ficheProduitOffcanvas'));
    offcanvas.show();
    
    console.log('Offcanvas ouvert avec succès pour:', itemDetails.nom);

    // Initialiser les boutons QR code après l'affichage
    initialiserBoutonsQRCode();

  } catch (error) {
    console.error('Erreur lors de l\'ouverture de l\'offcanvas:', error);
    afficherErreurOffcanvas('Une erreur est survenue lors du chargement');
  }
}


/**
 * =====================================
 * FONCTIONS POUR LES BOUTONS QR CODE
 * =====================================
 */

/**
 * Initialiser les boutons d'action du QR Code
 */
function initialiserBoutonsQRCode() {
  const btnTelecharger = document.getElementById('btnTelechargerIndex');
  const btnImprimer = document.getElementById('btnImprimerIndex');

  if (btnTelecharger) {
    btnTelecharger.addEventListener('click', telechargerQRCode);
  }

  if (btnImprimer) {
    btnImprimer.addEventListener('click', imprimerQRCode);
  }
}

/**
 * Télécharger le QR Code en tant qu'image PNG
 */
function telechargerQRCode() {
  const qrcodeContainer = document.getElementById('ficheQRCode');
  
  if (!qrcodeContainer) {
    console.error('Container QR code introuvable');
    return;
  }

  // Récupérer l'image du QR code
  const qrcodeImg = qrcodeContainer.querySelector('img');
  
  if (!qrcodeImg) {
    console.error('Image QR code introuvable');
    alert('Aucun QR code à télécharger');
    return;
  }

  // Créer un lien de téléchargement
  const link = document.createElement('a');
  link.href = qrcodeImg.src;
  
  // Récupérer le nom du matériel pour nommer le fichier
  const nomMateriel = document.getElementById('ficheNom')?.textContent || 'qrcode';
  link.download = `QRCode_${nomMateriel.replace(/\s+/g, '_')}.png`;
  
  // Déclencher le téléchargement
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  
  console.log('QR code téléchargé:', link.download);
}

/**
 * Imprimer le QR Code
 */
function imprimerQRCode() {
  const qrcodeContainer = document.getElementById('ficheQRCode');
  
  if (!qrcodeContainer) {
    console.error('Container QR code introuvable');
    return;
  }

  // Récupérer l'image du QR code
  const qrcodeImg = qrcodeContainer.querySelector('img');
  
  if (!qrcodeImg) {
    console.error('Image QR code introuvable');
    alert('Aucun QR code à imprimer');
    return;
  }

  // Récupérer le nom du matériel
  const nomMateriel = document.getElementById('ficheNom')?.textContent || 'Matériel';

  // Créer une nouvelle fenêtre pour l'impression
  const printWindow = window.open('', '_blank', 'width=800,height=600');
  
  if (!printWindow) {
    alert('Veuillez autoriser les pop-ups pour imprimer le QR code');
    return;
  }

  // Contenu HTML pour l'impression
  printWindow.document.write(`
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Impression QR Code - ${nomMateriel}</title>
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body {
          font-family: Arial, sans-serif;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
          padding: 20px;
        }
        .print-container {
          text-align: center;
          max-width: 400px;
        }
        h1 {
          color: #333;
          font-size: 24px;
          margin-bottom: 20px;
        }
        .qr-code {
          margin: 20px 0;
          padding: 20px;
          background: white;
          border: 2px solid #ddd;
          border-radius: 10px;
          display: inline-block;
        }
        .qr-code img {
          display: block;
          width: 250px;
          height: 250px;
        }
        .info {
          color: #666;
          font-size: 14px;
          margin-top: 20px;
        }
        @media print {
          body {
            background: white;
          }
          .no-print {
            display: none;
          }
        }
      </style>
    </head>
    <body>
      <div class="print-container">
        <h1>${nomMateriel}</h1>
        <div class="qr-code">
          <img src="${qrcodeImg.src}" alt="QR Code ${nomMateriel}">
        </div>
        <div class="info">
          <p><strong>MediaStock Inc</strong></p>
          <p>Scannez ce code pour accéder aux détails du matériel</p>
        </div>
      </div>
      <script>
        window.onload = function() {
          window.print();
          // Fermer la fenêtre après l'impression (optionnel)
          // window.onafterprint = function() { window.close(); };
        };
      </script>
    </body>
    </html>
  `);
  
  printWindow.document.close();
  console.log('Fenêtre d\'impression ouverte pour:', nomMateriel);
}



/**
 * =====================================
 * FONCTIONS POUR LES BOUTONS QR CODE
 * =====================================
 */

/**
 * Initialiser les boutons d'action du QR Code
 */
function initialiserBoutonsQRCode() {
  const btnTelecharger = document.getElementById('btnTelechargerIndex');
  const btnImprimer = document.getElementById('btnImprimerIndex');

  if (btnTelecharger) {
    btnTelecharger.addEventListener('click', telechargerQRCode);
  }

  if (btnImprimer) {
    btnImprimer.addEventListener('click', imprimerQRCode);
  }
}

/**
 * Télécharger le QR Code en tant qu'image PNG
 */
function telechargerQRCode() {
  const qrcodeContainer = document.getElementById('ficheQRCode');
  
  if (!qrcodeContainer) {
    console.error('Container QR code introuvable');
    return;
  }

  // Récupérer l'image du QR code
  const qrcodeImg = qrcodeContainer.querySelector('img');
  
  if (!qrcodeImg) {
    console.error('Image QR code introuvable');
    alert('Aucun QR code à télécharger');
    return;
  }

  // Créer un lien de téléchargement
  const link = document.createElement('a');
  link.href = qrcodeImg.src;
  
  // Récupérer le nom du matériel pour nommer le fichier
  const nomMateriel = document.getElementById('ficheNom')?.textContent || 'qrcode';
  link.download = `QRCode_${nomMateriel.replace(/\s+/g, '_')}.png`;
  
  // Déclencher le téléchargement
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  
  console.log('QR code téléchargé:', link.download);
}

/**
 * Imprimer le QR Code
 */
function imprimerQRCode() {
  const qrcodeContainer = document.getElementById('ficheQRCode');
  
  if (!qrcodeContainer) {
    console.error('Container QR code introuvable');
    return;
  }

  // Récupérer l'image du QR code
  const qrcodeImg = qrcodeContainer.querySelector('img');
  
  if (!qrcodeImg) {
    console.error('Image QR code introuvable');
    alert('Aucun QR code à imprimer');
    return;
  }

  // Récupérer le nom du matériel
  const nomMateriel = document.getElementById('ficheNom')?.textContent || 'Matériel';

  // Créer une nouvelle fenêtre pour l'impression
  const printWindow = window.open('', '_blank', 'width=800,height=600');
  
  if (!printWindow) {
    alert('Veuillez autoriser les pop-ups pour imprimer le QR code');
    return;
  }

  // Contenu HTML pour l'impression
  printWindow.document.write(`
    <!DOCTYPE html>
    <html lang="fr">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Impression QR Code - ${nomMateriel}</title>
      <style>
        * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
        }
        body {
          font-family: Arial, sans-serif;
          display: flex;
          flex-direction: column;
          align-items: center;
          justify-content: center;
          min-height: 100vh;
          padding: 20px;
        }
        .print-container {
          text-align: center;
          max-width: 400px;
        }
        h1 {
          color: #333;
          font-size: 24px;
          margin-bottom: 20px;
        }
        .qr-code {
          margin: 20px 0;
          padding: 20px;
          background: white;
          border: 2px solid #ddd;
          border-radius: 10px;
          display: inline-block;
        }
        .qr-code img {
          display: block;
          width: 250px;
          height: 250px;
        }
        .info {
          color: #666;
          font-size: 14px;
          margin-top: 20px;
        }
        @media print {
          body {
            background: white;
          }
          .no-print {
            display: none;
          }
        }
      </style>
    </head>
    <body>
      <div class="print-container">
        <h1>${nomMateriel}</h1>
        <div class="qr-code">
          <img src="${qrcodeImg.src}" alt="QR Code ${nomMateriel}">
        </div>
        <div class="info">
          <p><strong>MediaStock Inc</strong></p>
          <p>Scannez ce code pour accéder aux détails du matériel</p>
        </div>
      </div>
      <script>
        window.onload = function() {
          window.print();
          // Fermer la fenêtre après l'impression (optionnel)
          // window.onafterprint = function() { window.close(); };
        };
      </script>
    </body>
    </html>
  `);
  
  printWindow.document.close();
  console.log('Fenêtre d\'impression ouverte pour:', nomMateriel);
}

/**
 * =====================================
 * FONCTIONS D'AFFICHAGE ET D'ÉTAT
 * =====================================
 */

/**
 * Afficher un loader dans l'offcanvas pendant le chargement
 */
function afficherLoaderOffcanvas() {
  // Loader pour le nom
  document.getElementById('ficheNom').innerHTML = `
    <div class="placeholder-glow">
      <span class="placeholder col-8"></span>
    </div>
  `;
  
  // Loader pour les badges
  document.getElementById('ficheEtat').innerHTML = `
    <div class="placeholder-glow">
      <span class="placeholder col-4 me-2"></span>
      <span class="placeholder col-3"></span>
    </div>
  `;
  
  // Loader pour le QR code
  document.getElementById('ficheQRCode').innerHTML = `
    <div class="text-center p-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>
  `;
  
  // Loader pour l'historique
  document.getElementById('ficheHistorique').innerHTML = `
    <div class="text-center p-3">
      <div class="spinner-border spinner-border-sm text-primary" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>
  `;
}

/**
 * Afficher une erreur dans l'offcanvas
 */
function afficherErreurOffcanvas(message) {
  document.getElementById('ficheNom').textContent = 'Erreur de chargement';
  document.getElementById('ficheEtat').innerHTML = 
    '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Erreur</span>';
  document.getElementById('ficheQRCode').innerHTML = `
    <div class="alert alert-danger small text-center" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>${message}
    </div>
  `;
  document.getElementById('ficheHistorique').innerHTML = `
    <div class="alert alert-danger small" role="alert">
      <i class="fas fa-exclamation-triangle me-2"></i>Impossible de charger les données
    </div>
  `;
}

/**
 * =====================================
 * RÉCUPÉRATION DES DONNÉES API
 * =====================================
 */

/**
 * Récupérer les détails complets d'un item - VERSION HYBRIDE
 * Disponibilité depuis getitemsavailability.php + États depuis getoneitem.php
 */
async function recupererDetailsItem(itemId) {
  try {
    console.log('🔍 Récupération hybride pour item ID:', itemId);
    
    // ========== ÉTAPE 1: Récupérer la disponibilité depuis getitemsavailability.php ==========
    let itemAvailability = null;
    try {
      const responseList = await fetch('../api/getitemsavailability.php');
      if (responseList.ok) {
        const resultList = await responseList.json();
        const items = resultList.data || resultList;
        itemAvailability = items.find(item => item.id == itemId);
        
        if (itemAvailability) {
          console.log('Disponibilité récupérée depuis getitemsavailability.php:', {
            id: itemAvailability.id,
            nom: itemAvailability.nom,
            is_available: itemAvailability.is_available,
            statut: itemAvailability.statut
          });
        }
      }
    } catch (error) {
      console.warn('⚠ Erreur getitemsavailability.php:', error);
    }
    
    // ========== ÉTAPE 2: Récupérer les détails complets depuis getoneitem.php ==========
    let itemDetails = null;
    try {
      const responseDetails = await fetch(`../api/getoneitem.php?id=${itemId}`);
      if (responseDetails.ok) {
        const resultDetails = await responseDetails.json();
        if (resultDetails.success && resultDetails.data) {
          itemDetails = resultDetails.data;
          console.log('Détails récupérés depuis getoneitem.php:', {
            id: itemDetails.id,
            nom: itemDetails.nom,
            etat: itemDetails.etat
          });
        }
      }
    } catch (error) {
      console.warn('⚠ Erreur getoneitem.php:', error);
    }
    
    // ========== ÉTAPE 3: Fusionner les données ==========
    if (itemAvailability || itemDetails) {
      // Prendre les détails de getoneitem.php comme base
      const finalItem = itemDetails || itemAvailability;
      
      // Remplacer la disponibilité par celle de getitemsavailability.php si disponible
      if (itemAvailability && finalItem) {
        finalItem.is_available = itemAvailability.is_available;
        finalItem.statut = itemAvailability.statut;
        console.log('Données fusionnées - Disponibilité depuis getitemsavailability + Détails depuis getoneitem');
      }
      
      console.log(' RÉSULTAT FINAL:', finalItem);
      return finalItem;
    } else {
      console.error(' Aucune donnée récupérée des deux APIs');
      return null;
    }
    
  } catch (error) {
    console.error(' ERREUR GÉNÉRALE recupererDetailsItem:', error);
    return null;
  }
}

/**
 * Récupérer l'historique des prêts depuis l'API getitemprethistory.php
 */
async function recupererHistoriquePrets(itemId) {
  try {
    const response = await fetch(`../api/getitemprethistory.php?id=${itemId}`);
    const result = await response.json();
    
    if (result.success && result.data) {
      console.log('Historique prêts récupéré:', result.data);
      return result.data;
    } else {
      console.log('Aucun historique trouvé pour cet item:', result.message);
      return [];
    }
  } catch (error) {
    console.error('Erreur lors de la récupération de l\'historique:', error);
    return [];
  }
}

/**
 * =====================================
 * REMPLISSAGE DES INFORMATIONS
 * =====================================
 */

/**
 * Remplir les informations de base du matériel dans l'offcanvas
 */
function remplirInformationsBase(itemDetails) {
  // ========== DÉBOGAGE : Voir toutes les propriétés reçues ==========
  console.log('=== DÉBOGAGE ITEM DETAILS ===');
  console.log('Données complètes reçues:', itemDetails);
  console.log('Disponibilité (is_available):', itemDetails.is_available);
  console.log('État physique (etat):', itemDetails.etat);
  console.log('Statut:', itemDetails.statut);
  console.log('==============================');

  // ========== Nom du matériel ==========
  const ficheNom = document.getElementById('ficheNom');
  const nomComplet = itemDetails.model ? 
    `${itemDetails.nom} ${itemDetails.model}` : 
    itemDetails.nom;
  ficheNom.textContent = nomComplet;

  // ========== Icône du matériel ==========
  const ficheIcon = document.getElementById('ficheIcon');
  const iconClass = itemDetails.image_url || 'fa-solid fa-box';
  ficheIcon.innerHTML = `<i class="${iconClass} fa-3x"></i>`;

  // ========== Badges d'état et de disponibilité ==========
  const ficheEtat = document.getElementById('ficheEtat');
  
  // Badge de disponibilité (calculé selon la logique métier)
  const badgeDisponibilite = genererBadgeDisponibilite(itemDetails);
  
  // Badge d'état physique (bon/moyen/mauvais)
  const badgeEtatPhysique = genererBadgeEtatPhysique(itemDetails.etat);
  
  // Afficher les deux badges
  ficheEtat.innerHTML = badgeDisponibilite + badgeEtatPhysique;
  
  console.log('Informations de base remplies pour:', itemDetails.nom);
  console.log('Badge disponibilité généré:', badgeDisponibilite);
  console.log('Badge état généré:', badgeEtatPhysique);
}

/**
 * Générer le badge de disponibilité selon la logique métier - VERSION OPTIMISÉE
 */
function genererBadgeDisponibilite(itemDetails) {
  console.log('=== DÉBOGAGE DISPONIBILITÉ OPTIMISÉ ===');
  console.log('Tous les champs de l\'item:', Object.keys(itemDetails));
  console.log('is_available:', itemDetails.is_available, 'Type:', typeof itemDetails.is_available);
  console.log('disponible:', itemDetails.disponible, 'Type:', typeof itemDetails.disponible);
  console.log('statut:', itemDetails.statut, 'Type:', typeof itemDetails.statut);
  console.log('========================================');
  
  let isAvailable = false;
  let raisonDisponibilite = 'Aucun champ trouvé';
  
  // ========== PRIORITÉ 1 : is_available (utilisé dans renderItems ligne 38-39) ==========
  if (itemDetails.is_available !== undefined) {
    // Conversion en booléen robuste
    if (typeof itemDetails.is_available === 'boolean') {
      isAvailable = itemDetails.is_available;
    } else if (typeof itemDetails.is_available === 'number') {
      isAvailable = itemDetails.is_available === 1;
    } else if (typeof itemDetails.is_available === 'string') {
      isAvailable = itemDetails.is_available === '1' || itemDetails.is_available.toLowerCase() === 'true';
    }
    raisonDisponibilite = `is_available = ${itemDetails.is_available} (${typeof itemDetails.is_available})`;
  }
  
  // ========== PRIORITÉ 2 : disponible (au cas où l'API utilise ce champ) ==========
  else if (itemDetails.disponible !== undefined) {
    if (typeof itemDetails.disponible === 'boolean') {
      isAvailable = itemDetails.disponible;
    } else if (typeof itemDetails.disponible === 'number') {
      isAvailable = itemDetails.disponible === 1;
    } else if (typeof itemDetails.disponible === 'string') {
      isAvailable = itemDetails.disponible === '1' || itemDetails.disponible.toLowerCase() === 'true';
    }
    raisonDisponibilite = `disponible = ${itemDetails.disponible} (${typeof itemDetails.disponible})`;
  }
  
  // ========== PRIORITÉ 3 : statut en fallback (utilisé dans renderItems ligne 52) ==========
  else if (itemDetails.statut !== undefined) {
    isAvailable = itemDetails.statut === 'disponible';
    raisonDisponibilite = `statut = ${itemDetails.statut}`;
  }
  
  console.log('Disponibilité finale:', isAvailable, '- Raison:', raisonDisponibilite);
  
  // ========== Génération du badge avec détection de retard ==========
  if (isAvailable) {
    return '<span class="badge bg-success me-2"><i class="fas fa-check-circle me-1"></i>Disponible</span>';
  } else {
    // Si statut indique un retard spécifique, l'afficher
    if (itemDetails.statut === 'retard' || itemDetails.statut === 'en_retard' || itemDetails.statut === 'retard_pret') {
      return '<span class="badge bg-danger me-2"><i class="fas fa-exclamation-triangle me-1"></i>En retard</span>';
    } else {
      return '<span class="badge bg-warning text-dark me-2"><i class="fas fa-clock me-1"></i>Indisponible</span>';
    }
  }
}

/**
 * Générer le badge d'état physique du matériel
 */
function genererBadgeEtatPhysique(etat) {
  console.log('Génération badge état physique pour:', etat);
  
  // Gérer les cas où l'état pourrait être null, undefined ou vide
  const etatNormalise = (etat && etat.toString().toLowerCase()) || 'bon';
  
  let badgeClass = '';
  let iconClass = '';
  let texte = '';
  
  switch (etatNormalise) {
    case 'bon':
    case 'bonne':
    case 'good':
      badgeClass = 'bg-success';
      iconClass = 'fas fa-thumbs-up';
      texte = 'Bon état';
      break;
    case 'moyen':
    case 'moyenne':
    case 'medium':
    case 'average':
      badgeClass = 'bg-warning text-dark';
      iconClass = 'fas fa-exclamation-triangle';
      texte = 'État moyen';
      break;
    case 'mauvais':
    case 'mauvaise':
    case 'bad':
    case 'poor':
      badgeClass = 'bg-danger';
      iconClass = 'fas fa-thumbs-down';
      texte = 'Mauvais état';
      break;
    default:
      badgeClass = 'bg-secondary';
      iconClass = 'fas fa-question';
      texte = `État: ${etat || 'Non défini'}`;
  }
  
  console.log('Badge état généré:', texte);
  return `<span class="badge ${badgeClass}"><i class="${iconClass} me-1"></i>${texte}</span>`;
}

/**
 * =====================================
 * GESTION DU QR CODE DYNAMIQUE
 * =====================================
 */

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
    
     // Afficher les boutons d'action du QR Code
    const qrcodeActions = document.getElementById('qrcodeActionsIndex');
    
    if (qrcodeActions) {
      qrcodeActions.classList.remove('d-none');
    }


  } catch (error) {
    console.error('Erreur lors de la génération du QR Code:', error);
    document.getElementById('ficheQRCode').innerHTML = 
      '<div class="alert alert-danger small">Erreur génération QR Code</div>';
  }
}

/**
 * =====================================
 * GESTION DE L'HISTORIQUE DES PRÊTS
 * =====================================
 */

/**
 * Charger et afficher l'historique des prêts
 */
async function chargerHistoriquePrets(itemId) {
  const ficheHistorique = document.getElementById('ficheHistorique');
  
  try {
    // Récupérer l'historique depuis l'API
    const historique = await recupererHistoriquePrets(itemId);
    
    // Afficher l'historique
    afficherHistoriquePretsDynamique(historique);
    
  } catch (error) {
    console.error('Erreur lors du chargement de l\'historique:', error);
    ficheHistorique.innerHTML = `
      <div class="alert alert-danger small" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Erreur lors du chargement de l'historique
      </div>
    `;
  }
}

/**
 * Afficher l'historique des prêts dynamiquement dans l'offcanvas
 * Cette fonction utilise les vraies données de la base de données
 */
function afficherHistoriquePretsDynamique(historique) {
  const ficheHistorique = document.getElementById('ficheHistorique');
  
  // ========== Cas 1: Aucun historique disponible ==========
  if (!historique || historique.length === 0) {
    ficheHistorique.innerHTML = `
      <div class="text-center text-muted py-4">
        <i class="fas fa-history fa-2x mb-3 opacity-50"></i>
        <h6>Aucun prêt enregistré</h6>
        <p class="small mb-0">Ce matériel n'a jamais été emprunté</p>
      </div>
    `;
    return;
  }

  // ========== Cas 2: Historique disponible ==========
  console.log('Affichage de l\'historique:', historique);
  
  // Trier par date de prêt (plus récent en premier)
  const historiqueTrié = [...historique].sort((a, b) => 
    new Date(b.date_pret || b.datePret) - new Date(a.date_pret || a.datePret)
  );

  console.log('Historique trié:', historiqueTrié);
  console.log('Historique non trié:', historique);
  
  let html = '<div class="list-group list-group-flush">';
  
  historiqueTrié.forEach((pret, index) => {
    // ========== Analyse des données de prêt ==========
    const emprunteur_prenom = pret.emprunteur_prenom || '';
    const emprunteur_nom = pret.emprunteur_nom || '';
    const emprunteurComplet = `${emprunteur_prenom} ${emprunteur_nom}`.trim() || 'Emprunteur inconnu';
    const datePret = pret.date_sortie || pret.datePret || 'Non définie';
    const dateRetourPrevue = pret.date_retour_prevue || pret.dateRetourPrevue || pret.dateRetour || 'Non définie';
    const dateRetourEffectif = pret.date_retour_effective || pret.dateRetourEffectif || null;
    const notePret = pret.note_debut || pret.notePret || null;
    const noteRetour = pret.note_fin || pret.noteRetour || null;
    
    // ========== Détermination du statut ==========
    const estRestitue = dateRetourEffectif !== null;
    const estEnRetard = !estRestitue && new Date(dateRetourPrevue) < new Date();
    
    // Badges de statut
    let badgeStatut = '';
    if (estRestitue) {
      badgeStatut = '<span class="badge bg-secondary"><i class="fas fa-check me-1"></i>Restitué</span>';
    } else if (estEnRetard) {
      badgeStatut = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>En retard</span>';
    } else {
      badgeStatut = '<span class="badge bg-primary"><i class="fas fa-clock me-1"></i>En cours</span>';
    }

    // ========== Génération du HTML pour ce prêt ==========
    html += `
      <div class="list-group-item ${index === 0 ? 'border-top-0' : ''}" style="display: block; padding: 14px 16px;">

        <!-- En-tête avec emprunteur et statut -->
        <div class="header" style="display: block; margin-bottom: 10px;">
          <div class="fw-bold text-dark" style="margin-bottom: 4px;">
            <i class="fas fa-user me-1"></i>${emprunteurComplet}
          </div>
          <div class="status" style="display: inline-block; margin-top: 2px;">
            ${badgeStatut}
          </div>
        </div>

        <!-- Dates de prêt -->
        <div class="small text-muted mb-2" style="display: block; margin-bottom: 10px;">
          <div style="margin-bottom: 4px;">
            <i class="fas fa-calendar-plus me-1 text-success"></i>
            <strong>Prêt :</strong> ${formatDateFrancaise(datePret)}
          </div>
          <div>
            <i class="fas fa-calendar-minus me-1 text-warning"></i>
            <strong>Retour prévu :</strong> ${formatDateFrancaise(dateRetourPrevue)}
          </div>
        </div>

        <!-- Notes du matériel -->
        <div class="notes small" style="display: block; margin-bottom: 8px;">
          <div style="margin-bottom: 4px;">
            <span class="text-muted">Note de prêt :</span> 
            <strong>${notePret || '—'}</strong>
          </div>

          ${estRestitue ? `
            <div>
              <span class="text-muted">Note de retour :</span> 
              <strong>${noteRetour || '—'}</strong>
            </div>
          ` : `
            <div style="margin-top: 2px;">
              <i class="fas fa-arrow-right text-muted mx-1"></i>
              <span class="text-muted fst-italic">En cours...</span>
            </div>
          `}
        </div>

        <!-- Date de restitution si applicable -->
        ${estRestitue ? `
          <div class="small text-success mt-2" style="display: block; margin-top: 8px;">
            <i class="fas fa-check-circle me-1"></i>
            <strong>Restitué le :</strong> ${formatDateFrancaise(dateRetourEffectif)}
          </div>
        ` : ''}

        <!-- Alerte retard si applicable -->
        ${estEnRetard ? `
          <div class="small text-danger mt-2" style="display: block; margin-top: 8px;">
            <i class="fas fa-exclamation-triangle me-1"></i>
            <strong>Retard de ${calculerJoursRetard(dateRetourPrevue)} jour(s)</strong>
          </div>
        ` : ''}
      </div>

    `;
  });
  
  html += '</div>';
  ficheHistorique.innerHTML = html;
}

/**
 * =====================================
 * FONCTIONS UTILITAIRES POUR L'HISTORIQUE
 * =====================================
 */

/**
 * Formater une date en français (DD/MM/YYYY)
 */
function formatDateFrancaise(dateStr) {
  if (!dateStr) return 'Non définie';
  
  try {
    const date = new Date(dateStr);
    return date.toLocaleDateString('fr-FR');
  } catch (error) {
    return dateStr; // Retourner la chaîne originale si le formatage échoue
  }
}

/**
 * Générer un badge pour l'état d'un prêt/restitution
 */
function genererBadgeEtatPret(etat) {
  if (!etat) return '<span class="badge bg-secondary">Non défini</span>';
  
  const etatNormalise = etat.toLowerCase();
  
  switch (etatNormalise) {
    case 'bon':
      return '<span class="badge bg-success">Bon</span>';
    case 'moyen':
      return '<span class="badge bg-warning text-dark">Moyen</span>';
    case 'mauvais':
      return '<span class="badge bg-danger">Mauvais</span>';
    default:
      return `<span class="badge bg-info">${etat}</span>`;
  }
}

/**
 * Calculer le nombre de jours de retard
 */
function calculerJoursRetard(dateRetourPrevue) {
  if (!dateRetourPrevue) return 0;
  
  try {
    const aujourdhui = new Date();
    const dateRetour = new Date(dateRetourPrevue);
    const diffTime = aujourdhui - dateRetour;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return Math.max(0, diffDays);
  } catch (error) {
    return 0;
  }
}

// Initialiser les données dans localStorage si elles n'existent pas (fallback)
if (!localStorage.getItem('materiels')) {
  // Copier les données depuis materiels.json dans localStorage
  fetch('./data/materiels.json')
    .then(response => response.json())
    .then(data => {
      localStorage.setItem('materiels', JSON.stringify(data.materiels));
    })
    .catch(error => console.error('Erreur lors de l\'initialisation des matériels:', error));
}

if (!localStorage.getItem('historiquePrets')) {
  // Copier les données depuis prets.json dans localStorage
  fetch('./data/prets.json')
    .then(response => response.json())
    .then(data => {
      localStorage.setItem('historiquePrets', JSON.stringify(data.prets));
    })
    .catch(error => console.error('Erreur lors de l\'initialisation des prêts:', error));
}

// ****************************************************** fin js fiche produit (offcanvas) **************************************************************
