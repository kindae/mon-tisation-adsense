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
$accent = $tool ? cat_color($tool['cat']) : null;
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
<meta property="og:site_name" content="<?= e(cfg('site_name')) ?>">
<meta property="og:url" content="<?= e(abs_url($page['path'] ?? '')) ?>">
<meta name="theme-color" content="#2563eb">
<?php if (!empty($page['noindex'])): ?><meta name="robots" content="noindex"><?php endif; ?>
<link rel="stylesheet" href="<?= e(url('assets/style.css')) ?>?v=2">
<link rel="icon" href="<?= e(url('assets/logo.svg')) ?>" type="image/svg+xml">
<?php if ($tool): ?>
<script type="application/ld+json"><?= json_encode([
    '@context' => 'https://schema.org', '@type' => 'WebApplication', 'name' => $tool['title'],
    'description' => $tool['desc'], 'url' => abs_url($page['path']), 'applicationCategory' => 'UtilitiesApplication',
    'operatingSystem' => 'Tous', 'inLanguage' => 'fr', 'isAccessibleForFree' => true,
    'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'EUR'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<?php endif; ?>
<?php if ($client): ?>
<meta name="google-adsense-account" content="<?= e($client) ?>">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=<?= e($client) ?>" crossorigin="anonymous"></script>
<?php endif; ?>
</head>
<body<?= $accent ? ' style="--accent: ' . e($accent) . '"' : '' ?>>
<header class="site-header">
  <div class="wrap">
    <a class="logo" href="<?= e(url()) ?>"><img src="<?= e(url('assets/logo.svg')) ?>" alt="" width="34" height="34"><span><?= e(cfg('site_name')) ?></span></a>
    <nav>
      <a href="<?= e(url()) ?>#outils">Outils</a>
      <a href="<?= e(url('a-propos.php')) ?>">À propos</a>
      <a href="<?= e(url('contact.php')) ?>" class="nav-cta">Contact</a>
    </nav>
  </div>
</header>
<?php if ($tool): ?>
<div class="tool-hero">
  <div class="wrap">
    <nav class="breadcrumb"><a href="<?= e(url()) ?>">Accueil</a> <span>›</span> <a href="<?= e(url()) ?>#<?= cat_slug($tool['cat']) ?>"><?= e($tool['cat']) ?></a></nav>
  </div>
</div>
<main class="wrap layout">
<div class="main-col">
<?php else: ?>
<main class="wrap">
<?php endif; ?>
<?php ad('top'); ?>
