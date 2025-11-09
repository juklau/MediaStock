// =========================
// == Authentification =====
// =========================

document.getElementById('loginForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);

    const res = await fetch('../login.php', { method: 'POST', body: formData });
    const data = await res.json();

    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value.trim();

    if (data.success) {
        Swal.fire({
            icon: 'success',
            title: data.title,
            text: data.message,
            confirmButtonColor: '#4CAF50'
        }).then(() => {
            window.location.href = '../frontend/index.php';
        });
    } else {
        Swal.fire({
            icon: 'error',
            title: data.title,
            text: data.message,
            confirmButtonColor: '#FF9994'
        });
    }

});


// ============================================================
// == Affichage le nombre du matériel de chaque catégorie =====
// ============================================================
     
document.addEventListener('DOMContentLoaded', async () => {

    //lancer une requête HTTP GET => reponse en json
    fetch('../api/getnbdispoparcat.php')

    // convertir la réponse en objet JS => p.ex.: [{ categorie: "Informatique", disponible_count: 12 },..]
    .then(response => response.json())
    .then(data => {
        const categories = { //dictionnaire => nomm : id
            "Informatique": "info-count-informatique",
            "Audio": "info-count-audio",
            "Connectique": "info-count-connectique",
            "Autres": "info-count-autres"
        };

        // parcourir le data (dictionnaire)
        for (const [nom, id] of Object.entries(categories)) {

            const cat = data.find(item => item.categorie === nom);
            
            if(cat) {

                //création un div
                const element = document.createElement('div');

                //remplirle div avec le nb d'articles disponibles
                element.textContent = `${cat.disponible_count}`;
                
                
                const target = document.getElementById(id);
                if (target) {
                    target.appendChild(element);
                };
            }
            // sinon => rien est affiché pour catégorie
        }
    })
    .catch(error => console.error('Erreur:', error));

})

