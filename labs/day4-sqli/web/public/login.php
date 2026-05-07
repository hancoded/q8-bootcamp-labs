<?php
session_start();
require 'db.php';

$error = null;

// =====================================================================
// INTENTIONALLY VULNERABLE
// - Direct string concatenation into SQL → SQLi
// - No bcrypt verify; accepts any row match → auth bypass via SQLi
// Real apps use prepared statements + password_verify($input, $hash).
// =====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // BAD: string concatenation, no parameterization
    $query = "SELECT id, username, full_name, role, department FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if ($result === false) {
        // SQL error — surface it (helpful for SQLi recon)
        $error = "Database error: " . mysqli_error($conn);
    } elseif (mysqli_num_rows($result) > 0) {
        // BAD: any row match = logged in (no real password check)
        $row = mysqli_fetch_assoc($result);
        $_SESSION['user_id']    = $row['id'];
        $_SESSION['username']   = $row['username'];
        $_SESSION['full_name']  = $row['full_name'];
        $_SESSION['role']       = $row['role'];
        $_SESSION['department'] = $row['department'];
        header('Location: /dashboard.php');
        exit;
    } else {
        $error = "Invalid credentials. Please try again.";
    }
}

$page_title = "Sign in · Q8 Logistics";
include '_header.php';
?>

<div class="card" style="max-width:440px; margin:40px auto;">
  <h1>Sign in</h1>
  <p>Use your Q8 corporate credentials to continue.</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="field">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="firstname.lastname" autocomplete="off" required>
    </div>
    <div class="field">
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="off" required>
    </div>
    <button type="submit" class="btn" style="width:100%">Sign in</button>
  </form>

  <p style="margin-top:20px; font-size:12px; color:#94a3b8;">
    Issues signing in? Contact <a href="mailto:it@q8logistics.com" style="color:#0d2949">it@q8logistics.com</a>.
  </p>
</div>

<?php include '_footer.php'; ?>
