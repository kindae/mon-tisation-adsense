<?php
require __DIR__ . '/inc/functions.php';
$page = ['title' => 'Conditions d\'utilisation', 'description' => 'Conditions générales d\'utilisation du site ' . cfg('site_name') . '.', 'path' => 'cgu.php'];
include __DIR__ . '/inc/header.php';
?>
<article class="content">
  <h1>Conditions générales d'utilisation</h1>
  <h2>1. Objet</h2>
  <p>Les présentes conditions encadrent l'utilisation du site <?= e(cfg('site_name')) ?>. En utilisant le site, vous les acceptez.</p>
  <h2>2. Accès au service</h2>
  <p>Les outils sont accessibles gratuitement, sans inscription, 24 heures sur 24, sauf interruption pour maintenance ou en cas
  de force majeure. L'éditeur peut modifier, ajouter ou retirer des outils à tout moment.</p>
  <h2>3. Utilisation</h2>
  <p>Vous vous engagez à utiliser le site de manière loyale : il est interdit de tenter d'en perturber le fonctionnement,
  d'en extraire le contenu de manière automatisée ou massive, ou de l'utiliser à des fins illicites.</p>
  <h2>4. Résultats fournis</h2>
  <p>Les résultats des outils sont fournis à titre purement indicatif. Ils ne constituent ni un conseil financier, ni un avis
  médical, ni un conseil juridique ou fiscal. Vérifiez toujours les informations importantes auprès d'un professionnel.</p>
  <h2>5. Publicité</h2>
  <p>Le site affiche des annonces publicitaires. L'éditeur n'est pas responsable du contenu de ces annonces ni des sites vers
  lesquels elles renvoient.</p>
  <h2>6. Droit applicable</h2>
  <p>Les présentes conditions sont soumises au droit français. Pour toute question :
  <a href="<?= e(url('contact.php')) ?>">contactez-nous</a>.</p>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
