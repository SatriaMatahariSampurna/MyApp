<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $password = $_POST['password'];
    $role = 'user';

    if (empty($name) || empty($username) || empty($email) || empty($alamat) || empty($password)) {
        $message = "Semua field harus diisi!";
    } else {
        $query = "INSERT INTO users (name, username, email, alamat, password, role) VALUES ('$name', '$username', '$email', '$alamat', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            header("Location: index.php");
            exit();
        } else {
            $message = "Registrasi gagal: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style_register.css">
    <title>Registrasi</title>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Create Account</h1>
            <p>Silahkan Isi From Dibawah Ini.</p>
            
            <?php if (isset($message)) { echo "<p class='alert'>$message</p>"; } ?>


            <form method="POST">
                <input type="text" name="name" id="name" placeholder="Full Name" required>
                <input type="text" name="username" id="username" placeholder="Username" required>
                <input type="email" name="email" id="email" placeholder="Email Address" required>
                <textarea name="alamat" id="alamat" placeholder="Address" required></textarea>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <button type="submit" class="btn-submit">Create Account</button>
            </form>
            <p>Already have an account? <a href="index.php">Log in here</a></p>
        </div>
    </div>
</body>
</html>
