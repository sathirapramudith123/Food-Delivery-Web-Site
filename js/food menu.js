let menu = JSON.parse(localStorage.getItem("menu")) || [];

function saveMenu() {
  localStorage.setItem("menu", JSON.stringify(menu));
}

function renderMenu(filter = "") {
  const menuList = document.getElementById("menuList");
  menuList.innerHTML = "";

  const filteredMenu = menu.filter(item =>
    item.name.toLowerCase().includes(filter.toLowerCase())
  );

  filteredMenu.forEach((item, index) => {
    const col = document.createElement("div");
    col.className = "col";
    col.innerHTML = `
      <div class="card h-100">
        <img src="${item.image || 'https://via.placeholder.com/300x180'}" class="card-img-top" alt="${item.name}">
        <div class="card-body">
          <h5>${item.name}</h5>
          <p>${item.description}</p>
          <p class="text-danger fw-bold">$${item.price.toFixed(2)}</p>
          <button class="btn btn-sm btn-primary me-2" onclick="editFood(${index})">Edit</button>
          <button class="btn btn-sm btn-danger" onclick="deleteFood(${index})">Delete</button>
        </div>
      </div>
    `;
    menuList.appendChild(col);
  });
}

function addOrUpdateFood(e) {
  e.preventDefault();

  const index = document.getElementById("foodIndex").value;
  const name = document.getElementById("foodName").value;
  const description = document.getElementById("foodDesc").value;
  const price = parseFloat(document.getElementById("foodPrice").value);
  const image = document.getElementById("foodImage").value;

  if (index === "") {
    // Create
    menu.push({ name, description, price, image });
  } else {
    // Update
    menu[index] = { name, description, price, image };
    document.getElementById("submitBtn").textContent = "Add Food";
  }

  saveMenu();
  renderMenu();
  document.getElementById("foodForm").reset();
  document.getElementById("foodIndex").value = "";
}

function editFood(index) {
  const item = menu[index];
  document.getElementById("foodName").value = item.name;
  document.getElementById("foodDesc").value = item.description;
  document.getElementById("foodPrice").value = item.price;
  document.getElementById("foodImage").value = item.image;
  document.getElementById("foodIndex").value = index;
  document.getElementById("submitBtn").textContent = "Update Food";
}

function deleteFood(index) {
  if (confirm("Are you sure you want to delete this item?")) {
    menu.splice(index, 1);
    saveMenu();
    renderMenu();
  }
}

document.getElementById("foodForm").addEventListener("submit", addOrUpdateFood);

document.getElementById("searchInput").addEventListener("input", function () {
  renderMenu(this.value);
});

// Initial render
renderMenu();
