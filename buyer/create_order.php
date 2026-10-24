<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Step 1: Server ID aslo GET-e, order confirm korar age details dekhano
if (isset($_GET['server_id']) && !isset($_POST['confirm'])) {
    $server_id = $_GET['server_id'];

    $stmt = $conn->prepare("SELECT servers.*, users.name AS seller_name FROM servers JOIN users ON servers.seller_id = users.id WHERE servers.id = ? AND servers.status = 'approved'");
    $stmt->bind_param("i", $server_id);
    $stmt->execute();
    $server = $stmt->get_result()->fetch_assoc();

    if (!$server) {
        die("Server not found or not available.");
    }
?>
    <!DOCTYPE html>
    <html>
    <head><title>Confirm Order - Ownza</title></head>
    <body>
        <h2>Confirm Your Order</h2>
        <p><b>Server:</b> <?= htmlspecialchars($server['server_name']) ?></p>
        <p><b>Seller:</b> <?= htmlspecialchars($server['seller_name']) ?></p>
        <p><b>Members:</b> <?= $server['member_count'] ?></p>
        <p><b>Price:</b> ₹<?= $server['price'] ?></p>

        <form method="POST" action="create_order.php">
            <input type="hidden" name="server_id" value="<?= $server['id'] ?>">
            <input type="hidden" name="confirm" value="1">
            <button type="submit">Confirm & Place Order</button>
        </form>
    </body>
    </html>
<?php
    exit();
}

// Step 2: Order confirm hole, database-e insert kora
if (isset($_POST['confirm'])) {
    $server_id = $_POST['server_id'];

    $stmt = $conn->prepare("INSERT INTO orders (buyer_id, server_id, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("ii", $buyer_id, $server_id);

    if ($stmt->execute()) {
        echo "Order placed successfully! Waiting for admin to schedule a meeting.";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}
?>