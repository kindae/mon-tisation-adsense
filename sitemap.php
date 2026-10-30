<?php
require __DIR__ . '/inc/functions.php';
header('Content-Type: application/xml; charset=UTF-8');
$pages = ['', 'a-propos.php', 'contact.php', 'confidentialite.php', 'mentions-legales.php', 'cgu.php'];
foreach (array_keys(tools()) as $slug) {
    $pages[] = 'outils/' . $slug . '.php';
}
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($pages as $p):
    $file = __DIR__ . '/' . ($p === '' ? 'index.php' : $p); ?>
  <url><loc><?= e(abs_url($p)) ?></loc><lastmod><?= date('Y-m-d', filemtime($file)) ?></lastmod></url>
<?php endforeach; ?>
</urlset>
