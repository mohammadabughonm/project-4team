<?php
include "db_conn.php";
$id = $_GET["id"];

if (isset($_POST["submit"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $image = $_FILES['image'];
    // off_of_day

    $sql = "UPDATE `users` SET `username`='$username',`password`='$password',`email`='$email',`image`='$image' WHERE  id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header("Location: http://localhost/briefTry/AdminDashboard-main/index.php?view_user");

    } else {
        echo "Failed: " . mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <title> CRUD Application</title>
</head>

<body>
    <div class="container">
        <div class="text-center mb-4">
            <h3>Edit User Information</h3>
            <p class="text-muted">Click update after changing any information</p>
        </div>

        <?php
        $sql = "SELECT * FROM `users` WHERE id = $id";
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        ?>

        <div class="container d-flex justify-content-center">
            <form method="post" style="width:50vw; min-width:300px;">
                <div class="row mb-3">

                    <div class="col">
                        <label class="form-label">Name:
                            <input type="text" class="form-control" name="username" value="<?php echo $row['username'] ?>">
                        </label>
                    </div><br>
                    <div class="col">
                        <label class="form-label">password:
                            <input type="number" class="form-control" name="password" value="<?php echo $row['password'] ?>">
                        </label>
                    </div><br>
                    <div class="col">
                        <label class="form-label">email:
                            <input type="text" class="form-control" name="email" value="<?php echo $row['email'] ?>">
                        </label>
                    </div><br>
                    <div class="col">
                        <label class="form-label">image:
                            <input type="file" class="form-control" name="image">
                        </label>
                    </div><br>
                </div>


                <div>
                    <button type="submit" class="btn btn-success" name="submit">Update</button>
                    <a href="landing.php" class="btn btn-danger">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>

</body>

</html>