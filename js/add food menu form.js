document.getElementById("foodForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const foodItem = {
    name: document.getElementById("foodName").value,
    description: document.getElementById("foodDescription").value,
    price: parseFloat(document.getElementById("foodPrice").value).toFixed(2),
    category: document.getElementById("foodCategory").value,
    image: URL.createObjectURL(document.getElementById("foodImage").files[0])
  };

  // Store in localStorage
  const menuItems = JSON.parse(localStorage.getItem("menuItems")) || [];
  menuItems.push(foodItem);
  localStorage.setItem("menuItems", JSON.stringify(menuItems));

  // Redirect to menu page
  window.location.href = "menu.html";
});

function searchMenu() {
  const input = document.getElementById("searchInput").value.toLowerCase();
  const items = document.getElementsByClassName("menu-item");

  for (let i = 0; i < items.length; i++) {
    const title = items[i].querySelector(".card-title").textContent.toLowerCase();
    const desc = items[i].querySelector(".card-text").textContent.toLowerCase();

    if (title.includes(input) || desc.includes(input)) {
      items[i].style.display = "";
    } else {
      items[i].style.display = "none";
    }
  }
}

