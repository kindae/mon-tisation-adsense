<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-moyenne');

$notes = is_post() ? (array) ($_POST['notes'] ?? []) : [];
$coefs = is_post() ? (array) ($_POST['coefs'] ?? []) : [];
$bareme = post_num('bareme') ?? 20.0;
$rows = max(6, count($notes));
$result = null;
$error = null;

if (is_post()) {
    $sum = 0; $sumCoef = 0; $count = 0;
    foreach ($notes as $i => $n) {
        $n = is_string($n) ? str_replace(',', '.', trim($n)) : '';
        if ($n === '') {
            continue;
        }
        $c = isset($coefs[$i]) && is_string($coefs[$i]) && trim($coefs[$i]) !== '' ? str_replace(',', '.', trim($coefs[$i])) : '1';
        if (!is_numeric($n) || !is_numeric($c) || $c < 0 || $n < 0 || $n > $bareme) {
            $error = 'Ligne ' . ($i + 1) . ' : la note doit être comprise entre 0 et ' . fmt($bareme) . ' et le coefficient positif.';
            break;
        }
        $sum += $n * $c; $sumCoef += $c; $count++;
    }
    if (!$error && $sumCoef == 0) {
        $error = 'Saisissez au moins une note avec un coefficient supérieur à 0.';
    }
    if (!$error) {
        $avg = $sum / $sumCoef;
        $sur20 = $avg / $bareme * 20;
        $mention = $sur20 >= 16 ? 'Très bien' : ($sur20 >= 14 ? 'Bien' : ($sur20 >= 12 ? 'Assez bien' : ($sur20 >= 10 ? 'Admis (sans mention)' : 'Inférieure à la moyenne')));
        $result = compact('avg', 'sur20', 'mention', 'count', 'sumCoef');
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Saisissez vos notes et leurs coefficients pour obtenir votre moyenne pondérée, sur 20 ou sur le barème
de votre choix. Laissez le coefficient vide pour une moyenne simple.</p>
  </div>
</div>

<form method="post" class="tool">
  <label for="bareme">Barème (note maximale)</label>
  <input type="text" inputmode="decimal" id="bareme" name="bareme" value="<?= e(fmt($bareme)) ?>" style="max-width:140px">
  <div class="table-scroll" style="margin-top:16px"><table id="lignes">
    <tr><th>#</th><th>Note</th><th>Coefficient</th></tr>
    <?php for ($i = 0; $i < $rows; $i++): ?>
    <tr><td><?= $i + 1 ?></td>
      <td><input type="text" inputmode="decimal" name="notes[]" value="<?= e(is_string($notes[$i] ?? null) ? $notes[$i] : '') ?>" aria-label="Note <?= $i + 1 ?>"></td>
      <td><input type="text" inputmode="decimal" name="coefs[]" value="<?= e(is_string($coefs[$i] ?? null) ? $coefs[$i] : '') ?>" placeholder="1" aria-label="Coefficient <?= $i + 1 ?>"></td></tr>
    <?php endfor; ?>
  </table></div>
  <button type="button" class="secondary" id="ajout">+ Ajouter une ligne</button>
  <button type="submit">Calculer la moyenne</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="result">
  Moyenne : <span class="big"><?= fmt($result['avg']) ?> / <?= fmt($bareme) ?></span><br>
  <?php if ($bareme != 20): ?>Soit <strong><?= fmt($result['sur20']) ?> / 20</strong>. <?php endif; ?>
  Mention équivalente : <strong><?= e($result['mention']) ?></strong> ·
  <?= $result['count'] ?> note<?= $result['count'] > 1 ? 's' : '' ?>, total des coefficients : <?= fmt($result['sumCoef']) ?>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Comment calculer une moyenne avec coefficients ?</h2>
  <p>On multiplie chaque note par son coefficient, on additionne ces produits, puis on divise par la somme des coefficients.</p>
  <p>Exemple : 12 (coef. 2), 15 (coef. 1) et 9 (coef. 3) donnent (12 × 2 + 15 × 1 + 9 × 3) ÷ (2 + 1 + 3) = 66 ÷ 6 =
  <strong>11 / 20</strong>. Sans coefficients, la moyenne simple serait de 12 : le 9, très coefficienté, fait baisser le résultat.</p>

  <h2>Convertir une note sur un autre barème</h2>
  <p>Pour ramener une note sur 20, on divise par le barème puis on multiplie par 20. Une note de 32/40 vaut 32 ÷ 40 × 20 =
  16/20. L'outil le fait automatiquement si vous choisissez un barème différent de 20.</p>

  <h2>Les mentions au baccalauréat</h2>
  <div class="table-scroll"><table>
    <tr><th>Moyenne sur 20</th><th>Mention</th></tr>
    <tr><td>16 et plus</td><td>Très bien</td></tr>
    <tr><td>14 à moins de 16</td><td>Bien</td></tr>
    <tr><td>12 à moins de 14</td><td>Assez bien</td></tr>
    <tr><td>10 à moins de 12</td><td>Admis sans mention</td></tr>
  </table></div>
  <p>Entre 8 et 10, le candidat peut passer l'épreuve de rattrapage. Les règles exactes (contrôle continu, options) évoluent
  selon les réformes : vérifiez-les auprès de votre établissement.</p>

  <h2>Questions fréquentes</h2>
  <h3>Un coefficient peut-il être décimal ?</h3>
  <p>Oui, par exemple 0,5 ou 1,5. À l'université, on utilise souvent les crédits ECTS comme coefficients.</p>
  <h3>Comment calculer la note nécessaire pour atteindre une moyenne ?</h3>
  <p>Ajoutez une ligne avec la note envisagée et le coefficient de la prochaine épreuve, puis ajustez la note jusqu'à obtenir
  la moyenne visée.</p>
</article>
<script>
document.getElementById('ajout').addEventListener('click', function () {
  var table = document.getElementById('lignes');
  var last = table.rows[table.rows.length - 1];
  var row = last.cloneNode(true);
  row.cells[0].textContent = table.rows.length;
  row.querySelectorAll('input').forEach(function (i) { i.value = ''; });
  last.parentNode.appendChild(row);
  row.querySelector('input').focus();
});
</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
