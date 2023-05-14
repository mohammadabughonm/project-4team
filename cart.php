<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
table {
  border-collapse: collapse;
  width: 100%;
  max-width: 800px;
  margin: 0 auto;
}

th,
td {
  padding: 12px;
  text-align: left;
}

th {
  background-color: #ddd;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

input[type="number"] {
  width: 70px;
  padding: 6px 12px;
  font-size: 16px;
  border-radius: 4px;
  border: 1px solid #ccc;
}

input[type="submit"] {
  background-color: #4CAF50;
  color: white;
  padding: 8px 16px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color: #3e8e41;
}

</style>

  <title>Document</title>
</head>
<body>


<form method="post" action="process_payment.php">
  <label for="name">Name:</label>
  <input type="text" id="name" name="name" required>

  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required>

  <label for="address">Shipping Address:</label>
  <textarea id="address" name="address" required></textarea>

  <label for="card_number">Credit Card Number:</label>
  <input type="text" id="card_number" name="card_number" required>

  <label for="expiry_month">Expiry Month:</label>
  <select name="expiry_month" id="expiry_month" required>
    <option value="">--Select Month--</option>
    <option value="01">January</option>
    <option value="02">February</option>
    <option value="03">March</option>
    <option value="04">April</option>
    <option value="05">May</option>
    <option value="06">June</option>
    <option value="07">July</option>
    <option value="08">August</option>
    <option value="09">September</option>
    <option value="10">October</option>
    <option value="11">November</option>
    <option value="12">December</option>
  </select>
  <br>
  <label for="expiry_year">Expiry Year:</label>
  <select name="expiry_year" id="expiry_year" required>
    <option value="">--Select Year--</option>
    <?php
      $start_year = date('Y');
      $end_year = $start_year + 10;
      for ($i = $start_year; $i <= $end_year; $i++) {
        echo '<option value="' . substr($i, 2) . '">' . $i . '</option>';
      }
    ?>
  </select>
  <br>
  <label for="cvv">CVV:</label>
  <input type="text" id="cvv" name="cvv" required>

  <input type="submit" name="submit_payment" value="Submit Payment">
</form>

</body>
</html>
<?php
session_start(); // Start the session to access cart data stored in session

// Include necessary files (e.g. database connection, cart functions, etc.)
require_once('config.php');
// require_once('cart_functions.php');
function save_cart($cart) {
    $_SESSION['cart'] = $cart;
}

function get_product($product_id) {
    global $conn;
    $sql = "SELECT * FROM products WHERE id = $product_id";
    $result = mysqli_query($conn, $sql);
    $product = mysqli_fetch_assoc($result);
    return $product;
}



// Check if form has been submitted (e.g. to update quantity or remove item)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (isset($_POST['update_cart'])) {
    // Loop through each item in the cart and update the quantity based on the form input
    foreach ($_SESSION['cart'] as $product_id => &$item) {
      $item['quantity'] = $_POST['quantity'][$product_id];
    }
    // Save the updated cart to the session
    save_cart($product_id);
  } elseif (isset($_POST['remove_item'])) {
    // Remove the item from the cart based on the form input
    $product_id = $_POST['product_id'];
    if (is_array($_SESSION['cart']) && isset($_SESSION['cart'][$product_id])) {
      unset($_SESSION['cart'][$product_id]);
  }
      // Save the updated cart to the session
    save_cart($product_id);
  }
}

// Display the contents of the cart
echo '<table>';
echo '<tr><th>Product</th><th>Price</th><th>Quantity</th><th>Total</th><th>Actions</th></tr>';
if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {

  if(is_array($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $product_id => $item) {  
  $product = get_product($product_id); // Get product data from database or other source
  $price = $product['price'];
  $quantity = $item['quantity'];
  $total = $price * $quantity;
  echo '<tr>';
  echo '<td>' . $product['name'] . '</td>';
  echo '<td>$' . number_format($price, 2) . '</td>';
  echo '<td><form method="post"><input type="number" name="quantity[' . $product_id . ']" value="' . $quantity . '"><input type="hidden" name="product_id" value="' . $product_id . '"><input type="submit" name="update_cart" value="Update"></form></td>';
  echo '<td>$' . number_format($total, 2) . '</td>';
  echo '<td><form method="post"><input type="hidden" name="product_id" value="' . $product_id . '"><input type="submit" name="remove_item" value="Remove"></form></td>';
  echo '</tr>';
}
}else{echo '<td colspan=5>' . 'your cart is empty :(' . '</td>';}
} else {
  // code to handle an empty cart
  echo '<td>' . 'your cart is empty' . '</td>';

}
echo '</table>';
if (isset($_POST['remove_item'])) {
  // Remove the item from the cart based on the form input
  $product_id = $_POST['product_id'];
  unset($_SESSION['cart'][$product_id]);
  // Save the updated cart to the session
  save_cart($_SESSION['cart']);
  // Redirect the user back to the shopping cart page
  header('Location: cart.php');
  exit;
}

function get_cart_total() {
  $total = 0;
  if(isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
      foreach($_SESSION['cart'] as $product_id => $quantity) {
          $product = get_product($product_id);
          if($product) {
              $price = $product['price'];
              if(is_array($quantity)) {
                  // Extract the correct value from the array
                  $quantity = $quantity['quantity'];
              }
              $total += $price * $quantity;
          }
      }
  }
  return $total;
}


//Display the total cost of the cart
$total_cost = get_cart_total();
echo '<p>Total: $' . number_format($total_cost, 2) . '</p>';

?>


