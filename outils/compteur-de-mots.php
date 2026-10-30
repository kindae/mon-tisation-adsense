<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('compteur-de-mots');

$text = post('texte');
$stats = null;
if (is_post() && $text !== '') {
    preg_match_all("/[\p{L}\p{N}]+(?:['’-][\p{L}\p{N}]+)*/u", $text, $m);
    $words = $m[0];
    $nbWords = count($words);
    $sentences = preg_split('/[.!?…]+(?=\s|$)/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    $sentences = array_filter($sentences, fn($s) => preg_match('/[\p{L}\p{N}]/u', $s));
    $paragraphs = array_filter(preg_split('/\R\s*\R/u', $text), fn($p) => trim($p) !== '');

    $freq = [];
    foreach ($words as $w) {
        $w = mb_strtolower($w);
        if (mb_strlen($w) >= 4) {
            $freq[$w] = ($freq[$w] ?? 0) + 1;
        }
    }
    arsort($freq);

    $stats = [
        'words'      => $nbWords,
        'chars'      => mb_strlen($text),
        'chars_ns'   => mb_strlen(preg_replace('/\s+/u', '', $text)),
        'sentences'  => count($sentences),
        'paragraphs' => count($paragraphs),
        'reading'    => max(1, (int) ceil($nbWords / 230)),
        'speaking'   => max(1, (int) ceil($nbWords / 130)),
        'top'        => array_slice($freq, 0, 10, true),
    ];
}
include __DIR__ . '/../inc/header.php';
?>
<h1><?= e($tool['title']) ?></h1>
<p class="lead">Collez votre texte ci-dessous pour connaître instantanément son nombre de mots, de caractères (avec et sans espaces),
de phrases et de paragraphes, ainsi que son temps de lecture estimé.</p>

<form method="post" class="tool">
  <label for="texte">Votre texte</label>
  <textarea id="texte" name="texte" placeholder="Collez ou tapez votre texte ici…"><?= e($text) ?></textarea>
  <button type="submit">Compter</button>
</form>

<?php if ($stats): ?>
<div class="stats">
  <div class="stat"><b><?= fmt($stats['words'], 0) ?></b>mots</div>
  <div class="stat"><b><?= fmt($stats['chars'], 0) ?></b>caractères</div>
  <div class="stat"><b><?= fmt($stats['chars_ns'], 0) ?></b>caractères sans espaces</div>
  <div class="stat"><b><?= fmt($stats['sentences'], 0) ?></b>phrases</div>
  <div class="stat"><b><?= fmt($stats['paragraphs'], 0) ?></b>paragraphes</div>
  <div class="stat"><b><?= $stats['reading'] ?> min</b>de lecture</div>
  <div class="stat"><b><?= $stats['speaking'] ?> min</b>à l'oral</div>
</div>
<?php if ($stats['top']): ?>
<h2>Mots les plus fréquents</h2>
<div class="table-scroll"><table>
  <tr><th>Mot</th><th>Occurrences</th><th>Densité</th></tr>
  <?php foreach ($stats['top'] as $w => $n): ?>
  <tr><td><?= e($w) ?></td><td><?= $n ?></td><td><?= fmt($n / $stats['words'] * 100, 1) ?> %</td></tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>À quoi sert un compteur de mots ?</h2>
  <p>De nombreux textes doivent respecter une longueur précise : une dissertation de 1 500 mots, une lettre de motivation
  d'une page, une méta-description de 155 caractères, un tweet de 280 caractères ou un résumé d'article scientifique de
  250 mots. Compter à la main est long et source d'erreurs : cet outil le fait pour vous en une fraction de seconde.</p>

  <h2>Comment sont comptés les mots ?</h2>
  <p>Un mot est une suite de lettres ou de chiffres. Les mots composés reliés par un trait d'union (« porte-monnaie ») et les
  mots élidés avec une apostrophe (« l'outil », « aujourd'hui ») comptent pour un seul mot. La ponctuation et les espaces
  multiples sont ignorés. Les caractères, eux, incluent tout : lettres, chiffres, ponctuation, espaces et retours à la ligne.</p>

  <h2>Temps de lecture et de parole</h2>
  <p>Le temps de lecture est estimé sur une base de 230 mots par minute, la vitesse moyenne d'un adulte lisant en silence.
  Le temps de parole repose sur 130 mots par minute, un rythme confortable pour un discours ou une présentation orale.
  Ces valeurs sont des moyennes : un texte technique se lit plus lentement qu'un roman.</p>

  <h2>Mots les plus fréquents</h2>
  <p>Le tableau des mots les plus fréquents (de 4 lettres et plus) vous aide à repérer les répétitions à éviter dans un
  texte littéraire, ou à vérifier la présence d'un mot-clé dans un article destiné au web. Une densité supérieure à 3 ou 4 %
  pour un même mot donne souvent une impression de répétition.</p>

  <h2>Questions fréquentes</h2>
  <h3>Mon texte est-il enregistré ?</h3>
  <p>Non. Le texte est analysé au moment où vous cliquez sur « Compter », puis il est oublié. Il n'est stocké nulle part.</p>
  <h3>Y a-t-il une limite de longueur ?</h3>
  <p>Vous pouvez analyser des textes de plusieurs dizaines de milliers de mots sans problème.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
