<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'unassigned')");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        session_start();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $name;
        $_SESSION['role'] = 'unassigned';

        header("Location: profile_setup.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Account - Ownza</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="auth-card">
        <h2>Create your account</h2>
        <form method="POST" action="register.php">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Enter your name" required>

            <label>Email</label>
            <input type="email" name="email" placeholder="you@gmail.com" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Create a password" required>

            <button type="submit">Create Account</button>
        </form>
        <p class="auth-link">Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>