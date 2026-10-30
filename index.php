<?php
require __DIR__ . '/inc/functions.php';
$page = ['description' => cfg('tagline') . ' : compteur de mots, calcul de TVA, générateur de mot de passe, simulateur de prêt, convertisseur d\'unités et bien plus.'];
include __DIR__ . '/inc/header.php';

$byCat = [];
foreach (tools() as $slug => $t) {
    $byCat[$t['cat']][$slug] = $t;
}
?>
<section class="hero">
  <h1><?= e(cfg('tagline')) ?></h1>
  <p class="lead">Des outils pratiques pour le quotidien, le travail et les études : calculs, textes, conversions et sécurité.
  Gratuits, sans inscription, et utilisables sur ordinateur comme sur mobile.</p>
</section>

<?php foreach ($byCat as $cat => $list): ?>
<section>
  <h2 class="cat-title"><?= e($cat) ?></h2>
  <div class="grid">
  <?php foreach ($list as $slug => $t): ?>
    <a class="card" href="<?= e(tool_url($slug)) ?>">
      <span class="icon"><?= $t['icon'] ?></span>
      <strong><?= e($t['title']) ?></strong>
      <span><?= e($t['desc']) ?></span>
    </a>
  <?php endforeach; ?>
  </div>
</section>
<?php endforeach; ?>

<article class="content">
  <h2>Pourquoi utiliser <?= e(cfg('site_name')) ?> ?</h2>
  <p>Chaque jour, nous avons besoin de petits calculs ou de petites transformations : connaître le prix TTC d'une facture,
  vérifier qu'un texte respecte une limite de caractères, créer un mot de passe solide ou convertir des miles en kilomètres.
  <?= e(cfg('site_name')) ?> rassemble ces outils au même endroit, avec une interface claire et des explications pour comprendre
  le résultat obtenu.</p>
  <h3>Simples et rapides</h3>
  <p>Pas de compte à créer, pas de logiciel à installer : vous saisissez vos données, vous obtenez le résultat immédiatement.
  Chaque page explique la méthode de calcul utilisée et répond aux questions les plus fréquentes.</p>
  <h3>Respectueux de vos données</h3>
  <p>Les informations que vous saisissez servent uniquement à produire le résultat affiché. Elles ne sont ni enregistrées
  dans une base de données, ni revendues. Consultez notre <a href="<?= e(url('confidentialite.php')) ?>">politique de confidentialité</a>
  pour en savoir plus.</p>
  <h3>Une suggestion ?</h3>
  <p>Il vous manque un outil ou vous avez repéré une erreur ? <a href="<?= e(url('contact.php')) ?>">Écrivez-nous</a> :
  le site s'enrichit régulièrement grâce aux suggestions des visiteurs.</p>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
