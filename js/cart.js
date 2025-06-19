let cart = JSON.parse(localStorage.getItem("cart")) || [];

function saveCart() {
  localStorage.setItem("cart", JSON.stringify(cart));
}

function renderCart() {
  const cartList = document.getElementById("cartList");
  const cartTotal = document.getElementById("cartTotal");
  cartList.innerHTML = "";
  let total = 0;

  cart.forEach((item, index) => {
    const itemTotal = item.price * item.quantity;
    total += itemTotal;

    const col = document.createElement("div");
    col.className = "col";

    col.innerHTML = `
      <div class="cart-item">
        <h5>${item.name}</h5>
        <p>Price: $${item.price.toFixed(2)}</p>
        <p>
          Quantity: 
          <input type="number" min="1" class="form-control form-control-sm d-inline-block w-25" value="${item.quantity}" onchange="updateItem(${index}, this.value)">
        </p>
        <p>Total: $${itemTotal.toFixed(2)}</p>
        <button class="btn btn-sm btn-danger" onclick="deleteItem(${index})">Delete</button>
      </div>
    `;

    cartList.appendChild(col);
  });

  cartTotal.textContent = `$${total.toFixed(2)}`;
}

function addItem(name, price, quantity) {
  cart.push({ name, price, quantity });
  saveCart();
  renderCart();
}

function deleteItem(index) {
  if (confirm("Are you sure you want to delete this item?")) {
    cart.splice(index, 1);
    saveCart();
    renderCart();
  }
}

function updateItem(index, quantity) {
  cart[index].quantity = parseInt(quantity) || 1;
  saveCart();
  renderCart();
}

// Handle form
document.getElementById("addForm").addEventListener("submit", function (e) {
  e.preventDefault();
  const name = document.getElementById("itemName").value.trim();
  const price = parseFloat(document.getElementById("itemPrice").value);
  const quantity = parseInt(document.getElementById("itemQuantity").value);

  if (name && price && quantity) {
    addItem(name, price, quantity);
    this.reset();
  }
});

renderCart();
