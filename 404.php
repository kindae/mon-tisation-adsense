<?php
require __DIR__ . '/inc/functions.php';
http_response_code(404);
$page = ['title' => 'Page introuvable', 'description' => 'Cette page n\'existe pas.', 'noindex' => true];
include __DIR__ . '/inc/header.php';
?>
<section class="hero">
  <h1>Page introuvable</h1>
  <p class="lead">La page demandée n'existe pas ou a été déplacée.</p>
  <a class="btn" href="<?= e(url()) ?>">Voir tous les outils</a>
</section>
<?php include __DIR__ . '/inc/footer.php'; ?>
