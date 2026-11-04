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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f2">
    <title>Create account - Ownza</title>
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

        <section class="auth-content" aria-labelledby="register-title">
            <div class="auth-card">
                <h2 id="register-title">Create your account</h2>
                <p class="auth-intro">Join Ownza and take the next step for your community.</p>
                <form method="POST" action="register.php">
                    <label for="name">Full name</label>
                    <input id="name" type="text" name="name" placeholder="Enter your name" autocomplete="name" required>

                    <label for="email">Email address</label>
                    <input id="email" type="email" name="email" placeholder="you@example.com" autocomplete="email" required>

                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" placeholder="Create a password" autocomplete="new-password" required>

                    <button type="submit">Create account</button>
                </form>
                <p class="auth-link">Already have an account? <a href="login.php">Sign in</a></p>
                <p class="auth-note">Your account and community, in one place</p>
            </div>
        </section>
    </main>
</body>
</html>