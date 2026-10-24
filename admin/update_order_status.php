<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();

    // If deal completed, mark the server as "sold"
    if ($new_status == 'completed') {
        $stmt2 = $conn->prepare("UPDATE servers SET status = 'sold' WHERE id = (SELECT server_id FROM orders WHERE id = ?)");
        $stmt2->bind_param("i", $order_id);
        $stmt2->execute();
    }

    header("Location: update_order_status.php");
    exit();
}

// Fetch all orders that are past "pending" stage (meeting already scheduled)
$query = "SELECT orders.id AS order_id, orders.status,
                 servers.server_name,
                 buyer.name AS buyer_name, seller.name AS seller_name,
                 meetings.meeting_date, meetings.meeting_time
          FROM orders
          JOIN servers ON orders.server_id = servers.id
          JOIN users AS buyer ON orders.buyer_id = buyer.id
          JOIN users AS seller ON servers.seller_id = seller.id
          LEFT JOIN meetings ON meetings.order_id = orders.id
          WHERE orders.status != 'pending' AND orders.status != 'completed'";
$result = $conn->query($query);

// Define the allowed next-step flow
$status_flow = [
    'meeting_scheduled' => 'verified',
    'verified'          => 'payment_held',
    'payment_held'      => 'transferred',
    'transferred'       => 'completed',
];
?>

<!DOCTYPE html>
<html>
<head><title>Update Order Status - Admin</title></head>
<body>
    <h2>Orders In Progress — Move to Next Stage</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Order ID</th>
            <th>Server</th>
            <th>Buyer</th>
            <th>Seller</th>
            <th>Meeting</th>
            <th>Current Status</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= htmlspecialchars($row['server_name']) ?></td>
            <td><?= htmlspecialchars($row['buyer_name']) ?></td>
            <td><?= htmlspecialchars($row['seller_name']) ?></td>
            <td><?= $row['meeting_date'] ?> <?= $row['meeting_time'] ?></td>
            <td><b><?= $row['status'] ?></b></td>
            <td>
                <?php if (isset($status_flow[$row['status']])): ?>
                <form method="POST" action="update_order_status.php">
                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                    <input type="hidden" name="new_status" value="<?= $status_flow[$row['status']] ?>">
                    <button type="submit">
                        Move to: <?= $status_flow[$row['status']] ?>
                    </button>
                </form>
                <?php else: ?>
                    Done
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>