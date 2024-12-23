<?php
session_start();
include 'db.php';

if (!isset($_SESSION['username']) || !isset($_GET['friend_id'])) {
    echo json_encode([]);
    exit;
}

$friend_id = intval($_GET['friend_id']);
$query_user = "SELECT id FROM users WHERE username = '" . $_SESSION['username'] . "'";
$result_user = mysqli_query($conn, $query_user);
$current_user = mysqli_fetch_assoc($result_user)['id'];

$query = "
    SELECT 
        pengirim, 
        message, 
        CASE WHEN pengirim = $current_user THEN 'me' ELSE 'friend' END AS sender 
    FROM messages 
    WHERE (pengirim = $current_user AND penerima = $friend_id)
       OR (pengirim = $friend_id AND penerima = $current_user)
    ORDER BY timestamp ASC
";

$result = mysqli_query($conn, $query);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}

echo json_encode($messages);
?>
