<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('convertisseur-casse');

$modes = [
    'upper'    => 'MAJUSCULES',
    'lower'    => 'minuscules',
    'title'    => 'Casse De Titre',
    'sentence' => 'Casse de phrase',
    'invert'   => 'iNVERSER lA cASSE',
    'noaccent' => 'Supprimer les accents',
];

function remove_accents(string $s): string
{
    if (class_exists('Normalizer')) {
        return preg_replace('/\p{Mn}+/u', '', Normalizer::normalize($s, Normalizer::FORM_D));
    }
    return strtr($s, ['à'=>'a','â'=>'a','ä'=>'a','á'=>'a','ã'=>'a','ç'=>'c','é'=>'e','è'=>'e','ê'=>'e','ë'=>'e','î'=>'i','ï'=>'i','í'=>'i',
        'ô'=>'o','ö'=>'o','ó'=>'o','õ'=>'o','ù'=>'u','û'=>'u','ü'=>'u','ú'=>'u','ÿ'=>'y','ñ'=>'n','À'=>'A','Â'=>'A','Ä'=>'A','Ç'=>'C',
        'É'=>'E','È'=>'E','Ê'=>'E','Ë'=>'E','Î'=>'I','Ï'=>'I','Ô'=>'O','Ö'=>'O','Ù'=>'U','Û'=>'U','Ü'=>'U','Ÿ'=>'Y','Ñ'=>'N']);
}

$text = post('texte');
$mode = post('mode', 'upper');
if (!isset($modes[$mode])) {
    $mode = 'upper';
}
$output = null;
if (is_post() && $text !== '') {
    switch ($mode) {
        case 'upper': $output = mb_strtoupper($text); break;
        case 'lower': $output = mb_strtolower($text); break;
        case 'title': $output = mb_convert_case($text, MB_CASE_TITLE); break;
        case 'sentence':
            $output = preg_replace_callback('/(^|[.!?…]\s+|\R\s*)(\p{L})/u',
                fn($m) => $m[1] . mb_strtoupper($m[2]), mb_strtolower($text));
            break;
        case 'invert':
            $output = implode('', array_map(
                fn($c) => mb_strtolower($c) === $c ? mb_strtoupper($c) : mb_strtolower($c),
                mb_str_split($text)));
            break;
        case 'noaccent': $output = remove_accents($text); break;
    }
}
include __DIR__ . '/../inc/header.php';
?>
<h1><?= e($tool['title']) ?></h1>
<p class="lead">Transformez un texte en majuscules, en minuscules, en casse de titre ou de phrase, ou retirez tous ses accents,
sans avoir à le retaper.</p>

<form method="post" class="tool">
  <label for="texte">Texte à convertir</label>
  <textarea id="texte" name="texte" placeholder="Collez votre texte ici…"><?= e($text) ?></textarea>
  <label>Conversion</label>
  <?php foreach ($modes as $k => $label): ?>
    <label class="inline"><input type="radio" name="mode" value="<?= $k ?>" <?= $k === $mode ? 'checked' : '' ?>> <?= e($label) ?></label>
  <?php endforeach; ?>
  <br><button type="submit">Convertir</button>
</form>

<?php if ($output !== null): ?>
<div class="result">
  <label for="sortie"><strong>Résultat</strong></label>
  <textarea id="sortie" readonly><?= e($output) ?></textarea>
  <button type="button" class="secondary" data-copy="#sortie">Copier le résultat</button>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Les différents types de casse</h2>
  <p>La « casse » désigne le fait d'écrire une lettre en majuscule (haut de casse) ou en minuscule (bas de casse). Le terme vient
  de l'imprimerie : les typographes rangeaient les caractères en plomb dans une casse, avec les capitales en haut et les
  minuscules en bas.</p>
  <ul>
    <li><strong>MAJUSCULES</strong> : idéal pour un titre d'affiche ou un nom de famille dans un formulaire administratif.</li>
    <li><strong>minuscules</strong> : pratique pour corriger un texte tapé par erreur avec la touche Verr. Maj activée.</li>
    <li><strong>Casse De Titre</strong> : chaque mot commence par une majuscule, un usage courant dans les titres en anglais.</li>
    <li><strong>Casse de phrase</strong> : seule la première lettre de chaque phrase est en majuscule, comme en français courant.</li>
    <li><strong>Inverser la casse</strong> : chaque majuscule devient minuscule et inversement.</li>
    <li><strong>Supprimer les accents</strong> : « Élève à l'école » devient « Eleve a l'ecole ». Utile pour créer un identifiant,
    un nom de fichier ou une adresse web.</li>
  </ul>

  <h2>Les accents sur les majuscules</h2>
  <p>En français, les majuscules prennent les accents : on écrit « ÉCOLE » et non « ECOLE ». L'Académie française rappelle
  que l'accent a une pleine valeur orthographique. Notre convertisseur conserve donc les accents lors du passage en majuscules,
  sauf si vous choisissez explicitement de les supprimer.</p>

  <h2>Questions fréquentes</h2>
  <h3>Les noms propres sont-ils préservés en casse de phrase ?</h3>
  <p>Non : l'outil ne peut pas deviner qu'un mot est un nom propre. Pensez à relire votre texte et à rétablir les majuscules
  des noms de personnes, de villes ou de marques.</p>
  <h3>Le texte est-il conservé ?</h3>
  <p>Non, la conversion est faite à la volée et rien n'est enregistré.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
