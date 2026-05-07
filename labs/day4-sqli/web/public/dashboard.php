<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit;
}

$page_title = "Dashboard · Q8 Logistics";
include '_header.php';

// Show all colleagues — the post-login reward (also reveals the user table contents)
$query = "SELECT username, full_name, role, department, email FROM users ORDER BY id";
$result = mysqli_query($conn, $query);
?>

<div class="card">
  <h1>Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?> 👋</h1>
  <p>You're signed in as <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> · <?= htmlspecialchars($_SESSION['role']) ?> · <?= htmlspecialchars($_SESSION['department']) ?>.<br>
     <a href="/logout.php" style="color:#0d2949;font-weight:600;text-decoration:none">Sign out</a></p>
</div>

<div class="grid">
  <div class="feature-card">
    <h3>📦 Active shipments</h3>
    <p style="font-size:24px;font-weight:700;color:#0d2949">1,247</p>
    <p style="font-size:12px">across Kuwait, KSA, UAE</p>
  </div>
  <div class="feature-card">
    <h3>📊 Q4 revenue</h3>
    <p style="font-size:24px;font-weight:700;color:#0d2949">2.4M KWD</p>
    <p style="font-size:12px;color:#16a34a">+18% YoY</p>
  </div>
  <div class="feature-card">
    <h3>👥 Headcount</h3>
    <p style="font-size:24px;font-weight:700;color:#0d2949">412</p>
    <p style="font-size:12px">3 offices, 6 departments</p>
  </div>
</div>

<div class="card" style="margin-top:24px">
  <h2>Team directory</h2>
  <p style="font-size:13px;margin-bottom:14px">All Q8 staff. Click email to send a message.</p>
  <table>
    <thead>
      <tr><th>Username</th><th>Full Name</th><th>Role</th><th>Department</th><th>Email</th></tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:12px"><?= htmlspecialchars($row['username']) ?></code></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['role']) ?></td>
          <td><span class="badge"><?= htmlspecialchars($row['department']) ?></span></td>
          <td><a href="mailto:<?= htmlspecialchars($row['email']) ?>" style="color:#0d2949;text-decoration:none"><?= htmlspecialchars($row['email']) ?></a></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include '_footer.php'; ?>
