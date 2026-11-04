<?php
session_start();
include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // Role-wise redirect
        if ($user['role'] == 'admin') {
            header("Location: ../admin/dashboard.php");
        } elseif ($user['role'] == 'seller') {
            header("Location: ../seller/dashboard.php");
        } else {
            header("Location: ../buyer/dashboard.php");
        }
        exit();
    } else {
        $error = 'The email or password you entered is incorrect. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f2">
    <title>Sign in - Ownza</title>
    <link rel="stylesheet" href="../assets/ownza.css">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-showcase" aria-label="About Ownza">
            <a class="auth-brand" href="login.php">
                <span class="auth-brand-mark" aria-hidden="true">o</span>
                Ownza
            </a>

            <div class="auth-showcase-copy">
                <span class="auth-eyebrow">A marketplace built for owners</span>
                <h1>Your community.<br>A <span>new chapter.</span></h1>
                <p>Discover thriving online communities, meet their owners, and make your next move with confidence.</p>
            </div>

            <div class="showcase-card" aria-hidden="true">
                <div class="showcase-card-top">
                    <span>Featured community</span>
                    <span class="showcase-live">Explore Ownza</span>
                </div>
                <div class="showcase-listing">
                    <span class="showcase-avatar">✦</span>
                    <span class="showcase-listing-copy">
                        <strong>A community with potential</strong>
                        <span>Growing audience · Established</span>
                    </span>
                    <span class="showcase-price">Explore</span>
                </div>
            </div>

            <span class="auth-showcase-foot">A considered way to buy and sell online communities.</span>
        </section>

        <section class="auth-content" aria-labelledby="login-title">
            <div class="auth-card">
                <h2 id="login-title">Welcome back</h2>
                <p class="auth-intro">Sign in to continue to your Ownza account.</p>

                <?php if ($error !== ''): ?>
                    <p class="auth-error" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></p>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>

                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required>

                    <button type="submit">Sign in to Ownza</button>
                </form>

                <p class="auth-link">New to Ownza? <a href="register.php">Create an account</a></p>
                <p class="auth-note">Your account and community, in one place</p>
            </div>
        </section>
    </main>
</body>
</html>