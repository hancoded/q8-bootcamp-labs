<?php
// Shared header — included by all pages
$page_title = $page_title ?? 'Q8 Logistics Portal';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  :root {
    --q8-navy: #0d2949;
    --q8-navy-deep: #07182d;
    --q8-gold: #f59e0b;
    --text: #0f172a;
    --text-muted: #475569;
    --border: #e2e8f0;
    --bg: #f1f5f9;
    --card: #fff;
    --error-bg: #fef2f2;
    --error-text: #b91c1c;
    --error-border: #fecaca;
  }
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { font-family: 'Tajawal', system-ui, -apple-system, sans-serif; }
  body { background: var(--bg); color: var(--text); min-height: 100vh; line-height: 1.5; }

  .topbar {
    background: var(--q8-navy);
    color: #fff;
    padding: 14px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .topbar a { color: rgba(255,255,255,0.85); text-decoration: none; font-size: 14px; margin-left: 18px; }
  .topbar a:hover { color: #fff; }
  .topbar .brand { display: flex; align-items: center; gap: 12px; }
  .topbar .brand .mark {
    width: 36px; height: 36px; background: var(--q8-gold);
    border-radius: 8px; display: grid; place-items: center;
    font-weight: 900; color: var(--q8-navy);
  }
  .topbar .brand .name { font-weight: 700; font-size: 15px; }
  .topbar .brand .name small { display: block; font-size: 10px; font-weight: 400; color: rgba(255,255,255,0.55); text-transform: uppercase; letter-spacing: 1px; }

  .container { max-width: 1100px; margin: 0 auto; padding: 32px 24px; }
  .card { background: var(--card); border-radius: 10px; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.04); margin-bottom: 20px; }
  h1 { font-size: 26px; font-weight: 700; margin-bottom: 6px; letter-spacing: -0.3px; }
  h2 { font-size: 18px; font-weight: 700; margin-bottom: 14px; color: var(--q8-navy); }
  p { color: var(--text-muted); margin-bottom: 12px; }

  .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; }
  .feature-card {
    background: var(--card); border-radius: 10px; padding: 20px;
    border: 1px solid var(--border);
  }
  .feature-card h3 { font-size: 15px; font-weight: 700; margin-bottom: 6px; color: var(--q8-navy); }
  .feature-card p { font-size: 13px; }
  .feature-card a { color: var(--q8-navy); font-weight: 700; text-decoration: none; font-size: 13px; }

  .btn {
    display: inline-block; padding: 11px 22px;
    background: var(--q8-navy); color: #fff;
    border: none; border-radius: 8px;
    font: inherit; font-size: 14px; font-weight: 700;
    cursor: pointer; text-decoration: none;
    transition: background 0.15s;
  }
  .btn:hover { background: var(--q8-navy-deep); }
  .btn-gold { background: var(--q8-gold); color: var(--q8-navy); }
  .btn-gold:hover { background: #d97706; }

  .field { margin-bottom: 14px; }
  .field label { display: block; font-size: 13px; font-weight: 500; margin-bottom: 4px; }
  .field input, .field select {
    width: 100%; padding: 10px 12px; font: inherit; font-size: 14px;
    border: 1px solid var(--border); border-radius: 6px;
  }
  .field input:focus { outline: 2px solid var(--q8-navy); outline-offset: -1px; border-color: transparent; }

  .error {
    padding: 11px 14px; background: var(--error-bg); color: var(--error-text);
    border: 1px solid var(--error-border); border-radius: 8px;
    font-size: 14px; margin-bottom: 16px;
  }

  table { width: 100%; border-collapse: collapse; font-size: 14px; }
  table th { background: #f8fafc; padding: 10px 14px; text-align: left; font-weight: 600; color: var(--text-muted); border-bottom: 1px solid var(--border); font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
  table td { padding: 12px 14px; border-bottom: 1px solid var(--border); vertical-align: middle; }
  table tr:hover { background: #f8fafc; }
  .price { font-weight: 700; color: var(--q8-navy); }
  .badge { display: inline-block; padding: 2px 10px; background: #e0f2fe; color: #0369a1; border-radius: 12px; font-size: 11px; font-weight: 600; }
</style>
</head>
<body>

<header class="topbar">
  <div class="brand">
    <div class="mark">Q8</div>
    <div class="name">Q8 Logistics<small>Internal Portal</small></div>
  </div>
  <nav>
    <a href="/index.php">Home</a>
    <a href="/products.php">Products</a>
    <a href="/login.php">Sign in</a>
    <?php if (isset($_GET['logged_in'])): ?>
      <a href="/dashboard.php">Dashboard</a>
    <?php endif; ?>
  </nav>
</header>

<main class="container">
