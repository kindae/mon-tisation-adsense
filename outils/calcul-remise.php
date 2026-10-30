<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-remise');

$mode = post('mode', 'prix');
$result = null;
$error = null;
if (is_post()) {
    $a = post_num('a');
    $b = post_num('b');
    $c = post_num('c'); // seconde remise facultative
    if ($a === null || $b === null || $a < 0 || $b < 0) {
        $error = 'Veuillez saisir des nombres positifs valides.';
    } elseif ($mode === 'prix') {
        if ($b > 100 || ($c !== null && ($c < 0 || $c > 100))) {
            $error = 'Une remise doit être comprise entre 0 et 100 %.';
        } else {
            $final = $a * (1 - $b / 100) * (1 - ($c ?? 0) / 100);
            $result = ['Prix après remise' => fmt($final) . ' €', 'Vous économisez' => fmt($a - $final) . ' €',
                'Remise totale réelle' => fmt(($a - $final) / ($a ?: 1) * 100) . ' %'];
        }
    } elseif ($mode === 'taux') {
        if ($a == 0) {
            $error = 'Le prix initial ne peut pas être égal à zéro.';
        } else {
            $result = ['Remise appliquée' => fmt(($a - $b) / $a * 100) . ' %', 'Économie' => fmt($a - $b) . ' €'];
        }
    } elseif ($mode === 'initial') {
        if ($b >= 100) {
            $error = 'La remise doit être inférieure à 100 %.';
        } else {
            $initial = $a / (1 - $b / 100);
            $result = ['Prix avant remise' => fmt($initial) . ' €', 'Montant de la remise' => fmt($initial - $a) . ' €'];
        }
    }
}

$forms = [
    'prix'    => ['Prix après remise', 'Prix initial (€)', 'Remise (%)', true],
    'taux'    => ['Quel pourcentage de remise ?', 'Prix initial (€)', 'Prix soldé (€)', false],
    'initial' => ['Retrouver le prix avant remise', 'Prix soldé (€)', 'Remise appliquée (%)', false],
];
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Calculez le prix d'un article soldé, le pourcentage de réduction réellement appliqué ou le prix d'origine
d'un produit. Idéal pendant les soldes, le Black Friday ou pour vérifier une promotion.</p>
  </div>
</div>

<nav class="tabs">
  <?php foreach ($forms as $k => [$label]): ?>
    <a href="?mode=<?= $k ?>" class="<?= $k === ($_GET['mode'] ?? $mode) ? 'active' : '' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</nav>
<?php
$current = is_post() ? $mode : (isset($forms[$_GET['mode'] ?? '']) ? $_GET['mode'] : 'prix');
[$title, $la, $lb, $double] = $forms[$current];
?>
<form method="post" action="?mode=<?= $current ?>" class="tool">
  <h2><?= e($title) ?></h2>
  <input type="hidden" name="mode" value="<?= $current ?>">
  <div class="row">
    <div><label for="a"><?= e($la) ?></label><input type="text" inputmode="decimal" id="a" name="a" value="<?= e(post('a')) ?>" required></div>
    <div><label for="b"><?= e($lb) ?></label><input type="text" inputmode="decimal" id="b" name="b" value="<?= e(post('b')) ?>" required></div>
    <?php if ($double): ?>
    <div><label for="c">Seconde remise (%) <small class="muted">facultatif</small></label><input type="text" inputmode="decimal" id="c" name="c" value="<?= e(post('c')) ?>" placeholder="ex. 10"></div>
    <?php endif; ?>
  </div>
  <button type="submit">Calculer</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="stats">
  <?php foreach ($result as $label => $v): ?><div class="stat"><b><?= e($v) ?></b><?= e($label) ?></div><?php endforeach; ?>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Comment calculer un prix soldé ?</h2>
  <p>Pour appliquer une remise de X %, on multiplie le prix par (1 − X ÷ 100). Un article à 80 € soldé à −25 % coûte
  80 × 0,75 = <strong>60 €</strong>, soit 20 € d'économie.</p>

  <h2>Les remises successives ne s'additionnent pas</h2>
  <p>Une « remise supplémentaire de 10 % sur les prix déjà soldés à −30 % » ne fait pas −40 %. La seconde remise s'applique
  sur le prix déjà réduit : 100 € × 0,70 × 0,90 = 63 €, soit une remise totale réelle de <strong>37 %</strong>.
  Le champ « Seconde remise » vous donne directement ce taux réel.</p>

  <h2>Retrouver le pourcentage de réduction</h2>
  <p>Remise (%) = (prix initial − prix soldé) ÷ prix initial × 100. Un produit passé de 120 € à 90 € bénéficie d'une
  remise de 30 ÷ 120 × 100 = <strong>25 %</strong>.</p>

  <h2>Retrouver le prix d'origine</h2>
  <p>On divise le prix soldé par (1 − remise ÷ 100). Un article affiché 56 € après −30 % valait 56 ÷ 0,70 = <strong>80 €</strong>.
  Attention à l'erreur fréquente qui consiste à ajouter 30 % à 56 € (ce qui donnerait 72,80 €).</p>

  <h2>Bon à savoir sur les soldes en France</h2>
  <p>Les soldes d'hiver et d'été durent quatre semaines chacune. Le prix de référence affiché barré doit correspondre au prix
  le plus bas pratiqué par le vendeur au cours des 30 jours précédant la réduction : une règle qui protège contre les
  hausses de prix artificielles juste avant une promotion.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
