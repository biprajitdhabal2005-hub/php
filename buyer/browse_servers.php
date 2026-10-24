<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$query = "SELECT servers.*, users.name AS seller_name 
          FROM servers 
          JOIN users ON servers.seller_id = users.id 
          WHERE servers.status = 'approved'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Servers - Ownza</title>
</head>
<body>
    <h2>Available Discord Servers</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Server Name</th>
            <th>Seller</th>
            <th>Members</th>
            <th>Active Players</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['server_name']) ?></td>
            <td><?= htmlspecialchars($row['seller_name']) ?></td>
            <td><?= $row['member_count'] ?></td>
            <td><?= $row['active_players'] ?></td>
            <td>₹<?= $row['price'] ?></td>
            <td>
                <a href="create_order.php?server_id=<?= $row['id'] ?>">🛒 Buy Now</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>