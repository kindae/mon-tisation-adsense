<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('convertisseur-unites');

// Facteurs vers l'unité de base de chaque catégorie (mètre, kilogramme, litre).
$categories = [
    'longueur' => ['Longueur', [
        'mm' => ['Millimètre (mm)', 0.001], 'cm' => ['Centimètre (cm)', 0.01], 'm' => ['Mètre (m)', 1], 'km' => ['Kilomètre (km)', 1000],
        'in' => ['Pouce (in)', 0.0254], 'ft' => ['Pied (ft)', 0.3048], 'yd' => ['Yard (yd)', 0.9144], 'mi' => ['Mile (mi)', 1609.344],
        'nmi' => ['Mille marin', 1852],
    ]],
    'masse' => ['Masse', [
        'mg' => ['Milligramme (mg)', 0.000001], 'g' => ['Gramme (g)', 0.001], 'kg' => ['Kilogramme (kg)', 1], 't' => ['Tonne (t)', 1000],
        'oz' => ['Once (oz)', 0.028349523125], 'lb' => ['Livre anglaise (lb)', 0.45359237], 'st' => ['Stone (st)', 6.35029318],
    ]],
    'volume' => ['Volume', [
        'ml' => ['Millilitre (ml)', 0.001], 'cl' => ['Centilitre (cl)', 0.01], 'l' => ['Litre (l)', 1], 'm3' => ['Mètre cube (m³)', 1000],
        'tsp' => ['Cuillère à café (5 ml)', 0.005], 'tbsp' => ['Cuillère à soupe (15 ml)', 0.015], 'cup' => ['Tasse US (cup)', 0.2365882365],
        'floz' => ['Once liquide US (fl oz)', 0.0295735295625], 'galus' => ['Gallon US', 3.785411784], 'galuk' => ['Gallon impérial', 4.54609],
    ]],
    'temperature' => ['Température', [
        'c' => ['Degré Celsius (°C)', null], 'f' => ['Degré Fahrenheit (°F)', null], 'k' => ['Kelvin (K)', null],
    ]],
];

$cat = $_GET['cat'] ?? post('cat', 'longueur');
if (!is_string($cat) || !isset($categories[$cat])) {
    $cat = 'longueur';
}
[$catLabel, $units] = $categories[$cat];
$keys = array_keys($units);
$from = post('de', $keys[0]);
$to = post('vers', $keys[1]);
$value = post_num('valeur');
$result = null;
$error = null;

function to_celsius(float $v, string $u): float
{
    return $u === 'f' ? ($v - 32) * 5 / 9 : ($u === 'k' ? $v - 273.15 : $v);
}
function from_celsius(float $c, string $u): float
{
    return $u === 'f' ? $c * 9 / 5 + 32 : ($u === 'k' ? $c + 273.15 : $c);
}

if (is_post()) {
    if (!isset($units[$from], $units[$to])) {
        $error = 'Unités invalides.';
    } elseif ($value === null) {
        $error = 'Veuillez saisir une valeur numérique.';
    } else {
        $converted = $cat === 'temperature'
            ? from_celsius(to_celsius($value, $from), $to)
            : $value * $units[$from][1] / $units[$to][1];
        $result = fmt($value, 6) . ' ' . $units[$from][0] . ' = ';
        $resultValue = fmt($converted, abs($converted) < 0.01 && $converted != 0 ? 10 : 6) . ' ' . $units[$to][0];
    }
}
$page = ['path' => 'outils/convertisseur-unites.php'];
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Convertissez rapidement des longueurs, des masses, des volumes et des températures entre le système métrique
et les unités anglo-saxonnes.</p>
  </div>
</div>

<nav class="tabs">
  <?php foreach ($categories as $k => [$label]): ?>
    <a href="?cat=<?= $k ?>" class="<?= $k === $cat ? 'active' : '' ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</nav>

<form method="post" action="?cat=<?= $cat ?>" class="tool">
  <input type="hidden" name="cat" value="<?= $cat ?>">
  <div class="row">
    <div><label for="valeur">Valeur</label>
      <input type="text" inputmode="decimal" id="valeur" name="valeur" value="<?= e(post('valeur', '1')) ?>" required></div>
    <div><label for="de">De</label>
      <select id="de" name="de"><?php foreach ($units as $k => [$label]): ?><option value="<?= $k ?>" <?= $k === $from ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
    <div><label for="vers">Vers</label>
      <select id="vers" name="vers"><?php foreach ($units as $k => [$label]): ?><option value="<?= $k ?>" <?= $k === $to ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?></select></div>
  </div>
  <button type="submit">Convertir</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?><div class="result"><?= e($result) ?><br><span class="big"><?= e($resultValue) ?></span></div><?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Les conversions les plus utiles</h2>
  <div class="table-scroll"><table>
    <tr><th>Conversion</th><th>Équivalence</th></tr>
    <tr><td>1 pouce</td><td>2,54 cm</td></tr>
    <tr><td>1 pied</td><td>30,48 cm</td></tr>
    <tr><td>1 mile</td><td>1,609 km</td></tr>
    <tr><td>1 livre (lb)</td><td>453,6 g</td></tr>
    <tr><td>1 once (oz)</td><td>28,35 g</td></tr>
    <tr><td>1 gallon US</td><td>3,785 litres</td></tr>
    <tr><td>1 tasse US (cup)</td><td>environ 237 ml</td></tr>
  </table></div>

  <h2>Convertir les températures</h2>
  <p>Contrairement aux autres unités, les échelles de température ne sont pas simplement proportionnelles : elles n'ont pas
  le même zéro.</p>
  <ul>
    <li>°F = °C × 9 ÷ 5 + 32 (l'eau gèle à 32 °F et bout à 212 °F) ;</li>
    <li>°C = (°F − 32) × 5 ÷ 9 ;</li>
    <li>K = °C + 273,15 (0 K est le zéro absolu, la température la plus basse possible).</li>
  </ul>
  <p>Astuce de tête : pour passer approximativement des °F aux °C, retirez 30 et divisez par 2. 80 °F ≈ (80 − 30) ÷ 2 = 25 °C
  (valeur exacte : 26,7 °C).</p>

  <h2>Système métrique et système impérial</h2>
  <p>Le système métrique, né en France à la fin du XVIII<sup>e</sup> siècle, est aujourd'hui utilisé par la quasi-totalité des
  pays. Les États-Unis emploient encore couramment les unités dites « usuelles » (pouces, pieds, livres, gallons), tout
  comme le Royaume-Uni pour certains usages (miles sur les panneaux routiers, pintes dans les pubs). Attention : le gallon
  américain (3,785 l) et le gallon impérial britannique (4,546 l) sont différents.</p>

  <h2>Cuisine : cuillères et tasses</h2>
  <p>Les recettes anglo-saxonnes utilisent des mesures en volume. Une cuillère à soupe correspond à 15 ml, une cuillère à café
  à 5 ml et une « cup » américaine à environ 237 ml. Pour la farine, une tasse pèse environ 120 à 130 g, et pour le sucre
  environ 200 g, car la masse dépend de la densité de l'ingrédient.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
