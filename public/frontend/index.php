<?php
include_once(__DIR__ . '/../login_verify.php');
?>
<!DOCTYPE html>
<html lang="fr"> 

  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MediaStock - Patrimoine informatique</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="style-index.css" />
  </head>

  <body class="d-flex flex-column min-vh-100">
    <!-- Header -->
    <header class="position-relative">
      <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center">
          <div class="col-12 text-center position-relative h-100 d-flex align-items-center justify-content-center">
            <!-- Logo MediaStock qui colle aux bords -->
            <div class="logo-app position-relative justify-content-center align-items-center d-flex h-100">
              <a href="./index.php"><img src="logo.png" alt="MediaStock" class="img-logo"/></a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <div class="container py-4 flex-grow-1">
      <div class="d-grid gap-2 d-md-flex justify-content-md-center actions-row">
        <button class="btn action-btn w-100 w-md-auto" id="scanPretBtn">
          Créer un prêt
        </button>
        <button class="btn action-btn w-100 w-md-auto" id="scanRestitutionBtn">
          Restituter un prêt
        </button>
      </div>
    </div>

    <!-- Container pour le lecteur QR -->
    <div id="qr-reader-container">
      <div id="qr-reader"></div>
    </div>

    <hr class="w-100 m-0 border-0" id="hr" />

    <div class="container py-4 flex-grow-1">
      <h2 class="text-center section-title mb-3">Patrimoine informatique</h2>

      <div class="row g-2 mb-3 toolbar">
        <div class="col-12 col-md-auto">
          <button class="btn btn-primary w-100" onclick="location.href='Choixcat.php'">
            <i class="fas fa-plus me-2"></i>Ajouter un élément
          </button>
        </div>

        <div class="col-12 col-md">
          <div class="d-flex gap-2 justify-content-md-end">
            <select id="categoryFilter" class="form-select w-50">
              <option value="">Catégorie</option>
              <option value="informatique">Informatique</option>
              <option value="audio">Audio</option>
              <option value="connectique">Connectique</option>
              <option value="autres">Autres</option>
            </select>

            <select id="statusFilter" class="form-select w-50">
              <option value="">Disponibilité</option>
              <option value="disponible">Disponible</option>
              <option value="indisponible">Indisponible</option>
              <option value="retard">Retard</option>
            </select>

            <select id="etatFilter" class="form-select w-50">
              <option value="">État</option>
              <option value="bon">Bon</option>
              <option value="moyen">Moyen</option>
              <option value="mauvais">Mauvais</option>
            </select>
          </div>
        </div>
      </div>

      <div id="inventoryList" class="list-group mb-5">
        <!-- JS injecte ici -->
      </div>
    </div>

    <!-- Footer  => basé sur la page d'accueil, afin que ça soit pareil partout!-->
    <footer class="py-4">
      <div class="container">
        <div class="text-center">
          <p class="mb-0 text-dark">
            <a href="mentions-legales.html" class="text-dark text-decoration-none">Mentions légales</a>
          </p>
          <p class="mb-0 text-dark">© 2025 MediaStock Inc</p>
        </div>
      </div>
    </footer>

    <!-- Offcanvas - Fiche produit -->
    <div class="offcanvas offcanvas-end offcanvas-fiche" tabindex="-1" id="ficheProduitOffcanvas" aria-labelledby="ficheProduitLabel">
      <div class="offcanvas-header-custom">
        <button type="button" class="btn-close-custom" data-bs-dismiss="offcanvas" aria-label="Close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="offcanvas-body p-0">

        <!-- En-tête avec icône et nom -->
        <div class="fiche-header">
          <div class="fiche-icon-wrapper">
            <div id="ficheIcon" class="fiche-icon-circle">
              <i class="fas fa-box fa-3x"></i>
            </div>
          </div>
          <h3 id="ficheNom" class="fiche-title">Nom du matériel</h3>
          <div id="ficheEtat" class="fiche-badges">
            <!-- Badges injectés ici -->
          </div>
        </div>

        <!-- Corps de la fiche -->
        <div class="fiche-content">

          <!-- QR Code Card -->
          <div class="fiche-card">
            <div class="fiche-card-header">
              <i class="fas fa-qrcode me-2"></i>
              <span>QR Code du matériel</span>
            </div>
            <div class="fiche-card-body text-center">
              <div id="ficheQRCode" class="qr-container">
                <!-- QR code généré ici -->
              </div>
              <p class="text-muted small mt-2 mb-0"> 
                Scannez pour accéder aux détails
              </p>

              
              <!-- Boutons d'action QR Code -->
              <div class="d-flex justify-content-center gap-3 mt-3 d-none" id="qrcodeActionsIndex">
                <button id="btnTelechargerIndex" class="btn btn-qr d-flex align-items-center gap-2">
                  <i class="fa fa-download"></i> Télécharger
                </button>
                <button id="btnImprimerIndex" class="btn btn-qr d-flex align-items-center gap-2">
                  <i class="fa fa-print"></i> Imprimer
                </button>
              </div>
            </div>
          </div>

          <!-- Historique Card -->
          <div class="fiche-card">
            <div class="fiche-card-header">
              <i class="fas fa-history me-2"></i>
              <span>Historique des prêts</span>
            </div>
            <div class="fiche-card-body p-0">
              <div id="ficheHistorique">
                <!-- Historique injecté ici -->
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete confirmation modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-3">
          <div class="modal-body">
            <p class="mb-2">
              Etes vous bien sûr de vouloir supprimer cette élément ?
            </p>
            <div id="deleteIcon" class="d-flex align-items-center justify-content-center">
              <!-- icon injected -->
            </div>
            <p id="deleteName" class="mt-3 fw-semibold">Nom de l'élément</p>
          </div>
          <div class="modal-footer justify-content-center">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
              Annuler
            </button>
            <button id="confirmDeleteBtn" type="button" class="btn">
              Valider
            </button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
    <script src="script.js"></script>

  </body>
</html>
