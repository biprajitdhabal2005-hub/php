<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle meeting scheduling
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $meeting_date = $_POST['meeting_date'];
    $meeting_time = $_POST['meeting_time'];
    $admin_id = $_SESSION['user_id'];

    // Insert into meetings table
    $stmt = $conn->prepare("INSERT INTO meetings (order_id, meeting_date, meeting_time, admin_assigned) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("issi", $order_id, $meeting_date, $meeting_time, $admin_id);
    $stmt->execute();

    // Update order status
    $stmt2 = $conn->prepare("UPDATE orders SET status = 'meeting_scheduled' WHERE id = ?");
    $stmt2->bind_param("i", $order_id);
    $stmt2->execute();

    header("Location: manage_orders.php");
    exit();
}

// Fetch all pending orders with buyer + server + seller info
$query = "SELECT orders.id AS order_id, orders.status, 
                 servers.server_name, servers.price,
                 buyer.name AS buyer_name, seller.name AS seller_name
          FROM orders
          JOIN servers ON orders.server_id = servers.id
          JOIN users AS buyer ON orders.buyer_id = buyer.id
          JOIN users AS seller ON servers.seller_id = seller.id
          WHERE orders.status = 'pending'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - Admin</title>
</head>
<body>
    <h2>Pending Orders — Schedule Meeting</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Order ID</th>
            <th>Server</th>
            <th>Buyer</th>
            <th>Seller</th>
            <th>Price</th>
            <th>Schedule Meeting</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['server_name']) ?></td>
            <td><?= htmlspecialchars($row['buyer_name']) ?></td>
            <td><?= htmlspecialchars($row['seller_name']) ?></td>
            <td>₹<?= $row['price'] ?></td>
            <td>
                <form method="POST" action="manage_orders.php" style="display:flex; gap:5px;">
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <input type="date" name="meeting_date" required>
                    <input type="time" name="meeting_time" required>
                    <button type="submit">Set Meeting</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>