<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('generateur-mot-de-passe');

$sets = [
    'lower'   => ['abcdefghijkmnopqrstuvwxyz', 'Minuscules (a-z)'],
    'upper'   => ['ABCDEFGHJKLMNPQRSTUVWXYZ', 'Majuscules (A-Z)'],
    'digits'  => ['23456789', 'Chiffres (0-9)'],
    'symbols' => ['!@#$%&*?-_+=.:;', 'Symboles (!@#…)'],
];
$length = 16;
$chosen = array_keys($sets);
$passwords = [];
$error = null;

if (is_post()) {
    $length = (int) post('longueur', '16');
    $length = max(6, min(64, $length));
    $chosen = array_values(array_intersect(array_keys($sets), (array) ($_POST['jeux'] ?? [])));
    if (!$chosen) {
        $error = 'Cochez au moins un type de caractères.';
    } else {
        $pool = implode('', array_map(fn($k) => $sets[$k][0], $chosen));
        for ($n = 0; $n < 5; $n++) {
            // Au moins un caractère de chaque type choisi, le reste au hasard, puis mélange.
            $chars = array_map(fn($k) => $sets[$k][0][random_int(0, strlen($sets[$k][0]) - 1)], $chosen);
            while (count($chars) < $length) {
                $chars[] = $pool[random_int(0, strlen($pool) - 1)];
            }
            for ($i = count($chars) - 1; $i > 0; $i--) {
                $j = random_int(0, $i);
                [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
            }
            $passwords[] = implode('', $chars);
        }
        $entropy = $length * log(strlen($pool), 2);
        $strength = $entropy >= 100 ? 'Excellente' : ($entropy >= 75 ? 'Très forte' : ($entropy >= 55 ? 'Correcte' : 'Faible'));
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Générez des mots de passe aléatoires et robustes en un clic. Le générateur utilise une source d'aléa
cryptographique et les mots de passe ne sont jamais enregistrés.</p>
  </div>
</div>

<form method="post" class="tool">
  <label for="longueur">Longueur : <?= $length ?> caractères (6 à 64)</label>
  <input type="number" id="longueur" name="longueur" min="6" max="64" value="<?= $length ?>">
  <label>Caractères à inclure</label>
  <?php foreach ($sets as $k => [$chars, $label]): ?>
    <label class="inline"><input type="checkbox" name="jeux[]" value="<?= $k ?>" <?= in_array($k, $chosen, true) ? 'checked' : '' ?>> <?= e($label) ?></label>
  <?php endforeach; ?>
  <br><button type="submit">Générer des mots de passe</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($passwords): ?>
<div class="result">
  <p>Robustesse : <strong><?= $strength ?></strong> (environ <?= fmt($entropy, 0) ?> bits d'entropie)</p>
  <?php foreach ($passwords as $i => $p): ?>
    <p><code class="big" id="mdp<?= $i ?>"><?= e($p) ?></code>
    <button type="button" class="secondary" data-copy="#mdp<?= $i ?>">Copier</button></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Qu'est-ce qu'un mot de passe fort ?</h2>
  <p>Un mot de passe fort est d'abord <strong>long</strong> et <strong>imprévisible</strong>. La Commission nationale de
  l'informatique et des libertés (CNIL) recommande au moins 12 caractères mélangeant majuscules, minuscules, chiffres et
  caractères spéciaux, ou au moins 14 caractères s'il ne contient que des lettres et des chiffres. Évitez les mots du
  dictionnaire, les dates de naissance, les prénoms et les suites logiques comme « azerty » ou « 123456 ».</p>

  <h2>Comment fonctionne ce générateur ?</h2>
  <p>Chaque caractère est tiré au sort avec la fonction <code>random_int()</code> de PHP, qui s'appuie sur le générateur
  aléatoire cryptographiquement sûr du système. L'outil garantit qu'au moins un caractère de chaque type coché est présent,
  puis mélange le tout. Les caractères ambigus comme 0/O ou l/1/I sont exclus pour éviter les erreurs de recopie.</p>

  <h2>Que signifie l'entropie ?</h2>
  <p>L'entropie mesure, en bits, le nombre d'essais nécessaires pour deviner le mot de passe par force brute. Chaque bit
  supplémentaire double ce nombre. Au-delà de 75 bits, un mot de passe aléatoire est hors de portée des attaques actuelles ;
  à 100 bits, il est considéré comme excellent.</p>

  <h2>Bonnes pratiques</h2>
  <ul>
    <li>Utilisez un mot de passe <strong>différent pour chaque site</strong> : une fuite sur un site ne compromettra pas les autres.</li>
    <li>Stockez-les dans un <strong>gestionnaire de mots de passe</strong> plutôt que sur un papier ou dans un fichier texte.</li>
    <li>Activez la <strong>double authentification</strong> partout où elle est proposée, en particulier pour votre messagerie.</li>
    <li>Changez immédiatement un mot de passe si le service concerné annonce une fuite de données.</li>
  </ul>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
