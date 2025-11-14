<?php
  include_once(__DIR__ . '/../login_verify.php');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Création de prêt - MediaStock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"/>

    <link rel="stylesheet" href="style-creation-pret.css"/>
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

    <main class="page-body">
      <div class="text-center text-muted mb-2 mt-4" id="itemName"> Nom de l'élément</div>

      <div id="productVisual" class="text-center mb-3">
        <!-- si image fournie, sera injectée; sinon icône FontAwesome -->
        <div id="productImageWrap"><i id="productIcon" class="fas fa-mouse fa-5x"></i></div>
      </div>

      <form id="loanForm" class="needs-validation mt-5" novalidate>
        <!-- <div class="mb-2">
          <label class="form-label">Nom de l'intervenant :</label>
          <input type="text" id="intervenant" class="form-control" placeholder="nom"/>
        </div> -->

        <div class="mb-2">
          <label class="form-label">Nom de l'emprunteur : <span class="text-danger">*</span></label>
          <input type="text" id="emprunteurNom" class="form-control" placeholder="Nom" required/>
          <div class="invalid-feedback">Le nom de l'emprunteur est requis</div>
        </div>

        <div class="mb-2">
          <label class="form-label">Prénom de l'emprunteur : <span class="text-danger">*</span></label>
          <input type="text" id="emprunteurPrenom" class="form-control" placeholder="Prénom" required/>
          <div class="invalid-feedback">Le prénom de l'emprunteur est requis</div>
        </div>

        <div class="mb-2">
          <!-- les noms de la formation doivent être pareils que dans la BDD!!!! -->
          <label class="form-label">Classe : <span class="text-danger">*</span></label>
          <select id="classe" class="form-select" required>
            <option value="" selected disabled>Sélectionner une classe</option>
            <option value="INTERVENANT">INTERVENANT</option>
            <option value="ECS1">ECS1</option>
            <option value="ECS2">ECS2</option>
            <option value="ECS3 A Brand Digit">ECS3 A Brand Digit</option>
            <option value="ECS3 B Com Event">ECS3 B Com Event</option>
            <option value="ECS4 A Brand Digit">ECS4 A Brand Digit</option>
            <option value="ECS4 B Com Event">ECS4 B Com Event</option>
            <option value="ECS4 DA">ECS4 DA</option>
            <option value="ECS5 Com Digit">ECS5 Com Digit</option>
            <option value="ECS5 Com Event">ECS5 Com Event</option>
            <option value="NSS 1">NSS 1</option>
            <option value="NSS 2">NSS 2</option>
            <option value="PSL 1">PSL 1</option>
            <option value="PSL 2">PSL 2</option>
            <option value="PSL 3">PSL 3</option>
            <option value="Iris 1">Iris 1</option>
            <option value="Iris 2">Iris 2</option>
          </select>

          <div class="invalid-feedback">La classe est requise</div>
        </div>

        <div class="mb-3">
          <label class="form-label d-block">État : <span class="text-danger">*</span></label> 
          <div class="btn-group" role="group" aria-label="État">
            <input type="radio" class="btn-check" name="etat" id="etatBon" value="Bon" autocomplete="off" checked required/>
            <label class="btn btn-outline-success state-btn" for="etatBon" >Bon</label>

            <input type="radio" class="btn-check" name="etat" id="etatMoyen" value="Moyen" autocomplete="off"/>
            <label class="btn btn-outline-warning state-btn" for="etatMoyen">Moyen</label>

            <input type="radio" class="btn-check" name="etat" id="etatMauvais" value="Mauvais" autocomplete="off"/>
            <label class="btn btn-outline-danger state-btn" for="etatMauvais">Mauvais</label>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Notes :</label>
          <textarea id="notes" class="form-control notes-box" maxlength="500" placeholder="Notes"></textarea>
          <div class="form-text text-end" id="notesCount">0 / 500</div>
        </div>

        <div class="mb-4">
          <label class="form-label">Période de prêt : <span class="text-danger">*</span></label>
          <div class="calendar-visual-wrap">
            <div class="calendar-container"></div>
            <input id="datePicker" class="form-control date-input-below" type="text"placeholder="Sélectionner la période" required/>
          </div>
          <div class="invalid-feedback">
            Les dates de prêt et retour sont requises
          </div>
        </div>

        <div class="d-grid mt-3">
          <button id="submitBtn" class="btn btn-primary validate-btn">
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
            <h4 class="mb-3">Félicitation! Votre prêt a été effectuée.</h4>
            <button type="button" class="btn btn-success" data-bs-dismiss="modal">
              Valider
            </button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script spécifique à cette page -->
    <script src="creation-pret.js"></script>
  </body>
</html>
