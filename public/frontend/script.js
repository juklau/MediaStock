// ********************************************************** js page principale **********************************************************************
// Variable globale pour stocker les matériels chargés depuis l'API
let items = [];

/**
 * Charger les matériels depuis l'API
 */
async function chargerMateriels() {
  try {
    items = await API.getMateriels();
    console.log('Matériels chargés:', items.length);
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

/** 
 * Afficher les matériels depuis l'API
 */
function renderItems() {
  const categoryFilter = document.getElementById("categoryFilter").value;
  const statusFilter = document.getElementById("statusFilter").value;
  const container = document.getElementById("inventoryList");
  container.innerHTML = "";

  fetch(' /../api/getitemsavailability.php')
    .then(response => response.json())
    .then(data => {
      console.log('All Items:', data);

      const items = data.data; // les matériels renvoyés par API

      items.forEach(item => {
        // Appliquer les filtres
        if ((categoryFilter && item.categorie !== categoryFilter) ||
            (statusFilter && ((item.is_available ? 'disponible' : 'indisponible') !== statusFilter))) {
          return;
        }

        const statusClass = `status-${item.statut}`;

        const listItem = document.createElement("div");
        listItem.className = "list-group-item";
        listItem.dataset.itemId = item.id;

        listItem.innerHTML = `
          <div class="left">
            <div class="item-icon"><i class="${item.image_url}"></i></div>
            <div class="item-meta">
              <div><strong>${item.nom}</strong> ${item.model}</div>
              <div><span class="status-dot ${statusClass}"></span>${item.statut}</div>
            </div>
          </div>
          <div class="item-right">
            ${item.statut === 'disponible' ? '' : `<div class="text-muted small">${item.dateAjout || ''}</div>`}
            <button class="trash-btn" title="Supprimer" data-id="${item.id}"><i class="fas fa-trash-alt"></i></button>
          </div>
        `;

        container.appendChild(listItem);
      });

      // Attacher les gestionnaires de clic après le rendu
      attachClickHandlers();
    })
    .catch(error => {
      console.error('Erreur lors du chargement des matériels :', error);
      container.innerHTML = `<div class="error">Impossible de charger les matériels.</div>`;
    });
}

// Nouvelle fonction pour attacher les gestionnaires de clic sur les items
function attachClickHandlers() {
  const listItems = document.querySelectorAll('#inventoryList .list-group-item');
  const categoryFilter = document.getElementById("categoryFilter").value;
  const statusFilter = document.getElementById("statusFilter").value;
  
  // Créer le tableau filtré pour correspondre à l'ordre d'affichage
  const filteredItems = items.filter(item => {
    return (!categoryFilter || item.categorie === categoryFilter) &&
           (!statusFilter || item.status === statusFilter);
  });
  
  listItems.forEach((listItem, index) => {
    listItem.style.cursor = 'pointer';
    
    listItem.addEventListener('click', function(e) {
      // Ne pas ouvrir si on clique sur le bouton de suppression
      if (e.target.closest('.trash-btn')) {
        return;
      }
      
      // Trouver l'item correspondant dans le tableau
      if (filteredItems[index]) {
        const itemIndex = items.indexOf(filteredItems[index]);
        ouvrirFicheProduit(filteredItems[index], itemIndex);
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
        deleteIcon.innerHTML = `<i class="fas ${item.icone} fa-2x"></i>`;
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
          await API.deleteMateriel(currentItemId);
          bsModal.hide();
          await chargerMateriels(); // Recharger les données
        } catch (error) {
          console.error('Erreur lors de la suppression:', error);
          alert('Erreur lors de la suppression du matériel');
        }
      }
    });
  }
}

const catFilterEl = document.getElementById('categoryFilter');
if (catFilterEl) catFilterEl.addEventListener('change', renderItems);
const statusFilterEl = document.getElementById('statusFilter');
if (statusFilterEl) statusFilterEl.addEventListener('change', renderItems);

// Charger les données au chargement de la page
window.onload = function(){
  chargerMateriels();
};





//Scanner QR code pour creer ou restituer un materiel //

const qrReader = document.getElementById("qr-reader");

function startQrScan(targetPage) {
  qrReader.style.display = "block";

  const html5QrCode = new Html5Qrcode("qr-reader");

  html5QrCode.start(
    { facingMode: "environment" },
    { fps: 10, qrbox: 250 },
    (decodedText, decodedResult) => {
      console.log("QR Code détecté :", decodedText);
      html5QrCode.stop();
      qrReader.style.display = "none";

      // Rediriger vers la page correspondante en passant le code QR
      window.location.href = `${targetPage}?code=${encodeURIComponent(decodedText)}`;
    },
    (errorMessage) => {
      // Scan en cours
    }
  ).catch(err => {
    console.error("Impossible d'accéder à la caméra :", err);
  });
}

// Boutons
document.getElementById("scanPretBtn").addEventListener("click", () => startQrScan("creation-pret.html"));
document.getElementById("scanRestitutionBtn").addEventListener("click", () => startQrScan("restitution.html"));


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
 * Ajouter un prêt via l'API (appelé depuis creation-pret.html)
 */
async function ajouterPret(materielId, pretData) {
  try {
    const pretPayload = {
      materielId: materielId,
      emprunteur: pretData.nom + ' ' + pretData.prenom,
      datePret: pretData.datePret,
      dateRetour: pretData.dateRetour,
      etatPret: pretData.etat,
      intervenant: pretData.intervenant,
      classe: pretData.classe,
      notes: pretData.notes
    };
    
    await API.ajouterPret(pretPayload);
    return true;
  } catch (error) {
    console.error('Erreur lors de l\'ajout du prêt:', error);
    throw error;
  }
}

/**
 * Mettre à jour un prêt lors de la restitution
 */
async function mettreAJourRestitution(pretId, etatRetour) {
  try {
    await API.updatePret(pretId, {
      etatRetour: etatRetour,
      dateRestitution: new Date().toISOString().split('T')[0]
    });
    return true;
  } catch (error) {
    console.error('Erreur lors de la mise à jour de la restitution:', error);
    throw error;
  }
}

/**
 * Ouvrir l'offcanvas avec la fiche produit
 */
function ouvrirFicheProduit(item, itemIndex) {
  // Remplir les informations du produit
  document.getElementById('ficheNom').textContent = item.nom;
  
  // Icône
  const ficheIcon = document.getElementById('ficheIcon');
  ficheIcon.innerHTML = `<i class="fas ${item.icone} fa-3x"></i>`;
  
  // Afficher le status (disponible/indisponible/retard) ET l'état (Bon/Moyen/Mauvais)
  const ficheEtat = document.getElementById('ficheEtat');
  
  // Badge pour le status
  let statusBadge = '';
  switch(item.status) {
    case 'disponible':
      statusBadge = '<span class="badge bg-success me-2">Disponible</span>';
      break;
    case 'indisponible':
      statusBadge = '<span class="badge bg-warning me-2">Indisponible</span>';
      break;
    case 'retard':
      statusBadge = '<span class="badge bg-danger me-2">Retard</span>';
      break;
  }
  
  // Badge pour l'état (Bon/Moyen/Mauvais)
  const etatClass = item.etat ? item.etat.toLowerCase() : 'bon';
  const etatBadge = `<span class="badge badge-etat ${etatClass}">${item.etat || 'Bon'}</span>`;
  
  // Afficher les deux badges
  ficheEtat.innerHTML = statusBadge + etatBadge;
  
  // Générer le QR code avec l'ID du matériel
  const materielId = item.id;
  genererQRCodeFiche(materielId);
  
  // Afficher l'historique des prêts
  afficherHistoriquePrets(materielId);
  
  // Ouvrir l'offcanvas
  const offcanvas = new bootstrap.Offcanvas(document.getElementById('ficheProduitOffcanvas'));
  offcanvas.show();
}

/**
 * Générer le QR code dans la fiche produit
 */
function genererQRCodeFiche(materielId) {
  const ficheQRCode = document.getElementById('ficheQRCode');
  ficheQRCode.innerHTML = ''; // Nettoyer
  
  // Créer le QR code
  new QRCode(ficheQRCode, {
    text: materielId.toString(),
    width: 150,
    height: 150,
    colorDark: "#000000",
    colorLight: "#ffffff",
    correctLevel: QRCode.CorrectLevel.H
  });
}

/**
 * Afficher l'historique des prêts dans l'offcanvas
 */
async function afficherHistoriquePrets(materielId) {
  const ficheHistorique = document.getElementById('ficheHistorique');
  
  // Afficher un loader pendant le chargement
  ficheHistorique.innerHTML = `
    <div class="text-center py-3">
      <div class="spinner-border text-coral" role="status">
        <span class="visually-hidden">Chargement...</span>
      </div>
    </div>
  `;
  
  try {
    const historique = await getHistoriquePrets(materielId);
    
    if (historique.length === 0) {
      ficheHistorique.innerHTML = `
        <div class="text-center text-muted py-3">
          <i class="fas fa-inbox fa-2x mb-2"></i>
          <p>Aucun prêt enregistré pour ce matériel</p>
        </div>
      `;
      return;
    }
    
    // Afficher les prêts (du plus récent au plus ancien)
    let html = '<div class="list-group">';
    
    historique.reverse().forEach((pret, index) => {
      const estRestitue = pret.dateRestitution !== null;
      const badgeClass = estRestitue ? 'bg-secondary' : 'bg-primary';
      const badgeText = estRestitue ? 'Restitué' : 'En cours';
    
    html += `
      <div class="list-group-item">
        <div class="d-flex justify-content-between align-items-start mb-2">
          <div>
            <strong>${pret.emprunteur}</strong>
            <span class="badge ${badgeClass} ms-2">${badgeText}</span>
          </div>
        </div>
        
        <div class="small mb-2">
          <i class="fas fa-calendar-alt me-1"></i>
          <strong>Prêt:</strong> ${pret.datePret} 
          <i class="fas fa-arrow-right mx-2"></i>
          <strong>Retour prévu:</strong> ${pret.dateRetour}
        </div>
        
        <div class="small">
          <span class="badge badge-etat ${pret.etatPret.toLowerCase()}">${pret.etatPret}</span>
          <span class="mx-2">→</span>
          ${estRestitue 
            ? `<span class="badge badge-etat ${pret.etatRetour.toLowerCase()}">${pret.etatRetour}</span>` 
            : '<span class="text-muted">En attente de restitution</span>'}
        </div>
        
        ${estRestitue ? `
          <div class="small text-muted mt-1">
            <i class="fas fa-check-circle me-1"></i>Restitué le ${pret.dateRestitution}
          </div>
        ` : ''}
      </div>
    `;
  });
  
  html += '</div>';
  ficheHistorique.innerHTML = html;
  
  } catch (error) {
    console.error('Erreur lors de l\'affichage de l\'historique:', error);
    ficheHistorique.innerHTML = `
      <div class="alert alert-danger" role="alert">
        Erreur lors du chargement de l'historique
      </div>
    `;
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