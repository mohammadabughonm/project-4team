<?php
// // Connect to the database
$con = mysqli_connect("localhost", "root", "", "brief4");


session_start(); // start the session
if(!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array(); // create an empty cart
}
function addToCart($itemId, $quantity) {
    global $con;
    // Get the product information from the database
    $query = "SELECT * FROM `products` WHERE id=$itemId";
    $result = mysqli_query($con, $query);
    $product = mysqli_fetch_assoc($result);

    // Add the product to the cart with the specified quantity
    if (isset($_SESSION['cart'][$itemId])) {
        $_SESSION['cart'][$itemId]['quantity'] += $quantity; // add quantity to existing item in cart
    } else {
        $_SESSION['cart'][$itemId] = array(
            'user_id' => $_SESSION['product_id'],
            'product_id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'image' => $product['image']
        ); // add new item to cart
    }

    // Insert the product into the cart table
    $user_id = $_SESSION['product_id'];
    $product_id = $product['id'];
    $name = $product['name'];
    $price = $product['price'];
    $image = $product['image'];
    
    $query = "INSERT INTO `cart_items` (`user_id`, `product_id`, `name`, `price`, `quantity`, `image`) VALUES ('$user_id', '$product_id', '$name', '$price', '$quantity', '$image')";
    mysqli_query($con, $query);

    // Produce an alert message
    echo "<script>alert('Product is added to cart.')</script>";
}

if(isset($_POST['itemId']) && isset($_POST['quantity'])) {
    addToCart($_POST['itemId'], $_POST['quantity']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="productsPage.css">
    <title>Document</title>
</head>
<body>
    <?php
    include 'header.php';
    // Get search input
    $search_input = "";
    if (isset($_GET['search'])) {
        $search_input = mysqli_real_escape_string($con, $_GET['search']);
    }

    // Get filter values
    $min_price = 100;
    $max_price = 900;
    $category_ids = array();
    if (isset($_GET['filter'])) {
        $min_price = mysqli_real_escape_string($con, $_GET['min_price']);
        $max_price = mysqli_real_escape_string($con, $_GET['max_price']);
        if (isset($_GET['category_ids'])) {
            $category_ids = $_GET['category_ids'];
            // rest of the code
        } else {
            // handle the case when the key is not set
        }
    }

    // Prepare SQL statement with search and filter conditions
    $sql = "SELECT * FROM products WHERE name LIKE '%$search_input%'";
    if (!empty($category_ids)) {
        $category_ids_str = implode(",", $category_ids);
        $sql .= " AND category_id IN ($category_ids_str)";
    }
    $sql .= " AND price >= $min_price AND price <= $max_price";
    $result = mysqli_query($con, $sql);

    // Get all categories
    $brand_query = "SELECT * FROM categories";
    $brand_query_run = mysqli_query($con, $brand_query);

    // Add price filter to query if present
    $where = "";
    $price_filter = false;
    if (isset($_GET['start_price']) && isset($_GET['end_price'])) {
        $start_price = $_GET['start_price'];
        $end_price = $_GET['end_price'];
        if ($start_price != "" && $end_price != "") {
            $where .= " WHERE price BETWEEN $start_price AND $end_price";
            $price_filter = true;
        }
    }

    // Add name filter to query if present
    if (isset($_GET['name'])) {
        $name = $_GET['name'];
        if (count($name) > 0) {
            $name_array = [];
            foreach ($name as $n) {
                $name_array[] = "category_id = $n";
            }
            $name_str = implode(" OR ", $name_array);
            if ($price_filter) {
                $where .= " AND ($name_str)";
            } else {
                $where .= " WHERE ($name_str)";
            }
        }
    }

    // Get products based on filters
    $query = "SELECT * FROM products $where";
    $result = mysqli_query($con, $query);

    ?>

    <form action="" method="GET">
        <div class="line1">
            <div class="col-md-2 m-3">
                <label for="">Start Price</label>
                <input type="text" name="start_price" value="<?php if (isset($_GET['start_price'])) {
                    echo $_GET['start_price'];
                } else {
                    echo "0";
                } ?>" class="form-control">
            </div>
            <div class="col-md-2 m-3">
                <label for="">End Price</label>
                <input type="text" name="end_price" value="<?php if (isset($_GET['end_price'])) {
                    echo $_GET['end_price'];
                } else {
                    echo "900";
                } ?>" class="form-control">
            </div>
            <div class="col-md-3 m-3">
                <label for="">categories</label><br>
                <?php
                if (mysqli_num_rows($brand_query_run) > 0) {
                    foreach ($brand_query_run as $brandlist) {
                        $checked = [];
                        if (isset($_GET['name'])) {
                            $checked = $_GET['name'];
                        }
                        ?>
                        <div id="cat">
                            <input type="checkbox" name="name[]" value="<?= $brandlist['id']; ?>" <?php if (in_array($brandlist['id'], $checked)) {
                                  echo "checked";
                              } ?> />
                            <?= $brandlist['name']; ?>
                        </div>
                        <?php
                    }
                } else { 
                    echo "No categories found";
                }
                ?>
            </div>
            <div class="col-md-2 mt-3">
            <button type="submit" class="click">Filter</button>
            </div>
        </div>
                            

    </form>

    <div  class="row gap-3 m-3">
        <?php if (mysqli_num_rows($result) > 0) {
            foreach ($result as $items) { ?>
                <div class="col-md-2 gap-3">

                    <div class="card text-center p-1 m-2 cardhw w-100" style="max-width: 220px;">
                        <!-- <img src="<?= $items['image'] ?>" class="imgcard" alt="<?= $items['name'] ?>">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= $items['name'] ?>
                            </h5>
                            <p class="card-text">
                                <?= $items['price'] ?> JD
                            </p>
                            <a class="click"  class="btn btn-primary" id="add-to-cart">ADD cart</a>
                        </div> -->
                        <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                        <img src="<?= $items['image'] ?>" class="imgcard" alt="<?= $items['name'] ?>" width="200px" height="200px">
                        <h5 class="card-title">  <?= $items['name'] ?>   </h5>
                        <input type="hidden" name="itemId" value="<?= $items['id'] ?>">
                        <label for="quantity">Quantity:</label>
                        <input type="number" name="quantity" value="1">
                        <p class="card-text">
                                <?= $items['price'] ?> JD
                            </p>

                        <button type="submit">Add to Cart</button>
                        <br>
                        <a class="click"  class="btn btn-primary" id="add-to-cart" href="comments.php">more info</a>

                        </form>

                    </div>
                </div>
            <?php }
        } else {
            echo "No items found.";
        } ?>
    </div>
    <div id="mysearch">
    <form method="GET" class="p-2" >
        <div id="search1">
            <label for="searchInput" class="form-label">Search by name:</label>
            <input type="text" class="form-control form-control-sm" id="searchInput" name="search"
                placeholder="Enter item name" style="width: 300px; height: 30px;">
            <button type="submit" class="click">Search</button>
        </div>
        
    </form>

    <?php
  

    // Check if search query is submitted
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        // Search for items that match the name
        $result = mysqli_query($con, "SELECT * FROM products WHERE name LIKE '%$search%'");
    } else {
        // If no search query, display all items
        // $result = mysqli_query($con, "SELECT * FROM products");
    }
    ?>


    <div class="carousel-inner p-3" style="width: 300px; height: 400px; ">
        <?php $i = 0;
        foreach ($result as $item) { ?>
            <div id="rsearch" class="carousel-item <?= ($i == 0) ? 'active' : '' ?>" data-bs-interval="3000">
                <img src="<?= $item['image'] ?>" class="d-block w-100" alt="<?= $item['name'] ?>">
                <!-- <div class="carousel-caption d-none d-md-block"> -->
                    <h5>
                        <?= $item['name'] ?>
                    </h5>
                <!-- </div> -->
            </div>
            <?php $i++;
        } ?>
    </div>





    <?php if (mysqli_num_rows($result) == 0) {
        echo "No items found.";
    } ?>
    </div>

    <?php  include 'footer.php' ;?>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add an event listener to the "Add to Cart" buttons
// document.querySelectorAll('.add-to-cart').forEach(button => {
// button.addEventListener('click', event => {
    const addToCartBtn = document.getElementById("add-to-cart");
  addToCartBtn.addEventListener("click", function() {
// Get the product ID from the data-id attribute
const productId = button.dataset.id;
alert("Item added to cart!");

// Send an AJAX request to update the cart quantity for this product
fetch('update-cartpage2.php', {
  method: 'POST',
  body: JSON.stringify({ productId }),
  headers: {
    'Content-Type': 'application/json'
  }
})
.then(response => response.json())
.then(data => {
  // Update the UI to show the updated cart quantity
  button.innerText = `Added (${data.cartQty})`;
})
.catch(error => console.error(error));
});


    </script>
</body>

</html>