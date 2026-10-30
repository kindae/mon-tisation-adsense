<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('base64');

$text = post('texte');
$action = post('action', 'encode');
$output = null;
$error = null;
if (is_post() && $text !== '') {
    if ($action === 'decode') {
        $decoded = base64_decode(preg_replace('/\s+/', '', strtr($text, '-_', '+/')), true);
        if ($decoded === false) {
            $error = 'Cette chaîne n\'est pas du Base64 valide.';
        } elseif (!mb_check_encoding($decoded, 'UTF-8')) {
            $error = 'Le contenu décodé est binaire (image, fichier…) et ne peut pas être affiché comme du texte.';
        } else {
            $output = $decoded;
        }
    } else {
        $output = base64_encode($text);
    }
}
include __DIR__ . '/../inc/header.php';
?>
<h1><?= e($tool['title']) ?></h1>
<p class="lead">Encodez n'importe quel texte en Base64 ou décodez une chaîne Base64 pour lire son contenu. Les caractères accentués
et les emojis sont pris en charge (UTF-8).</p>

<form method="post" class="tool">
  <label for="texte">Texte ou chaîne Base64</label>
  <textarea id="texte" name="texte" placeholder="Bonjour le monde !"><?= e($text) ?></textarea>
  <button type="submit" name="action" value="encode">Encoder en Base64</button>
  <button type="submit" name="action" value="decode" class="secondary">Décoder le Base64</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($output !== null): ?>
<div class="result">
  <label for="sortie"><strong><?= $action === 'decode' ? 'Texte décodé' : 'Résultat en Base64' ?></strong></label>
  <textarea id="sortie" readonly><?= e($output) ?></textarea>
  <button type="button" class="secondary" data-copy="#sortie">Copier</button>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Qu'est-ce que le Base64 ?</h2>
  <p>Le Base64 est une méthode d'encodage qui représente des données quelconques à l'aide de 64 caractères « sûrs » :
  les lettres A à Z et a à z, les chiffres 0 à 9, ainsi que les signes + et /. Le signe = sert à compléter la fin de la chaîne.
  Il permet de transporter des données binaires (une image, un fichier) dans des systèmes prévus pour du texte, comme les
  e-mails, le JSON ou le HTML.</p>

  <h2>Exemples d'utilisation</h2>
  <ul>
    <li><strong>Pièces jointes d'e-mails</strong> : le format MIME encode les fichiers joints en Base64.</li>
    <li><strong>Images intégrées</strong> : une petite icône peut être insérée directement dans une page web via une URL <code>data:</code>.</li>
    <li><strong>Authentification HTTP</strong> : l'en-tête « Basic » contient l'identifiant et le mot de passe encodés en Base64.</li>
    <li><strong>Jetons JWT</strong> : leurs parties sont encodées en Base64 « URL-safe » (avec - et _ à la place de + et /). Notre décodeur accepte les deux variantes.</li>
  </ul>

  <h2>Base64 n'est pas du chiffrement</h2>
  <p>Attention : n'importe qui peut décoder du Base64 en une seconde. Ce n'est en aucun cas un moyen de protéger un mot de passe
  ou une information confidentielle. Pour cela, il faut utiliser un véritable algorithme de chiffrement.</p>

  <h2>Pourquoi le résultat est-il plus long ?</h2>
  <p>Le Base64 transforme chaque groupe de 3 octets en 4 caractères : la taille augmente donc d'environ 33 %. Les lettres
  accentuées occupent 2 octets en UTF-8, ce qui allonge encore un peu le résultat.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
