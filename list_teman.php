<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username'])) {
    echo json_encode([]);
    exit;
}

$username = $_SESSION['username'];
$query = "SELECT id, username FROM users WHERE username != '$username' AND username != 'admin'";
$result = mysqli_query($conn, $query);

$friends = [];
while ($row = mysqli_fetch_assoc($result)) {
    $friends[] = $row;
}

echo json_encode($friends);
?>
