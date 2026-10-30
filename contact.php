<?php
require __DIR__ . '/inc/functions.php';
$page = ['title' => 'Contact', 'description' => 'Contactez l\'équipe de ' . cfg('site_name') . ' : question, suggestion d\'outil ou signalement d\'erreur.', 'path' => 'contact.php'];

$sent = false;
$error = null;
if (is_post()) {
    $nom = post('nom');
    $email = post('email');
    $message = post('message');
    if (post('site_web') !== '') {
        $sent = true; // champ piège rempli : robot, on fait semblant d'envoyer
    } elseif ($nom === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Merci de remplir tous les champs avec une adresse e-mail valide.';
    } elseif (mb_strlen($message) > 5000) {
        $error = 'Votre message est trop long (5 000 caractères maximum).';
    } else {
        $nom = str_replace(["\r", "\n"], ' ', $nom);
        $host = parse_url((string) cfg('site_url'), PHP_URL_HOST) ?: 'localhost';
        $headers = "From: " . cfg('site_name') . " <no-reply@{$host}>\r\n"
            . "Reply-To: {$email}\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";
        $subject = '=?UTF-8?B?' . base64_encode('[' . cfg('site_name') . '] Message de ' . $nom) . '?=';
        $sent = @mail(cfg('contact_email'), $subject, "Nom : {$nom}\nE-mail : {$email}\n\n{$message}", $headers);
        if (!$sent) {
            $error = 'L\'envoi a échoué. Écrivez-nous directement à ' . cfg('contact_email') . '.';
        }
    }
}
include __DIR__ . '/inc/header.php';
?>
<article class="content">
  <h1>Nous contacter</h1>
  <p>Une question, une suggestion de nouvel outil, une erreur à signaler ? Utilisez le formulaire ci-dessous ou écrivez-nous
  directement à <a href="mailto:<?= e(cfg('contact_email')) ?>"><?= e(cfg('contact_email')) ?></a>.
  Nous répondons généralement sous 48 heures ouvrées.</p>

  <?php if ($sent): ?>
    <div class="result">Merci ! Votre message a bien été envoyé.</div>
  <?php else: ?>
    <?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="tool">
      <label for="nom">Votre nom</label>
      <input type="text" id="nom" name="nom" value="<?= e(post('nom')) ?>" required maxlength="100">
      <label for="email">Votre adresse e-mail</label>
      <input type="text" inputmode="email" id="email" name="email" value="<?= e(post('email')) ?>" required maxlength="200">
      <div class="hp" aria-hidden="true"><label for="site_web">Ne pas remplir</label><input type="text" id="site_web" name="site_web" tabindex="-1" autocomplete="off"></div>
      <label for="message">Votre message</label>
      <textarea id="message" name="message" required maxlength="5000"><?= e(post('message')) ?></textarea>
      <p class="muted">Vos nom et adresse e-mail servent uniquement à vous répondre. Voir notre
      <a href="<?= e(url('confidentialite.php')) ?>">politique de confidentialité</a>.</p>
      <button type="submit">Envoyer</button>
    </form>
  <?php endif; ?>
</article>
<?php include __DIR__ . '/inc/footer.php'; ?>
