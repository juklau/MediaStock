
// Navigation intelligente selon la provenance
const urlParams = new URLSearchParams(window.location.search);
const fromPage = urlParams.get('from');

if (fromPage === 'accueil') {
    // Retourner vers accueil.html si on vient de la page de connexion
    document.getElementById('backBtn').href = 'accueil.html';
    document.getElementById('footerBackBtn').href = 'accueil.html';
}