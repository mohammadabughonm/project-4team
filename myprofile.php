<?php 
include 'config.php';
session_start();
$user_id = $_SESSION['id'];

$sql2 = "SELECT * FROM `users` WHERE id='$user_id'";
$result2 = $conn->query($sql2);
if ($result2->num_rows == 1) {
  // Get the row data
  $row = $result2->fetch_assoc();
  $username = $row["username"];
  $address=$row["address"];
  $email=$row["email"];
  $image = $row["image"];
  $mobile=$row["phone"];
}
  


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // process form data here
$username = $_POST['username'];
$email = $_POST['email'];
$address = $_POST['address'];
$mobile = $_POST['mobile'];

$sql = "UPDATE users SET username='$username', email='$email', phone='$mobile', address='$address' WHERE id='$user_id'";

if ($conn->query($sql) === TRUE) {
  // update successful
  header("Location: myprofile.php");
  exit();
} else {
  // update failed
}

}



?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile | Online Store</title>
  <link rel="stylesheet" type="text/css" href="newprofile.css">
  
</head>
<body>
  <?php 
    include 'header.php'; 
        
  ?>
  
  <main>
  <section class="user-profile">
  <div class="profile-picture">
    <img src="https://img.freepik.com/premium-vector/avatar-profile-vector-illustrations-website-social-networks-user-profile-icon_495897-224.jpg" alt="Profile Picture">
  </div>
  <div class="profile-info">
    <h2><?php echo $username;?></h2>
    <p>Email: <?php echo $email;?></p>
    <p>Address: <?php echo $address;?></p>
    <p>Mobile: <?php echo $mobile;?></p>
    <div class="edit-profile">
      <!-- <button id="edit-profile-btn">Edit Profile</button> -->
      <button id="change-profile-pic">Change Profile Picture</button>

    </div>
    <div id="edit-profile-form" class="hidden">
      <h3>Edit Profile</h3>
      <form method="post">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" value="<?php echo $username;?>"><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $email;?>"><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" value="<?php echo $address;?>"><br>

        <label for="mobile">Mobile:</label>
        <input type="text" id="mobile" name="mobile" value="<?php echo $mobile;?>"><br>

        <input type="submit" value="Save Changes">
      </form>
    </div>
  </div>
</section>

    <section class="order-history">
  <h2>Order History</h2>
  <table border=2 style="border-collapse: collapse;">
    <thead>
      <tr>
        <th>Order Number</th>
        <th>Date</th>
        <th>Items</th>
        <th>Total</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>12345</td>
        <td>May 10, 2023</td>
        <td>3 items</td>
        <td>$100.00</td>
        <td>Delivered</td>
        <td><button class="order-details-btn">View Details</button></td>
      </tr>
      
      
    </tbody>
  </table>
</section>
  </main>
  
</body>
</html>
