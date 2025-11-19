<?php
  include_once(__DIR__ . '/../login_verify.php');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Modification d'un matériel - MediaStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="style-creation-pret.css"/>
    <link rel="stylesheet" href="style-index.css"/>
  </head>
  <body class="d-flex flex-column min-vh-100">
    <!-- Header -->
    <header class="position-relative">
      <div class="container h-100">
          <div class="row h-100 align-items-center justify-content-center">
          <div class="col-12 text-center">
              <div class="logo-app d-flex align-items-center justify-content-center">
              <a href="./index.php">
                  <img src="logo.png" alt="MediaStock" class="img-logo"/>
              </a>
              </div>
          </div>
          </div>
      </div>
    </header>
   
    <!-- Corps principal -->
    <main class="page-body">
        
         <!-- Bouton retour -->
        <div class="row mb-4 mt-5 text-center">
          <div class="change col-12">
            <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i></a>
          </div>
        </div>

        <div class="text-center text-muted my-4" id="itemNameReturn">Nom de l'élément</div>

        <!-- image url -->
        <div id="productVisualReturn" class="text-center mb-5">
            <div id="productImageWrapReturn"></div>
        </div>

       <!-- QR Code Card -->
        <div class="fiche-card mt-4">
            <div class="fiche-card-header">
                <i class="fas fa-qrcode me-2"></i>
                <span>QR Code du matériel</span>
            </div>
            <div class="fiche-card-body text-center">
                <div id="ficheQRCode" class="qr-container">
                <!-- QR code généré ici -->
                </div>
            </div>
        </div>

        <form id="modificationForm" class="needs-validation" novalidate>
            <div class="mb-4">
                <label class="form-label">Nom du matériel :</label>
                <input type="text" id="nomItemModif" class="form-control" placeholder="Nom de l'item" required/>
            </div>

            <div class="mb-4">
                <label class="form-label">Modèle du matériel :</label>
                <input type="text" id="modeleItemModif" class="form-control" placeholder="Modèle de l'item" required/>
            </div>

            <!-- les deux états des couleurs ne sont pas pareil!!!  -->
            <!-- <div class="mb-3">
                <label class="form-label d-block">État actuel du matériel :</label>
                <div id="etatItemDisplay" class="etat-pret-display">
                    <span id="etatPretBadge" class="badge-etat">Bon</span>
                </div>
            </div> -->

            <div class="mb-5">
                <label class="form-label d-block">État du matériel: </label>
                <div class="btn-group" role="group" aria-label="État de l'item">
                    <input type="radio" class="btn-check" name="etatModif" id="etatBonModif" value="Bon" autocomplete="off" required checked/>
                    <label class="btn btn-outline-success state-btn" for="etatBonModif">Bon</label>

                    <input type="radio" class="btn-check" name="etatModif" id="etatMoyenModif" value="Moyen" autocomplete="off"/>
                    <label class="btn btn-outline-warning state-btn" for="etatMoyenModif">Moyen</label>

                    <input type="radio" class="btn-check" name="etatModif" id="etatMauvaisModif" value="Mauvais" autocomplete="off"/>
                    <label class="btn btn-outline-danger state-btn" for="etatMauvaisModif">Mauvais</label>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Catégorie :</label>
                <!-- <input type="text" id="categorieItemModif" class="form-control" placeholder="Catégorie de l'item" required/> -->
                <select id="categorieItemModif" class="form-select">
                    <option value="informatique">Informatique</option>
                    <option value="audio">Audio</option>
                    <option value="connectique">Connectique</option>
                    <option value="autres">Autres</option>
                </select>
            </div>

            <div class="d-grid mt-5">
                <button type="submit" id="submitBtnModif" class="btn btn-primary validate-btn">
                    Valider
                </button>
            </div>
        </form>
    </main>

    <!-- Footer => basé sur la page d'acceuil, afin que ça soit pareil partout!- -->
    <footer class="py-4 mt-auto text-muted py-3 border-top">
      <div class="container">
        <div class="text-center">
          <p class="mb-0 text-dark">
            <a href="mentions-legales.html" class="text-dark text-decoration-none"> Mentions légales</a>
          </p>
          <p class="mb-0 text-dark">© 2025 MediaStock Inc</p>
        </div>
      </div>
    </footer>

    <!-- Modal de félicitation -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
          <div class="modal-body">
            <div class="success-icon mb-3"><i class="fas fa-check"></i></div>
            <h4 class="mb-3">Félicitation! Votre modification a été effectuée.</h4>
            <button type="button" class="btn btn-success" data-bs-dismiss="modal">
              Valider
            </button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script spécifique à cette page -->
    <script src="modification.js"></script>
  </body>
</html>
