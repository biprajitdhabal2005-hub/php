<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'buyer') {
    header("Location: ../auth/login.php");
    exit();
}

$buyer_id = $_SESSION['user_id'];

// Handle rating submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    $stmt = $conn->prepare("INSERT INTO ratings (order_id, rating, review_text) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $order_id, $rating, $review_text);

    if ($stmt->execute()) {
        echo "Thank you for your review!";
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}

// Fetch completed orders by this buyer that don't have a rating yet
$query = "SELECT orders.id AS order_id, servers.server_name
          FROM orders
          JOIN servers ON orders.server_id = servers.id
          LEFT JOIN ratings ON ratings.order_id = orders.id
          WHERE orders.buyer_id = ? AND orders.status = 'completed' AND ratings.id IS NULL";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $buyer_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head><title>Leave a Review - Ownza</title></head>
<body>
    <h2>Rate Your Completed Deals</h2>
    <?php if ($result->num_rows == 0): ?>
        <p>No completed deals waiting for a review.</p>
    <?php endif; ?>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            <h4><?= htmlspecialchars($row['server_name']) ?></h4>
            <form method="POST" action="leave_rating.php">
                <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                <label>Rating (1-5):</label>
                <select name="rating" required>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Bad</option>
                </select><br><br>
                <label>Review:</label><br>
                <textarea name="review_text" rows="3" cols="40"></textarea><br><br>
                <button type="submit">Submit Review</button>
            </form>
        </div>
    <?php endwhile; ?>
</body>
</html>