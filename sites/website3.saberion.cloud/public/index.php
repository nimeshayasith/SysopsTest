<?php
$site = require __DIR__ . '/../includes/bootstrap.php';
$cfg = $site['cfg'];
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>BrightPath Learning | Practical IT courses online</title>
<meta name="description" content="BrightPath Learning: hands-on courses in Linux, cloud and web hosting.">
<link rel="stylesheet" href="/assets/style.css">
</head>
<body>

<nav class="nav"><div class="wrap">
  <a class="brand" href="/">BrightPath<span> Learning</span></a>
  <ul>
    <li><a href="/">Home</a></li>
    <li><a href="/#courses">Courses</a></li>
    <li><a href="/about.html">About &amp; Register</a></li>
  </ul>
</div></nav>

<header class="hero"><div class="wrap">
  <h1>Learn practical IT skills at your own pace.</h1>
  <p>Hands-on courses in Linux, cloud and web hosting, taught by working engineers, with downloadable materials for every lesson.</p>
  <a class="btn" href="#courses">Browse courses</a>
  <a class="btn alt" href="/about.html">Register</a>
</div></header>

<section id="courses"><div class="wrap">
  <h2>Our courses</h2>
  <p class="lead">Start with the basics or move straight to the advanced topics.</p>
  <div class="grid">
    <?php foreach ($site['courses'] as $c): ?>
      <div class="card">
        <div class="icon"><?= h($c['icon']) ?></div>
        <h3><?= h($c['title']) ?></h3>
        <p><?= h($c['summary']) ?></p>
        <div class="meta">
          <span class="tag"><?= h($c['level']) ?></span>
          <span class="tag"><?= (int) $c['weeks'] ?> weeks</span>
        </div>
        <p style="margin-top:12px">Instructor: <?= h($c['instructor']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div></section>

<section class="tint"><div class="wrap">
  <h2>How it works</h2>
  <p class="lead">Three simple steps.</p>
  <div class="grid">
    <div class="card"><div class="icon">📝</div><h3>1. Register</h3><p>Create a free student account in under a minute.</p></div>
    <div class="card"><div class="icon">🎓</div><h3>2. Learn</h3><p>Follow weekly lessons, labs and quizzes at your own pace.</p></div>
    <div class="card"><div class="icon">📄</div><h3>3. Download</h3><p>Keep the slides, notes and lab guides for future reference.</p></div>
  </div>
</div></section>

<footer><div class="wrap">
  <strong style="color:#fff">BrightPath Learning</strong><br>
  Practical IT courses &middot; <a href="/about.html">Register</a>

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
  <div class="copy">&copy; <?= date('Y') ?> BrightPath Learning. All rights reserved.</div>
</div></footer>

</body>
</html>
