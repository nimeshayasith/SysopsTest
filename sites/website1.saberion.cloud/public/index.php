<?php
$site = require __DIR__ . '/../includes/bootstrap.php';
$cfg = $site['cfg'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Northwind Digital | Cloud hosting and web development</title>
<meta name="description" content="Northwind Digital builds and hosts fast, secure websites on AWS for small businesses.">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<nav class="nav"><div class="wrap">
  <a class="brand" href="/">Northwind<span> Digital</span></a>
  <ul>
    <li><a href="/">Home</a></li>
    <li><a href="/#services">Services</a></li>
    <li><a href="/about.html">About &amp; Contact</a></li>
  </ul>
</div></nav>

<header class="hero"><div class="wrap">
  <h1>Websites and cloud hosting that just work.</h1>
  <p>We design, build and run secure websites on AWS, so you can focus on your business and not on servers.</p>
  <a class="btn" href="/about.html">Talk to us</a>
  <a class="btn alt" href="#services">Our services</a>
</div></header>

<section id="services"><div class="wrap">
  <h2>What we do</h2>
  <p class="lead">Everything a growing company needs to get online and stay online.</p>
  <div class="grid">
    <div class="card"><div class="icon">☁️</div><h3>Cloud Hosting</h3><p>Managed Ubuntu servers and databases on AWS, monitored and backed up every day.</p></div>
    <div class="card"><div class="icon">💻</div><h3>Web Development</h3><p>Fast, mobile-friendly PHP websites built to your brand and your budget.</p></div>
    <div class="card"><div class="icon">🔒</div><h3>Security &amp; Backups</h3><p>Firewalls, HTTPS and automated backups, with a restore plan we actually test.</p></div>
    <div class="card"><div class="icon">🛟</div><h3>Support</h3><p>Real engineers answer your calls, with response in under one hour.</p></div>
  </div>
</div></section>

<section class="tint"><div class="wrap">
  <div class="stats">
    <div><strong>120+</strong>Sites hosted</div>
    <div><strong>99.9%</strong>Uptime</div>
    <div><strong>8 yrs</strong>In business</div>
    <div><strong>24/7</strong>Monitoring</div>
  </div>
</div></section>

<section><div class="wrap">
  <h2>Latest news</h2>
  <p class="lead">Updates from our team.</p>
  <div class="grid">
    <?php foreach ($site['news'] as $n): ?>
      <div class="card news">
        <time><?= h(date('j M Y', strtotime($n['posted']))) ?></time>
        <h3><?= h($n['title']) ?></h3>
        <p><?= h($n['body']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div></section>

<section class="tint cta"><div class="wrap">
  <h2>Ready to move to the cloud?</h2>
  <p class="lead" style="margin:0 auto 24px">Get a free quote within one business day.</p>
  <a class="btn" style="background:var(--accent);color:#fff" href="/about.html">Contact us</a>
</div></section>

<footer><div class="wrap">
  <strong style="color:#fff">Northwind Digital</strong><br>
  Cloud hosting and web development &middot; <a href="/about.html">Contact</a>

  <div class="status">
    <strong>System status</strong><br>
    <?php if ($site['ok']): ?>
      Database: <span class="ok">Connected</span><br>
      Site: <?= h($cfg['site_name']) ?> (<?= h($cfg['name']) ?>)<br>
      Message: <?= h($site['message']) ?><br>
      Visit count: <strong><?= (int) $site['visits'] ?></strong>
    <?php else: ?>
      Database: <span class="bad"><?= h($site['error']) ?></span>
    <?php endif; ?>
  </div>
  <div class="copy">&copy; <?= date('Y') ?> Northwind Digital. All rights reserved.</div>
</div></footer>

</body>
</html>
