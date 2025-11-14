<?php
  include_once(__DIR__ . '/../login_verify.php');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Restitution de prêt - MediaStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>

    <link rel="stylesheet" href="style-restitution.css" />
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
      <div class="text-center text-muted mb-2 mt-4" id="itemNameReturn">Nom de l'élément</div>

      <div id="productVisualReturn" class="text-center mb-3">
        <div id="productImageWrapReturn">
          <i id="productIconReturn" class="fas fa-mouse fa-5x"></i> 
        </div>
      </div>

      <form id="returnForm" class="needs-validation mt-5" novalidate>
        <!-- <div class="mb-2">
          <label class="form-label">Nom de l'intervenant :</label>
          <input type="text" id="intervenantReturn" class="form-control" placeholder="nom" readonly/>
        </div> -->

        <div class="mb-2">
          <label class="form-label">Nom de l'emprunteur :</label>
          <input type="text" id="emprunteurNomReturn" class="form-control" placeholder="Nom" readonly/>
        </div>

        <div class="mb-2">
          <label class="form-label">Prénom de l'emprunteur :</label>
          <input type="text" id="emprunteurPrenomReturn" class="form-control" placeholder="Prénom" readonly/>
        </div>

        <div class="mb-2">
          <label class="form-label">Classe :</label>
          <input type="text" id="classeReturn" class="form-control" placeholder="Classe" readonly/>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">État au moment du prêt :</label>
          <div id="etatPretDisplay" class="etat-pret-display">
            <span id="etatPretBadge" class="badge-etat">Bon</span>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">État à la restitution : <span class="text-danger">*</span></label>
          <div class="btn-group" role="group" aria-label="État à la restitution">
            <input type="radio" class="btn-check" name="etatReturn" id="etatBonReturn" value="Bon" autocomplete="off" required checked/>
            <label class="btn btn-outline-success state-btn" for="etatBonReturn">Bon</label>

            <input type="radio" class="btn-check" name="etatReturn" id="etatMoyenReturn" value="Moyen" autocomplete="off"/>
            <label class="btn btn-outline-warning state-btn" for="etatMoyenReturn">Moyen</label>

            <input type="radio" class="btn-check" name="etatReturn" id="etatMauvaisReturn" value="Mauvais" autocomplete="off"/>
            <label class="btn btn-outline-danger state-btn" for="etatMauvaisReturn">Mauvais</label>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Notes :</label>
          <textarea id="notesReturn" class="form-control notes-box" maxlength="500" placeholder="Notes" readonly></textarea>
          <div class="form-text text-end" id="notesCountReturn">0 / 500</div>
        </div>

        <div class="mb-3">
          <label class="form-label">Commentaire :</label>
          <textarea id="commentaireReturn" class="form-control notes-box" maxlength="500" placeholder="Ajouter un commentaire de retour..."></textarea>
          <div class="form-text text-end" id="commentaireCountReturn">
            0 / 500
          </div>
        </div>

        <div class="mb-4">
          <label class="form-label">Date de retour prévue :</label>
          <div class="calendar-visual-wrap">
            <div class="calendar-container-return"></div>
            <input id="datePickerReturn" class="form-control date-input-below" type="text" placeholder="Date de retour" readonly/>
          </div>
        </div>

        <!-- bouton corrigé -->
        <div class="d-grid mt-3">
          <button type="submit" id="submitBtnReturn" class="btn btn-primary validate-btn">
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
            <a href="mentions-legales.html" class="text-dark text-decoration-none">Mentions légales</a>
          </p>
          <p class="mb-0 text-dark">© 2025 MediaStock Inc</p>
        </div>
      </div>
    </footer>

    <!-- Modal de confirmation -->
    <div class="modal fade" id="successModalReturn" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center p-4">
          <div class="modal-body">
            <div class="success-icon mb-3">
              <i class="fas fa-check fa-3x text"></i>
            </div>
            <h4 class="mb-3">Félicitations! La restitution a été effectuée.</h4>
            <button type="button" class="btn btn-success" data-bs-dismiss="modal">
              Valider
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Librairies -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script spécifique à cette page -->
    <script src="restitution.js"></script>
  </body>
</html>
