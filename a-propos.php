<?php
require __DIR__ . '/inc/functions.php';
$page = ['title' => 'À propos', 'description' => 'Qui sommes-nous et pourquoi ' . cfg('site_name') . ' existe.', 'path' => 'a-propos.php'];
include __DIR__ . '/inc/header.php';
?>
<article class="content">
  <h1>À propos de <?= e(cfg('site_name')) ?></h1>
  <p><?= e(cfg('site_name')) ?> est un site indépendant qui propose des outils en ligne gratuits pour simplifier les petites
  tâches du quotidien : calculs financiers, conversions, traitement de texte, sécurité informatique ou gestion du temps.</p>

  <h2>Notre démarche</h2>
  <p>Nous sommes partis d'un constat simple : beaucoup de sites d'outils en ligne sont lents, surchargés, demandent une
  inscription ou ne donnent aucune explication sur le résultat. Nous avons voulu faire l'inverse :</p>
  <ul>
    <li><strong>des pages rapides</strong>, lisibles sur mobile comme sur ordinateur ;</li>
    <li><strong>aucune inscription</strong> ni installation ;</li>
    <li><strong>des explications claires</strong> : chaque outil détaille la formule ou la méthode utilisée, avec des exemples ;</li>
    <li><strong>le respect de vos données</strong> : ce que vous saisissez n'est jamais enregistré.</li>
  </ul>

  <h2>Comment le site est-il financé ?</h2>
  <p>Pour rester gratuit, le site affiche des publicités fournies par Google AdSense. Ces revenus servent à payer l'hébergement
  et le temps consacré à l'amélioration et à l'ajout de nouveaux outils. Vous trouverez plus d'informations sur l'usage des
  cookies publicitaires dans notre <a href="<?= e(url('confidentialite.php')) ?>">politique de confidentialité</a>.</p>

  <h2>Fiabilité des résultats</h2>
  <p>Nous vérifions soigneusement chaque outil, mais une erreur reste toujours possible. Les résultats sont fournis à titre
  indicatif : pour une décision importante (crédit, santé, fiscalité), demandez conseil à un professionnel. Si vous repérez
  une erreur, <a href="<?= e(url('contact.php')) ?>">signalez-la-nous</a>, nous la corrigerons rapidement.</p>

  <h2>Responsable du site</h2>
  <p>Le site est édité par <?= e(cfg('owner_name')) ?>. Vous pouvez nous écrire à
  <a href="mailto:<?= e(cfg('contact_email')) ?>"><?= e(cfg('contact_email')) ?></a>.</p>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
