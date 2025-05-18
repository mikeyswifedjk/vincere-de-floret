<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connection.php';
include ('admin-nav.php');

$cashierName = $_SESSION['admin_user'] ?? 'Admin';
$products = mysqli_query($conn, "SELECT * FROM product WHERE status = 'Available'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="../assets/logo/logo2.png"/>
  <title>POS System</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
  <link rel="stylesheet" href="../css/pos.css">
</head>
<body>

<div class="main-container">
  <div class="content-wrapper">
    <div class="all">
      <h2 class="page-title">Point Of Sales System</h2>

      <div class="pos-grid">
        <!-- Products Section -->
        <section class="products-section">
          <h3>Available Products</h3>
          <div class="products">
            <?php while($row = mysqli_fetch_assoc($products)): ?>
              <div class="product-card">
                <img src="../img/<?= $row['image'] ?>" alt="<?= $row['name'] ?>">
                <h4><?= $row['name'] ?></h4>
                <p>₱<?= number_format($row['price'], 2) ?></p>
                <button onclick="addToCart(<?= $row['id'] ?>, '<?= $row['name'] ?>', <?= $row['price'] ?>)">Add</button>
              </div>
            <?php endwhile; ?>
          </div>
        </section>

        <!-- Cart & Payment Section -->
        <aside class="cart-section">
          <h3>Your Cart</h3>
          <form method="POST" action="process-pos.php" onsubmit="return validateCash()">
            <div class="cart" id="cart"></div>

            <div class="cart-summary">
              <p><strong>Total Amount: ₱<span id="totalAmount">0.00</span></strong></p>

              <label for="payment_method">Payment Method:</label>
              <select name="payment_method" id="payment_method" onchange="toggleCashField()" required>
                <option value="Cash">Cash</option>
                <option value="Gcash">Gcash</option>
                <option value="BDO">BDO</option>
              </select>

              <div id="cashInput">
                <label>Amount Tendered: ₱</label>
                <input type="number" id="amountPaid" name="amount_paid" step="0.01" min="0" oninput="computeChange()" required>
                <p><strong>Change: ₱<span id="changeAmount">0.00</span></strong></p>
              </div>

              <input type="hidden" name="cart_data" id="cart_data">
              <input type="hidden" name="cashier_name" value="<?= $cashierName ?>">

              <div class="action-buttons">
                <button class="delete-button" type="button" onclick="removeSelected()">Remove Selected</button>
                <button class="submit-btn" type="submit">Confirm & Pay</button>
              </div>
            </div>
          </form>
        </aside>
      </div>
    </div>
  </div>
</div>



<script>
let cart = [];

function addToCart(id, name, price) {
  const item = cart.find(p => p.id === id);
  if (item) {
    item.qty++;
  } else {
    cart.push({ id, name, price, qty: 1, selected: false });
  }
  renderCart();
}

function updateQty(index, value) {
  const qty = parseInt(value);
  if (qty > 0) {
    cart[index].qty = qty;
    renderCart();
  }
}

function toggleSelect(index, checked) {
  cart[index].selected = checked;
}

function removeSelected() {
  cart = cart.filter(item => !item.selected);
  renderCart();
}

function renderCart() {
  const cartBox = document.getElementById('cart');
  cartBox.innerHTML = '';
  const cartData = [];
  let totalAmount = 0;

  cart.forEach((item, i) => {
    const total = item.price * item.qty;
    totalAmount += total;
    cartBox.innerHTML += `
      <div class="cart-item">
        <input type="checkbox" onchange="toggleSelect(${i}, this.checked)">
        <h4>${item.name}</h4>
        <p>₱${item.price} x 
        <input type="number" class="qty-input" value="${item.qty}" min="1" onchange="updateQty(${i}, this.value)">
        </p>
        <p>Total: ₱${total.toFixed(2)}</p>
      </div>`;
    cartData.push(item);
  });

  document.getElementById('cart_data').value = JSON.stringify(cartData);
  document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
  computeChange();
}

function toggleCashField() {
  const method = document.getElementById('payment_method').value;
  const cashInput = document.getElementById('cashInput');
  if (method === 'Cash') {
    cashInput.style.display = 'block';
    document.getElementById('amountPaid').required = true;
  } else {
    cashInput.style.display = 'none';
    document.getElementById('amountPaid').required = false;
    document.getElementById('changeAmount').textContent = '0.00';
  }
}

function computeChange() {
  const amountPaid = parseFloat(document.getElementById('amountPaid').value) || 0;
  const total = parseFloat(document.getElementById('totalAmount').textContent);
  const change = amountPaid - total;
  document.getElementById('changeAmount').textContent = (change > 0 ? change : 0).toFixed(2);
}

function validateCash() {
  const method = document.getElementById('payment_method').value;
  if (method === 'Cash') {
    const paid = parseFloat(document.getElementById('amountPaid').value) || 0;
    const total = parseFloat(document.getElementById('totalAmount').textContent);
    if (paid < total) {
      alert('❌ Insufficient cash payment.');
      return false;
    }
  }
  return true;
}
</script>

</body>
</html>
