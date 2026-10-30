<?php
if (!defined('APP')) { exit; }
/** @var array $page title, description, path ; @var array|null $tool */
$tool = $tool ?? null;
$page = $page ?? [];
if ($tool) {
    $page += ['title' => $tool['title'], 'description' => $tool['desc'], 'path' => 'outils/' . $tool['slug'] . '.php'];
}
$fullTitle = isset($page['title']) ? $page['title'] . ' — ' . cfg('site_name') : cfg('site_name') . ' — ' . cfg('tagline');
$client = cfg('adsense_client');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e($page['description'] ?? cfg('tagline')) ?>">
<link rel="canonical" href="<?= e(abs_url($page['path'] ?? '')) ?>">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e($page['description'] ?? cfg('tagline')) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e(abs_url($page['path'] ?? '')) ?>">
<?php if (!empty($page['noindex'])): ?><meta name="robots" content="noindex"><?php endif; ?>
<link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🧰</text></svg>">
<?php if ($client): ?>
<meta name="google-adsense-account" content="<?= e($client) ?>">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= e($client) ?>" crossorigin="anonymous"></script>
<?php endif; ?>
</head>
<body>
<header class="site-header">
  <div class="wrap">
    <a class="logo" href="<?= e(url()) ?>">🧰 <?= e(cfg('site_name')) ?></a>
    <nav>
      <a href="<?= e(url()) ?>">Outils</a>
      <a href="<?= e(url('a-propos.php')) ?>">À propos</a>
      <a href="<?= e(url('contact.php')) ?>">Contact</a>
    </nav>
  </div>
</header>
<main class="wrap">
<?php if ($tool): ?>
<nav class="breadcrumb"><a href="<?= e(url()) ?>">Accueil</a> › <span><?= e($tool['cat']) ?></span> › <span><?= e($tool['title']) ?></span></nav>
<?php endif; ?>
<?php ad('top'); ?>
