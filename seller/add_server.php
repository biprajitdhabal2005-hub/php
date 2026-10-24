<?php
session_start();
include '../includes/db.php';

// Only logged-in sellers can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'seller') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $seller_id = $_SESSION['user_id'];
    $server_name = $_POST['server_name'];
    $member_count = $_POST['member_count'];
    $active_players = $_POST['active_players'];
    $price = $_POST['price'];
    $expected_price = $_POST['expected_price'];

    $stmt = $conn->prepare("INSERT INTO servers (seller_id, server_name, member_count, active_players, price, expected_price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isiidd", $seller_id, $server_name, $member_count, $active_players, $price, $expected_price);

    if ($stmt->execute()) {
        echo "Server listed successfully! Status: Pending admin approval.";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>List a Server - Ownza</title>
</head>
<body>
    <h2>List Your Discord Server</h2>
    <form method="POST" action="add_server.php">
        <label>Server Name:</label><br>
        <input type="text" name="server_name" required><br><br>

        <label>Member Count:</label><br>
        <input type="number" name="member_count" required><br><br>

        <label>Active Players:</label><br>
        <input type="number" name="active_players" required><br><br>

        <label>Price (₹):</label><br>
        <input type="number" step="0.01" name="price" required><br><br>

        <label>Expected Price (₹):</label><br>
        <input type="number" step="0.01" name="expected_price" required><br><br>

        <button type="submit">Submit Listing</button>
    </form>
</body>
</html>