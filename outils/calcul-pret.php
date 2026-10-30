<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-pret');

$capital = post_num('capital');
$taux = post_num('taux');
$duree = post_num('duree');
$result = null;
$error = null;

if (is_post()) {
    if ($capital === null || $capital <= 0) {
        $error = 'Veuillez saisir un montant emprunté valide.';
    } elseif ($taux === null || $taux < 0 || $taux > 30) {
        $error = 'Veuillez saisir un taux annuel entre 0 et 30 %.';
    } elseif ($duree === null || $duree <= 0 || $duree > 40) {
        $error = 'Veuillez saisir une durée entre 1 et 40 ans.';
    } else {
        $n = (int) round($duree * 12);
        $t = $taux / 100 / 12;
        $mensualite = $t > 0 ? $capital * $t / (1 - (1 + $t) ** -$n) : $capital / $n;

        $years = [];
        $restant = $capital;
        for ($m = 1; $m <= $n; $m++) {
            $interets = $restant * $t;
            $amorti = $mensualite - $interets;
            $restant = max(0, $restant - $amorti);
            $y = (int) ceil($m / 12);
            $years[$y] ??= ['interets' => 0, 'capital' => 0];
            $years[$y]['interets'] += $interets;
            $years[$y]['capital'] += $amorti;
            $years[$y]['restant'] = $restant;
        }
        $result = ['mensualite' => $mensualite, 'total' => $mensualite * $n, 'cout' => $mensualite * $n - $capital, 'years' => $years];
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Estimez la mensualité d'un crédit immobilier, auto ou à la consommation, son coût total en intérêts et
l'évolution du capital restant dû, année par année.</p>
  </div>
</div>

<form method="post" class="tool">
  <div class="row">
    <div><label for="capital">Montant emprunté (€)</label>
      <input type="text" inputmode="decimal" id="capital" name="capital" value="<?= e(post('capital')) ?>" placeholder="200000" required></div>
    <div><label for="taux">Taux annuel (%)</label>
      <input type="text" inputmode="decimal" id="taux" name="taux" value="<?= e(post('taux')) ?>" placeholder="3,5" required></div>
    <div><label for="duree">Durée (années)</label>
      <input type="text" inputmode="decimal" id="duree" name="duree" value="<?= e(post('duree')) ?>" placeholder="20" required></div>
  </div>
  <button type="submit">Calculer la mensualité</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="stats">
  <div class="stat"><b><?= fmt($result['mensualite']) ?> €</b>Mensualité (hors assurance)</div>
  <div class="stat"><b><?= fmt($result['cout']) ?> €</b>Coût total des intérêts</div>
  <div class="stat"><b><?= fmt($result['total']) ?> €</b>Montant total remboursé</div>
</div>
<h2>Tableau d'amortissement annuel</h2>
<div class="table-scroll"><table>
  <tr><th>Année</th><th>Capital remboursé</th><th>Intérêts payés</th><th>Capital restant dû</th></tr>
  <?php foreach ($result['years'] as $y => $row): ?>
  <tr><td><?= $y ?></td><td><?= fmt($row['capital']) ?> €</td><td><?= fmt($row['interets']) ?> €</td><td><?= fmt($row['restant']) ?> €</td></tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Comment est calculée la mensualité ?</h2>
  <p>Ce simulateur utilise la formule des prêts à mensualités constantes, la plus répandue en France :</p>
  <p><strong>M = C × t ÷ (1 − (1 + t)<sup>−n</sup>)</strong></p>
  <p>où <em>C</em> est le capital emprunté, <em>t</em> le taux mensuel (taux annuel ÷ 12) et <em>n</em> le nombre de
  mensualités. Par exemple, 200 000 € empruntés sur 20 ans à 3,5 % donnent une mensualité d'environ 1 160 €, et un coût
  total des intérêts d'environ 78 000 €.</p>

  <h2>Comprendre le tableau d'amortissement</h2>
  <p>Chaque mensualité se compose d'une part d'intérêts et d'une part de capital remboursé. En début de prêt, le capital
  restant dû est élevé : les intérêts représentent donc une grande partie de la mensualité. Au fil du temps, la part
  d'intérêts diminue et la part de capital augmente. C'est pourquoi un remboursement anticipé est plus intéressant dans
  les premières années.</p>

  <h2>Ce que ce calcul n'inclut pas</h2>
  <p>La mensualité affichée est hors assurance emprunteur, frais de dossier et frais de garantie. Pour comparer des offres,
  référez-vous au <strong>TAEG</strong> (taux annuel effectif global), que la banque est tenue de communiquer et qui intègre
  l'ensemble de ces coûts.</p>

  <h2>Le taux d'endettement</h2>
  <p>En France, le Haut Conseil de stabilité financière recommande que les mensualités de crédit (assurance comprise) ne
  dépassent pas 35 % des revenus nets de l'emprunteur. Avec 3 500 € de revenus mensuels, la mensualité maximale serait donc
  d'environ 1 225 €.</p>
  <p class="muted">Simulation fournie à titre indicatif : elle ne constitue pas une offre de prêt.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
