// Simulated form handling
document.addEventListener("DOMContentLoaded", () => {
  const regForm = document.getElementById("registerForm");

  if (regForm) {
    regForm.addEventListener("submit", (e) => {
      e.preventDefault();
      alert("Registered successfully!");
      regForm.reset();
    });
  }


});
