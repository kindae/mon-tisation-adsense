<?php
require __DIR__ . '/inc/functions.php';
$page = ['title' => 'Politique de confidentialité', 'description' => 'Comment ' . cfg('site_name') . ' traite vos données personnelles et utilise les cookies.', 'path' => 'confidentialite.php'];
include __DIR__ . '/inc/header.php';
?>
<article class="content">
  <h1>Politique de confidentialité</h1>
  <p class="muted">Dernière mise à jour : <?= date('d/m/Y', filemtime(__FILE__)) ?></p>

  <p>La présente politique explique quelles données sont collectées lorsque vous utilisez <?= e(cfg('site_name')) ?>
  (<?= e(cfg('site_url')) ?>), pourquoi, et quels sont vos droits, conformément au Règlement général sur la protection des
  données (RGPD) et à la loi Informatique et Libertés.</p>

  <h2>1. Responsable du traitement</h2>
  <p><?= e(cfg('owner_name')) ?>, <?= e(cfg('owner_address')) ?>. Contact :
  <a href="mailto:<?= e(cfg('contact_email')) ?>"><?= e(cfg('contact_email')) ?></a>.</p>

  <h2>2. Données saisies dans les outils</h2>
  <p>Les textes, nombres et dates que vous saisissez dans les outils sont utilisés uniquement pour calculer le résultat
  affiché. Ils ne sont <strong>ni enregistrés, ni conservés, ni transmis</strong> à des tiers.</p>

  <h2>3. Formulaire de contact</h2>
  <p>Lorsque vous nous écrivez, nous recevons votre nom, votre adresse e-mail et votre message. Ces données servent
  uniquement à vous répondre (base légale : intérêt légitime) et sont conservées au maximum 3 ans après notre dernier échange.</p>

  <h2>4. Journaux techniques</h2>
  <p>Comme tout site web, notre hébergeur enregistre des journaux techniques (adresse IP, date, page consultée, navigateur)
  pour assurer la sécurité et le bon fonctionnement du service. Ces journaux sont conservés pour une durée limitée,
  conformément aux obligations légales.</p>

  <h2>5. Cookies et publicité</h2>
  <p>Ce site est financé par la publicité. Il utilise <strong>Google AdSense</strong>, un service fourni par Google Ireland
  Limited, pour afficher des annonces.</p>
  <ul>
    <li>Des fournisseurs tiers, y compris Google, utilisent des cookies pour diffuser des annonces en fonction des visites
    antérieures d'un utilisateur sur ce site ou sur d'autres sites web.</li>
    <li>Grâce aux cookies publicitaires, Google et ses partenaires adaptent les annonces diffusées auprès des utilisateurs en
    fonction de leur navigation sur ce site et/ou sur d'autres sites web.</li>
    <li>Vous pouvez désactiver la publicité personnalisée dans les
    <a href="https://adssettings.google.com" rel="nofollow noopener" target="_blank">paramètres des annonces Google</a>.
    Vous pouvez également désactiver les cookies de certains fournisseurs tiers pour la publicité personnalisée sur
    <a href="https://www.aboutads.info/choices/" rel="nofollow noopener" target="_blank">www.aboutads.info</a> ou
    <a href="https://www.youronlinechoices.com/fr/" rel="nofollow noopener" target="_blank">www.youronlinechoices.com</a>.</li>
    <li>Pour en savoir plus sur la manière dont Google utilise les données lorsque vous utilisez les sites de ses partenaires :
    <a href="https://policies.google.com/technologies/partner-sites?hl=fr" rel="nofollow noopener" target="_blank">policies.google.com/technologies/partner-sites</a>.</li>
  </ul>
  <p>Lors de votre première visite, un bandeau de consentement vous permet d'accepter ou de refuser les cookies publicitaires.
  En cas de refus, des annonces non personnalisées peuvent tout de même s'afficher. Vous pouvez modifier votre choix à tout
  moment via le lien de gestion du consentement proposé par ce bandeau.</p>

  <h2>6. Stockage local</h2>
  <p>Certains outils (par exemple l'horloge) mémorisent vos préférences d'affichage dans votre navigateur (« localStorage »).
  Ces informations restent sur votre appareil et ne nous sont jamais transmises.</p>

  <h2>7. Vos droits</h2>
  <p>Vous disposez d'un droit d'accès, de rectification, d'effacement, de limitation, d'opposition et de portabilité de vos
  données. Pour l'exercer, écrivez à <a href="mailto:<?= e(cfg('contact_email')) ?>"><?= e(cfg('contact_email')) ?></a>.
  Si vous estimez que vos droits ne sont pas respectés, vous pouvez adresser une réclamation à la CNIL (www.cnil.fr).</p>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
