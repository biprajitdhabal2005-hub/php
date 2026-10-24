<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// If already assigned, skip this page
if ($_SESSION['role'] != 'unassigned') {
    header("Location: ../" . $_SESSION['role'] . "/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role']; // 'buyer' or 'seller'
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $role, $user_id);
    $stmt->execute();

    $_SESSION['role'] = $role;
    header("Location: ../" . $role . "/dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Set Up Your Profile - Ownza</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-card">
        <h2>How do you want to use Ownza?</h2>
        <p>You can change this later from your profile.</p>

        <form method="POST" action="profile_setup.php" class="role-select">
            <button type="submit" name="role" value="buyer" class="role-btn buyer">
                🛒 Continue as Buyer
            </button>
            <button type="submit" name="role" value="seller" class="role-btn seller">
                🏷️ Continue as Seller
            </button>
        </form>
    </div>
</body>
</html>