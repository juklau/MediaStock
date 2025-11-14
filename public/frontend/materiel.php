<?php
  include_once(__DIR__ . '/../login_verify.php');
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>MediaStock - Gestion d'inventaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="style-materiel.css"/>
  </head>

  <body class="bg-white">
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

    <!-- Main -->
    <main class="container text-center py-4 min-vh-100">

      <!-- Bouton retour -->
      <div class="row mb-4 pt-5">
        <div class="col-12">
          <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i></a>
        </div>
      </div>
      <h1 class="h4 fw-normal mt-5 mb-5">Ajouter votre matériel</h1>

      <!-- Image produit -->
      <div class="mb-3 d-flex justify-content-center align-items-center">
        <div id="icon-container" class="mb-3"></div>
      </div>

      <!-- Champ nom (centré, responsive) -->
      <form class="mb-5" id="materielForm">
        <div class="d-flex justify-content-center">
          <input type="text" id="materielNom" class="form-control text-center" placeholder="Veuillez saisir le nom du matériel" required/>
        </div>

        <div class="d-flex justify-content-center mt-4">
          <input type="text" id="modeleNom" class="form-control text-center" placeholder="Veuillez saisir le modèle du matériel"/>
        </div>
      </form>

      <!-- Bouton générer -->
      <div class="mb-5">
        <button type="button" id="btnAjouterBD" class="btn">
          Ajouter à la Base De Données
        </button>
      </div>

      <!-- Message de succès -->
      <div id="messageSucces" class="alert alert-success d-none mb-3" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        <span id="messageTexte"></span>
      </div>

      <!-- QR code -->
      <div class="mb-3" id="qrcodeContainer">
        <div id="qrcodeDisplay" class="d-flex justify-content-center">
          <div class="text-muted">
            <!-- Le QR code apparaîtra ici après l'ajout -->
          </div>
        </div>
      </div>

      <!-- Actions -->
      <!-- il  ne veut pas se mettre à jour dans le navigatuer!!!!!!! -->
      <div class="d-flex justify-content-center gap-3" id="qrcodeActions">
        <button id="btnTelecharger" class="btn d-flex align-items-center gap-2">
          <i class="fa fa-download"></i> Télécharger
        </button>
        <!-- <button id="btnPartager" class="btn d-flex align-items-center justify-content-center"><i class="fa fa-share"></i></button> -->
        <button id="btnImprimer" class="btn d-flex align-items-center justify-content-center">
          <i class="fa fa-print"></i>
        </button>
      </div>
      
      <!-- Bouton Terminer (caché par défaut, apparaît après ajout à la BDD) -->
      <div class="d-flex justify-content-center mt-4 d-none" id="btnTerminerContainer">
        <button id="btnTerminer" class="btn btn-terminer d-flex align-items-center gap-2">
          <i class="fa fa-check-circle"></i> Terminer
        </button>
      </div>
      
      <div class="my-5">
        <h6 id="text-non-imprimable">
          Si Vous ne pouvez pas imprimer le QR code, vous pouvez le retrouver
          dans la fiche produit.
        </h6>
      </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Bibliothèque QRCode.js pour générer les QR codes -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <!-- Script spécifique à cette page -->
    <!-- <script src="/materiel.js"></script> -->
    <script src="./materiel.js"></script>
  </body>
</html>
