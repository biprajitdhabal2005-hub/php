<?php
session_start();
include '../includes/db.php';

// Only admin can access this page
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle Approve/Reject action
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $status = ($_GET['action'] == 'approve') ? 'approved' : 'rejected';

    $stmt = $conn->prepare("UPDATE servers SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();

    header("Location: pending_listings.php");
    exit();
}

// Fetch all pending servers with seller name
$query = "SELECT servers.*, users.name AS seller_name 
          FROM servers 
          JOIN users ON servers.seller_id = users.id 
          WHERE servers.status = 'pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pending Listings - Admin</title>
</head>
<body>
    <h2>Pending Server Listings</h2>
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
                <a href="?action=approve&id=<?= $row['id'] ?>">✅ Approve</a> | 
                <a href="?action=reject&id=<?= $row['id'] ?>">❌ Reject</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>