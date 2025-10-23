document.addEventListener("DOMContentLoaded", () => {
  console.log("✅ restitution.js chargé");

  const form = document.getElementById("returnForm");
  const modalEl = document.getElementById("successModalReturn");

  if (!form) {
    console.error("⚠️ Formulaire non trouvé (#returnForm)");
    return;
  }

  if (!modalEl) {
    console.error("⚠️ Modal non trouvé (#successModalReturn)");
    return;
  }

  // Empêche la soumission normale (rechargement)
  form.addEventListener("submit", (e) => {
    e.preventDefault();
    console.log("✅ Formulaire soumis");

    const modal = new bootstrap.Modal(modalEl);
    modal.show();

    // Redirection après fermeture
    modalEl.addEventListener(
      "hidden.bs.modal",
      () => {
        window.location.href = "index.html";
      },
      { once: true }
    );
  });
});
