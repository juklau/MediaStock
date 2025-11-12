<?php
  include_once(__DIR__ . '/../login_verify.php');
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediaStock - Gestion d'inventaire</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>

    <link rel="stylesheet" href="style-choixcat.css" />
  </head>

  <!-- peut être à enlever : min-vh-100?? -->
  <body class="bg-light d-flex flex-column min-vh-100">
    <!-- Header -->
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

    <!-- Main Content -->
    <main class="py-5">
      <div class="container">

        <!-- Bouton retour -->
        <div class="row mb-4 text-center">
          <div class="col-12">
            <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i></a>
          </div>
        </div>

        <!-- Choixcat Section -->
        <div class="row">
          <div class="col-12">
            <h2 class="text-center my-5 text-decoration-underline" id="item-dispo">
              Matériel disponible :
            </h2>

            <div class="row g-4 mt-3">
              <!-- Périphérique informatique -->
              <div class="col-6 col-md-6 col-lg-6">
                <a href="materiel.php" class="text-decoration-none" onclick="localStorage.setItem('selectedCategory', 'Informatique')">
                  <div class="card h-100 shadow-sm border-3 rounded-4 equipment-card">
                    <div class="card-body text-center p-4">
                      <div class="mb-3"><i class="fas fa-desktop text-dark"></i></div>
                      <h5 class="card-title text-dark mb-2 items-Choixcat">
                        Périphérique informatique
                      </h5>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Périphérique audio -->
              <div class="col-6 col-md-6 col-lg-6">
                <a href="materiel.php" class="text-decoration-none" onclick="localStorage.setItem('selectedCategory', 'Audio')">
                  <div class="card h-100 shadow-sm border-3 rounded-4 equipment-card">
                    <div class="card-body text-center p-4">
                      <div class="mb-3"><i class="fas fa-volume-high text-dark"></i></div>
                      <h5 class="card-title text-dark mb-2 items-Choixcat">
                        Périphérique audio
                      </h5>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Connectiques -->
              <div class="col-6 col-md-6 col-lg-6">
                <a href="materiel.php" class="text-decoration-none" onclick="localStorage.setItem('selectedCategory', 'Connectique')">
                  <div class="card h-100 shadow-sm border-3 rounded-4 equipment-card">
                    <div class="card-body text-center p-4">
                      <div class="mb-3"><i class="fas fa-plug text-dark"></i></div>
                      <h5 class="card-title text-dark mb-2 items-Choixcat">
                        Connectique
                      </h5>
                    </div>
                  </div>
                </a>
              </div>

              <!-- Autres -->
              <div class="col-6 col-md-6 col-lg-6">
                <a href="materiel.php" class="text-decoration-none" onclick="localStorage.setItem('selectedCategory', 'Autres')">
                  <div class="card h-100 shadow-sm border-3 rounded-4 equipment-card">
                    <div class="card-body text-center p-4">
                      <div class="mb-3"><i class="fas fa-server text-dark"></i></div>
                      <h5 class="card-title text-dark mb-2 items-Choixcat">
                        Autres
                      </h5>
                    </div>
                  </div>
                </a>
              </div>
            </div>
          </div>
        </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script spécifique à cette page -->
    <script src="materiel.js"></script>
  </body>
</html>
