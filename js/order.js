let orders = JSON.parse(localStorage.getItem("orders")) || [];

function saveOrders() {
  localStorage.setItem("orders", JSON.stringify(orders));
}

function renderOrders() {
  const tbody = document.getElementById("orderTableBody");
  tbody.innerHTML = "";

  orders.forEach((order, index) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${index + 1}</td>
      <td>${order.customer}</td>
      <td>${order.item}</td>
      <td>${order.quantity}</td>
      <td>${order.status}</td>
      <td>
        <button class="btn btn-sm btn-warning me-2" onclick="editOrder(${index})">Edit</button>
        <button class="btn btn-sm btn-danger" onclick="deleteOrder(${index})">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function addOrder(customer, item, quantity, status) {
  orders.push({ customer, item, quantity, status });
  saveOrders();
  renderOrders();
}

function deleteOrder(index) {
  if (confirm("Delete this order?")) {
    orders.splice(index, 1);
    saveOrders();
    renderOrders();
  }
}

function editOrder(index) {
  const order = orders[index];

  const customer = prompt("Edit customer name:", order.customer);
  const item = prompt("Edit item:", order.item);
  const quantity = parseInt(prompt("Edit quantity:", order.quantity));
  const status = prompt("Edit status (Pending / Preparing / Delivered):", order.status);

  if (customer && item && quantity && status) {
    orders[index] = { customer, item, quantity, status };
    saveOrders();
    renderOrders();
  }
}

// Form handler
document.getElementById("orderForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const customer = document.getElementById("customerName").value.trim();
  const item = document.getElementById("orderItem").value.trim();
  const quantity = parseInt(document.getElementById("orderQuantity").value);
  const status = document.getElementById("orderStatus").value;

  if (customer && item && quantity && status) {
    addOrder(customer, item, quantity, status);
    this.reset();
  }
});

renderOrders();
