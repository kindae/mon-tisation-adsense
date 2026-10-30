<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('convertisseur-couleurs');

function rgb_to_hsl(int $r, int $g, int $b): array
{
    $r /= 255; $g /= 255; $b /= 255;
    $max = max($r, $g, $b); $min = min($r, $g, $b);
    $l = ($max + $min) / 2;
    $h = $s = 0;
    if ($max !== $min) {
        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);
        $h = $max === $r ? ($g - $b) / $d + ($g < $b ? 6 : 0) : ($max === $g ? ($b - $r) / $d + 2 : ($r - $g) / $d + 4);
        $h *= 60;
    }
    return [round($h), round($s * 100), round($l * 100)];
}

function hsl_to_rgb(float $h, float $s, float $l): array
{
    $s /= 100; $l /= 100;
    $c = (1 - abs(2 * $l - 1)) * $s;
    $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
    $m = $l - $c / 2;
    [$r, $g, $b] = $h < 60 ? [$c, $x, 0] : ($h < 120 ? [$x, $c, 0] : ($h < 180 ? [0, $c, $x] : ($h < 240 ? [0, $x, $c] : ($h < 300 ? [$x, 0, $c] : [$c, 0, $x]))));
    return [(int) round(($r + $m) * 255), (int) round(($g + $m) * 255), (int) round(($b + $m) * 255)];
}

function hex(array $rgb): string
{
    return sprintf('#%02x%02x%02x', ...$rgb);
}

function luminance(array $rgb): float
{
    $c = array_map(function ($v) { $v /= 255; return $v <= 0.03928 ? $v / 12.92 : (($v + 0.055) / 1.055) ** 2.4; }, $rgb);
    return 0.2126 * $c[0] + 0.7152 * $c[1] + 0.0722 * $c[2];
}

function contrast(array $a, array $b): float
{
    [$l1, $l2] = [luminance($a), luminance($b)];
    return (max($l1, $l2) + 0.05) / (min($l1, $l2) + 0.05);
}

/** Lit une couleur HEX, rgb() ou hsl(). */
function parse_color(string $s): ?array
{
    $s = strtolower(trim($s));
    if (preg_match('/^#?([0-9a-f]{3}|[0-9a-f]{6})$/', $s, $m)) {
        $h = strlen($m[1]) === 3 ? preg_replace('/(.)/', '$1$1', $m[1]) : $m[1];
        return [hexdec(substr($h, 0, 2)), hexdec(substr($h, 2, 2)), hexdec(substr($h, 4, 2))];
    }
    if (preg_match('/^(?:rgb|rvb)a?\(?\s*(\d{1,3})[\s,]+(\d{1,3})[\s,]+(\d{1,3})/', $s, $m)) {
        $rgb = [(int) $m[1], (int) $m[2], (int) $m[3]];
        return max($rgb) <= 255 ? $rgb : null;
    }
    if (preg_match('/^(?:hsl|tsl)a?\(?\s*(\d{1,3}(?:\.\d+)?)(?:deg)?[\s,]+(\d{1,3}(?:\.\d+)?)%?[\s,]+(\d{1,3}(?:\.\d+)?)%?/', $s, $m)) {
        return $m[1] <= 360 && $m[2] <= 100 && $m[3] <= 100 ? hsl_to_rgb(fmod((float) $m[1], 360), (float) $m[2], (float) $m[3]) : null;
    }
    return null;
}

$saisie = post('couleur', '#2563eb');
$rgb = parse_color($saisie);
$error = is_post() && !$rgb ? 'Format non reconnu. Exemples acceptés : #2563eb, 2563eb, rgb(37, 99, 235), hsl(221, 83%, 53%).' : null;
$rgb ??= [37, 99, 235];
[$h, $s, $l] = rgb_to_hsl(...$rgb);
$k = 1 - max($rgb) / 255;
$cmjn = $k >= 1 ? [0, 0, 0, 100] : array_map(fn($v) => round((1 - $v / 255 - $k) / (1 - $k) * 100), $rgb) + [3 => round($k * 100)];
$nuances = [];
foreach ([95, 85, 75, 65, 55, 45, 35, 25, 15] as $lum) {
    $nuances[] = hex(hsl_to_rgb($h, $s, $lum));
}
$cWhite = contrast($rgb, [255, 255, 255]);
$cBlack = contrast($rgb, [0, 0, 0]);
$formats = [
    'HEX' => strtoupper(hex($rgb)),
    'RVB (RGB)' => "rgb({$rgb[0]}, {$rgb[1]}, {$rgb[2]})",
    'TSL (HSL)' => "hsl({$h}, {$s}%, {$l}%)",
    'CMJN (CMYK)' => "cmyk({$cmjn[0]}%, {$cmjn[1]}%, {$cmjn[2]}%, {$cmjn[3]}%)",
];
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Saisissez une couleur en HEX, RVB ou TSL, ou choisissez-la dans la palette : obtenez instantanément toutes ses
conversions, ses nuances et son contraste avec le blanc et le noir.</p>
  </div>
</div>

<form method="post" class="tool" id="form-couleur">
  <label for="couleur">Couleur</label>
  <div style="display:flex; gap:10px; align-items:center">
    <input type="color" id="picker" value="<?= e(hex($rgb)) ?>" aria-label="Choisir une couleur">
    <input type="text" id="couleur" name="couleur" value="<?= e($saisie) ?>" placeholder="#2563eb ou rgb(37, 99, 235)">
  </div>
  <button type="submit">Convertir</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<div class="result color-result">
  <div class="color-preview" style=" background: <?= e(hex($rgb)) ?>"></div>
  <div>
    <?php $i = 0; foreach ($formats as $label => $val): $i++; ?>
      <div style="display:flex; justify-content:space-between; gap:8px; align-items:center; padding:4px 0">
        <span><strong><?= $label ?></strong> : <code id="f<?= $i ?>"><?= e($val) ?></code></span>
        <button type="button" class="secondary btn small" style="margin:0" data-copy="#f<?= $i ?>">Copier</button>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<h2>Nuances</h2>
<div class="swatches">
  <?php foreach ($nuances as $n): $c = parse_color($n); ?>
    <div class="swatch" style="background: <?= $n ?>; color: <?= contrast($c, [255, 255, 255]) > contrast($c, [0, 0, 0]) ? '#fff' : '#000' ?>"><?= strtoupper($n) ?></div>
  <?php endforeach; ?>
</div>

<h2>Lisibilité (contraste WCAG)</h2>
<div class="stats">
  <div class="stat" style="background: <?= e(hex($rgb)) ?>; color:#fff"><b style="color:#fff"><?= fmt($cWhite, 2) ?>:1</b>Texte blanc · <?= $cWhite >= 4.5 ? '✅ AA' : '⚠️ insuffisant' ?></div>
  <div class="stat" style="background: <?= e(hex($rgb)) ?>; color:#000"><b style="color:#000"><?= fmt($cBlack, 2) ?>:1</b>Texte noir · <?= $cBlack >= 4.5 ? '✅ AA' : '⚠️ insuffisant' ?></div>
</div>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Les formats de couleur expliqués</h2>
  <h3>HEX (hexadécimal)</h3>
  <p>Le format le plus utilisé en CSS : un dièse suivi de 6 caractères, deux par composante rouge, verte et bleue, de 00 à FF
  (0 à 255). #FF0000 est un rouge pur. La forme courte #F00 est équivalente.</p>
  <h3>RVB (RGB)</h3>
  <p>Les mêmes trois composantes, écrites en décimal de 0 à 255 : rgb(255, 0, 0). C'est la façon dont les écrans
  produisent les couleurs, par synthèse additive de lumière.</p>
  <h3>TSL (HSL)</h3>
  <p>Teinte (0 à 360°, la position sur le cercle chromatique), saturation (0 à 100 %) et luminosité (0 à 100 %).
  Ce format est très pratique pour créer des variantes : il suffit de modifier la luminosité pour obtenir une nuance plus
  claire ou plus foncée d'une même couleur, comme dans la palette de nuances ci-dessus.</p>
  <h3>CMJN (CMYK)</h3>
  <p>Cyan, magenta, jaune et noir : le format de l'impression, par synthèse soustractive des encres. La conversion depuis le
  RVB est approximative : pour un travail d'impression professionnel, utilisez le profil colorimétrique de votre imprimeur.</p>

  <h2>Le contraste et l'accessibilité</h2>
  <p>Les règles d'accessibilité WCAG demandent un rapport de contraste d'au moins <strong>4,5:1</strong> entre un texte de
  taille normale et son fond (niveau AA), et 3:1 pour les grands titres. Un contraste suffisant rend votre site lisible par
  les personnes malvoyantes, mais aussi par tout le monde sur un écran de téléphone en plein soleil.</p>
</article>
<script>
document.getElementById('picker').addEventListener('change', function (e) {
  document.getElementById('couleur').value = e.target.value;
  document.getElementById('form-couleur').submit();
});
</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
