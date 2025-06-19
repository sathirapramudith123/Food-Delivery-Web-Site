document.addEventListener("DOMContentLoaded", function () {
  const menuItems = JSON.parse(localStorage.getItem("menuItems")) || [];

  const menuContainer = document.querySelector(".row");

  menuItems.forEach(item => {
    const col = document.createElement("div");
    col.className = "col";

    col.innerHTML = `
      <div class="card h-100">
        <img src="${item.image}" class="card-img-top" alt="${item.name}">
        <div class="card-body">
          <h5 class="card-title">${item.name}</h5>
          <p class="card-text">${item.description}</p>
          <p class="text-danger fw-bold">$${item.price}</p>
          <a href="#" class="btn btn-danger w-100">Add to Cart</a>
        </div>
      </div>
    `;

    menuContainer.appendChild(col);
  });
});
