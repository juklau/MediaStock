

document.addEventListener("DOMContentLoaded", async () => {
  // Récupérer le code QR depuis l'URL
  const urlParams = new URLSearchParams(window.location.search);
  const qrCode = urlParams.get("code");

  if (!qrCode || isNaN(qrCode)) {
    console.warn("QR code invalide ou manquant :", qrCode);
    return;
  }

  try {
    const response = await fetch(`/api/getoneitem.php?id=${encodeURIComponent(qrCode)}`);
    const result = await response.json();

    if (result.success && result.data) {
      const item = result.data;

      // Injecter les données dans la page
      document.getElementById("itemName").textContent = item.nom || "Nom inconnu";

      // Afficher l’icône ou image
      const iconWrap = document.getElementById("productImageWrap");
      if (item.image_url && item.image_url.startsWith("fa-")) {
        iconWrap.innerHTML = `<i class="${item.image_url} fa-5x" style="color: #333;"></i>`;
      } else if (item.image_url) {
        iconWrap.innerHTML = `<img src="${item.image_url}" alt="${item.nom}" class="img-fluid" style="max-height: 120px;">`;
      }

      // Tu peux aussi pré-remplir des champs si besoin
      // document.getElementById("intervenant").value = ...;
    } else {
      alert("Matériel introuvable : " + result.message);
    }
  } catch (error) {
    console.error("Erreur lors de la récupération du matériel :", error);
    alert("Une erreur est survenue lors du chargement du matériel.");
  }
});