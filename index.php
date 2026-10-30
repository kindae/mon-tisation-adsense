<?php
require __DIR__ . '/inc/functions.php';
$page = ['description' => cfg('tagline') . ' : calcul de TVA, salaire brut/net, QR code, compteur de mots, générateur de mot de passe, simulateur de prêt et bien plus.'];
include __DIR__ . '/inc/header.php';

$byCat = array_fill_keys(array_keys(categories()), []);
foreach (tools() as $slug => $t) {
    $byCat[$t['cat']][$slug] = $t;
}
$byCat = array_filter($byCat);
?>
</main>
<section class="hero">
  <div class="wrap">
    <span class="pill">✨ <?= count(tools()) ?> outils gratuits · sans inscription</span>
    <h1>Tous vos outils du quotidien,<br><span class="grad">au même endroit.</span></h1>
    <p class="lead">Calculs, finances, textes, conversions, dates et sécurité : des outils rapides, clairs et expliqués,
    sur ordinateur comme sur mobile.</p>
    <div class="search">
      <svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M10 2a8 8 0 0 1 6.32 12.9l5.39 5.4-1.42 1.4-5.39-5.38A8 8 0 1 1 10 2zm0 2a6 6 0 1 0 0 12 6 6 0 0 0 0-12z"/></svg>
      <input type="search" id="tool-search" placeholder="Rechercher un outil : TVA, QR code, mot de passe…" aria-label="Rechercher un outil" autocomplete="off">
    </div>
    <div class="chips">
      <?php foreach ($byCat as $cat => $list): ?>
        <a href="#<?= cat_slug($cat) ?>" style="--accent: <?= cat_color($cat) ?>"><?= categories()[$cat][1] ?> <?= e($cat) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<main class="wrap" id="outils">
<?php foreach ($byCat as $cat => $list): ?>
<section class="cat-section" id="<?= cat_slug($cat) ?>" style="--accent: <?= cat_color($cat) ?>">
  <h2 class="cat-title"><span class="cat-dot"></span><?= e($cat) ?> <small><?= count($list) ?> outil<?= count($list) > 1 ? 's' : '' ?></small></h2>
  <div class="grid">
  <?php foreach ($list as $slug => $t): ?>
    <a class="card" href="<?= e(tool_url($slug)) ?>" data-search="<?= e(mb_strtolower($t['title'] . ' ' . $t['desc'] . ' ' . $cat)) ?>">
      <span class="icon"><?= $t['icon'] ?></span>
      <strong><?= e($t['title']) ?></strong>
      <span class="desc"><?= e($t['desc']) ?></span>
      <span class="go">Utiliser l'outil →</span>
    </a>
  <?php endforeach; ?>
  </div>
</section>
<?php endforeach; ?>
<p id="no-result" class="empty" hidden>Aucun outil ne correspond à votre recherche. <a href="<?= e(url('contact.php')) ?>">Suggérez-le-nous !</a></p>

<section class="features">
  <div class="feature"><span>⚡</span><h3>Instantané</h3><p>Pas de compte, pas d'installation : le résultat s'affiche en un clic.</p></div>
  <div class="feature"><span>📖</span><h3>Expliqué</h3><p>Chaque outil détaille la formule utilisée, avec des exemples concrets.</p></div>
  <div class="feature"><span>🔒</span><h3>Confidentiel</h3><p>Vos saisies servent uniquement au calcul et ne sont jamais enregistrées.</p></div>
  <div class="feature"><span>📱</span><h3>Partout</h3><p>Conçu pour le mobile, la tablette et l'ordinateur, en mode clair ou sombre.</p></div>
</section>

<article class="content">
  <h2>Pourquoi utiliser <?= e(cfg('site_name')) ?> ?</h2>
  <p>Chaque jour, nous avons besoin de petits calculs ou de petites transformations : connaître le prix TTC d'une facture,
  estimer un salaire net, vérifier qu'un texte respecte une limite de caractères, créer un mot de passe solide ou un QR code
  pour un menu de restaurant. <?= e(cfg('site_name')) ?> rassemble ces outils au même endroit, avec une interface claire et
  des explications pour comprendre le résultat obtenu.</p>
  <h3>Simples et rapides</h3>
  <p>Vous saisissez vos données, vous obtenez le résultat immédiatement. Chaque page explique la méthode de calcul utilisée et
  répond aux questions les plus fréquentes, pour que vous puissiez vérifier et réutiliser le raisonnement.</p>
  <h3>Respectueux de vos données</h3>
  <p>Les informations que vous saisissez servent uniquement à produire le résultat affiché. Elles ne sont ni enregistrées
  dans une base de données, ni revendues. Consultez notre <a href="<?= e(url('confidentialite.php')) ?>">politique de confidentialité</a>
  pour en savoir plus.</p>
  <h3>Une suggestion ?</h3>
  <p>Il vous manque un outil ou vous avez repéré une erreur ? <a href="<?= e(url('contact.php')) ?>">Écrivez-nous</a> :
  le site s'enrichit régulièrement grâce aux suggestions des visiteurs.</p>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
