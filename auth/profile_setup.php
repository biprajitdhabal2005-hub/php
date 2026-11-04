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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f4f6f2">
    <title>Choose your path - Ownza</title>
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

        <section class="auth-content" aria-labelledby="profile-title">
            <div class="auth-card">
                <h2 id="profile-title">How will you use Ownza?</h2>
                <p class="auth-intro">Choose the path that suits you. You can get started in just a moment.</p>

                <form method="POST" action="profile_setup.php" class="role-select">
                    <button type="submit" name="role" value="buyer" class="role-btn">
                        <span class="role-btn-icon" aria-hidden="true">⌕</span>
                        <span class="role-btn-copy">
                            <strong>I'm looking to buy</strong>
                            <span>Discover communities to make your own</span>
                        </span>
                    </button>
                    <button type="submit" name="role" value="seller" class="role-btn">
                        <span class="role-btn-icon" aria-hidden="true">↗</span>
                        <span class="role-btn-copy">
                            <strong>I'm looking to sell</strong>
                            <span>Find the right next owner for your community</span>
                        </span>
                    </button>
                </form>

                <p class="auth-note">Your account and community, in one place</p>
            </div>
        </section>
    </main>
</body>
</html>