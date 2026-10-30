<?php
require __DIR__ . '/inc/functions.php';
$page = ['title' => 'Mentions légales', 'description' => 'Mentions légales du site ' . cfg('site_name') . '.', 'path' => 'mentions-legales.php'];
include __DIR__ . '/inc/header.php';
?>
<article class="content">
  <h1>Mentions légales</h1>
  <h2>Éditeur du site</h2>
  <p><?= e(cfg('site_name')) ?> (<?= e(cfg('site_url')) ?>) est édité par :<br>
  <?= e(cfg('owner_name')) ?><br>
  <?= e(cfg('owner_address')) ?><br>
  E-mail : <a href="mailto:<?= e(cfg('contact_email')) ?>"><?= e(cfg('contact_email')) ?></a></p>
  <p>Directeur de la publication : <?= e(cfg('owner_name')) ?>.</p>

  <h2>Hébergement</h2>
  <p><?= e(cfg('host_info')) ?></p>

  <h2>Propriété intellectuelle</h2>
  <p>L'ensemble des contenus de ce site (textes, mise en page, code) est la propriété de l'éditeur, sauf mention contraire.
  Toute reproduction, totale ou partielle, sans autorisation préalable est interdite.</p>

  <h2>Responsabilité</h2>
  <p>Les outils et informations proposés sur ce site le sont à titre indicatif. Malgré le soin apporté à leur réalisation,
  l'éditeur ne peut garantir l'absence d'erreur et ne saurait être tenu responsable des décisions prises sur la base des
  résultats obtenus.</p>

  <h2>Données personnelles</h2>
  <p>Voir notre <a href="<?= e(url('confidentialite.php')) ?>">politique de confidentialité</a>.</p>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
