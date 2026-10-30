<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-pourcentage');

$mode = post('mode');
$a = post_num('a');
$b = post_num('b');
$result = null;
$error = null;
if (is_post()) {
    if ($a === null || $b === null) {
        $error = 'Veuillez saisir deux nombres valides.';
    } elseif ($mode === 'part') {
        $result = ['part', fmt($a) . ' % de ' . fmt($b) . ' = ', fmt($a * $b / 100, 4)];
    } elseif ($mode === 'ratio') {
        $result = $b == 0 ? null : ['ratio', fmt($a) . ' représente ', fmt($a / $b * 100, 2) . ' % de ' . fmt($b)];
        $error = $b == 0 ? 'Le total ne peut pas être égal à zéro.' : null;
    } elseif ($mode === 'evol') {
        $result = $a == 0 ? null : ['evol', 'De ' . fmt($a) . ' à ' . fmt($b) . ' : ', (($b - $a) >= 0 ? '+' : '') . fmt(($b - $a) / abs($a) * 100, 2) . ' %'];
        $error = $a == 0 ? 'La valeur de départ ne peut pas être égale à zéro.' : null;
    }
}

function pct_form(string $mode, string $title, string $labelA, string $labelB, ?array $result, string $current): void
{
    $va = $current === $mode ? post('a') : '';
    $vb = $current === $mode ? post('b') : '';
    ?>
    <form method="post" class="tool">
      <h2 style="margin-top:0"><?= e($title) ?></h2>
      <input type="hidden" name="mode" value="<?= $mode ?>">
      <div class="row">
        <div><label for="<?= $mode ?>a"><?= e($labelA) ?></label><input type="text" inputmode="decimal" id="<?= $mode ?>a" name="a" value="<?= e($va) ?>" required></div>
        <div><label for="<?= $mode ?>b"><?= e($labelB) ?></label><input type="text" inputmode="decimal" id="<?= $mode ?>b" name="b" value="<?= e($vb) ?>" required></div>
      </div>
      <button type="submit">Calculer</button>
      <?php if ($result && $result[0] === $mode): ?>
        <div class="result"><?= e($result[1]) ?><span class="big"><?= e($result[2]) ?></span></div>
      <?php endif; ?>
    </form>
    <?php
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Trois calculateurs pour répondre aux questions de pourcentage les plus courantes : combien font X % d'un nombre,
quelle part représente une valeur, et de combien une valeur a augmenté ou diminué.</p>
  </div>
</div>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php pct_form('part', 'Combien font X % de Y ?', 'Pourcentage X (%)', 'Valeur Y', $result, $mode); ?>
<?php pct_form('ratio', 'X représente quel pourcentage de Y ?', 'Valeur X', 'Total Y', $result, $mode); ?>
<?php pct_form('evol', 'Quelle est l\'évolution en % de X à Y ?', 'Valeur de départ X', 'Valeur d\'arrivée Y', $result, $mode); ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Les formules du pourcentage</h2>
  <h3>1. Calculer X % d'un nombre</h3>
  <p>On multiplie le nombre par le pourcentage, puis on divise par 100. Exemple : 15 % de 80 = 80 × 15 ÷ 100 = <strong>12</strong>.
  C'est le calcul à faire pour connaître le montant d'une remise ou d'un pourboire.</p>
  <h3>2. Calculer la part d'une valeur dans un total</h3>
  <p>On divise la valeur par le total, puis on multiplie par 100. Exemple : 18 bonnes réponses sur 24 questions =
  18 ÷ 24 × 100 = <strong>75 %</strong>.</p>
  <h3>3. Calculer un taux d'évolution</h3>
  <p>On calcule (valeur d'arrivée − valeur de départ) ÷ valeur de départ × 100. Exemple : un loyer qui passe de 700 € à
  735 € augmente de (735 − 700) ÷ 700 × 100 = <strong>+5 %</strong>. Un résultat négatif indique une baisse.</p>

  <h2>Pièges classiques</h2>
  <p><strong>Une hausse puis une baisse du même pourcentage ne s'annulent pas.</strong> Un prix de 100 € augmenté de 20 %
  passe à 120 € ; s'il baisse ensuite de 20 %, il retombe à 96 €, et non à 100 €. En effet, la baisse s'applique sur 120 €.</p>
  <p><strong>Points de pourcentage et pourcentages sont différents.</strong> Si un taux d'intérêt passe de 2 % à 3 %, il
  augmente d'un point de pourcentage, mais de 50 % en valeur relative.</p>

  <h2>Retrouver un prix avant remise</h2>
  <p>Pour retrouver le prix initial d'un article soldé à −30 % et affiché 56 €, on divise par le coefficient (1 − 0,30) :
  56 ÷ 0,70 = 80 €. Retirer 30 % de 56 € donnerait un résultat faux.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
