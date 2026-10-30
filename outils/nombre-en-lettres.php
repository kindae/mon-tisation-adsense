<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('nombre-en-lettres');

const UNITES = ['zéro', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf', 'dix', 'onze', 'douze', 'treize',
    'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
const DIZAINES = [2 => 'vingt', 3 => 'trente', 4 => 'quarante', 5 => 'cinquante', 6 => 'soixante'];

/** 0 à 99. $sep : ' ' (orthographe traditionnelle) ou '-' (rectifiée 1990). $final : le nombre termine l'expression. */
function lettres_100(int $n, string $sep, bool $final): string
{
    if ($n < 20) {
        return UNITES[$n];
    }
    $d = intdiv($n, 10);
    $u = $n % 10;
    if ($d === 7 || $d === 9) {
        $base = $d === 7 ? 'soixante' : 'quatre-vingt';
        $reste = $n - ($d === 7 ? 60 : 80);
        return $reste === 11 && $d === 7 ? "soixante{$sep}et{$sep}onze" : $base . '-' . UNITES[$reste];
    }
    if ($d === 8) {
        return $u === 0 ? ($final ? 'quatre-vingts' : 'quatre-vingt') : 'quatre-vingt-' . UNITES[$u];
    }
    if ($u === 0) {
        return DIZAINES[$d];
    }
    return DIZAINES[$d] . ($u === 1 ? "{$sep}et{$sep}un" : '-' . UNITES[$u]);
}

/** 0 à 999. */
function lettres_1000(int $n, string $sep, bool $final): string
{
    $c = intdiv($n, 100);
    $r = $n % 100;
    if ($c === 0) {
        return lettres_100($r, $sep, $final);
    }
    $cent = $c === 1 ? 'cent' : UNITES[$c] . $sep . 'cent' . ($r === 0 && $final ? 's' : '');
    return $r === 0 ? $cent : $cent . $sep . lettres_100($r, $sep, $final);
}

function en_lettres(int $n, bool $rectifie = false): string
{
    if ($n === 0) {
        return 'zéro';
    }
    $sep = $rectifie ? '-' : ' ';
    $parts = [];
    foreach ([1000000000 => 'milliard', 1000000 => 'million'] as $val => $nom) {
        $q = intdiv($n, $val);
        if ($q > 0) {
            // « millions » et « milliards » sont des noms : « cents » et « vingts » s'accordent devant eux.
            $parts[] = lettres_1000($q, $sep, true) . ' ' . $nom . ($q > 1 ? 's' : '');
            $n %= $val;
        }
    }
    $petit = [];
    $milliers = intdiv($n, 1000);
    if ($milliers > 0) {
        // « mille » est invariable et ne prend pas « un » devant.
        $petit[] = ($milliers === 1 ? '' : lettres_1000($milliers, $sep, false) . $sep) . 'mille';
        $n %= 1000;
    }
    if ($n > 0) {
        $petit[] = lettres_1000($n, $sep, true);
    }
    if ($petit) {
        $parts[] = implode($sep, $petit);
    }
    // « million » et « milliard » sont des noms : toujours séparés par une espace.
    return implode(' ', $parts);
}

$saisie = post('nombre');
$mode = post('mode', 'nombre');
$rectifie = post('ortho') === '1990';
$output = null;
$error = null;

if (is_post()) {
    $clean = str_replace([' ', "\u{00A0}", "\u{202F}", '.'], ['', '', '', ','], $saisie);
    if (!preg_match('/^(\d{1,12})(?:,(\d+))?$/', $clean, $m)) {
        $error = 'Saisissez un nombre positif (jusqu\'à 999 999 999 999), avec une virgule pour les décimales.';
    } else {
        $entier = (int) $m[1];
        $dec = $m[2] ?? '';
        $sep = $rectifie ? '-' : ' ';
        if ($mode === 'euros') {
            $centimes = $dec === '' ? 0 : (int) round((float) ('0.' . $dec) * 100);
            if ($centimes === 100) { $entier++; $centimes = 0; }
            $deEuros = $entier >= 1000000 && $entier % 1000000 === 0 ? ' d\'euros' : ' euro' . ($entier > 1 ? 's' : '');
            $output = en_lettres($entier, $rectifie) . $deEuros;
            if ($centimes > 0) {
                $output .= ' et ' . en_lettres($centimes, $rectifie) . ' centime' . ($centimes > 1 ? 's' : '');
            }
        } else {
            $output = en_lettres($entier, $rectifie);
            if ($dec !== '') {
                $zeros = strlen($dec) - strlen(ltrim($dec, '0'));
                $reste = ltrim($dec, '0');
                $output .= ' virgule ' . str_repeat('zéro ', $zeros) . ($reste === '' ? '' : en_lettres((int) substr($reste, 0, 12), $rectifie));
                $output = trim($output);
            }
        }
        $output = mb_strtoupper(mb_substr($output, 0, 1)) . mb_substr($output, 1);
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Convertissez n'importe quel nombre ou montant en toutes lettres, en respectant les règles d'accord de
« vingt », « cent » et « mille ». Indispensable pour remplir un chèque, une facture ou un acte officiel.</p>
  </div>
</div>

<form method="post" class="tool">
  <label for="nombre">Nombre ou montant</label>
  <input type="text" inputmode="decimal" id="nombre" name="nombre" value="<?= e($saisie) ?>" placeholder="ex. 1 280,50" required>
  <label>Écrire comme</label>
  <label class="inline"><input type="radio" name="mode" value="nombre" <?= $mode !== 'euros' ? 'checked' : '' ?>> Un nombre</label>
  <label class="inline"><input type="radio" name="mode" value="euros" <?= $mode === 'euros' ? 'checked' : '' ?>> Un montant en euros</label>
  <label>Orthographe</label>
  <label class="inline"><input type="radio" name="ortho" value="trad" <?= !$rectifie ? 'checked' : '' ?>> Traditionnelle (deux cent trois)</label>
  <label class="inline"><input type="radio" name="ortho" value="1990" <?= $rectifie ? 'checked' : '' ?>> Rectifiée 1990 (deux-cent-trois)</label>
  <br><button type="submit">Convertir en lettres</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($output !== null): ?>
<div class="result">
  <span class="big" id="sortie" style="font-size:1.35rem"><?= e($output) ?></span><br>
  <button type="button" class="secondary" data-copy="#sortie">Copier</button>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Les règles à connaître</h2>
  <h3>Vingt et cent</h3>
  <p>« Vingt » et « cent » prennent un <strong>s</strong> lorsqu'ils sont multipliés et terminent le nombre : quatre-vingts,
  deux cents. Ils restent invariables s'ils sont suivis d'un autre nombre : quatre-vingt-un, deux cent trois.
  Devant « mille », ils restent aussi invariables (deux cent mille), mais s'accordent devant « million » et « milliard »,
  qui sont des noms : deux cents millions.</p>
  <h3>Mille</h3>
  <p>« Mille » est toujours invariable : trois mille, et jamais « trois milles ». On écrit « mille » et non « un mille ».</p>
  <h3>Le trait d'union</h3>
  <p>Selon l'orthographe traditionnelle, on met un trait d'union entre les dizaines et les unités inférieures à cent, sauf
  quand ils sont reliés par « et » : vingt-deux, vingt et un, soixante et onze. Les rectifications de 1990 recommandent de
  relier tous les numéraux par des traits d'union : deux-cent-vingt-et-un. Les deux orthographes sont admises.</p>

  <h2>Remplir un chèque</h2>
  <p>Sur un chèque, le montant en lettres fait foi en cas de différence avec le montant en chiffres. Écrivez « euros »
  après le nombre, puis les centimes : « Mille deux cent quatre-vingts euros et cinquante centimes ». Tirez un trait
  après le montant pour éviter tout ajout frauduleux.</p>

  <h2>Exemples</h2>
  <div class="table-scroll"><table>
    <tr><th>Chiffres</th><th>Lettres</th></tr>
    <tr><td>71</td><td>soixante et onze</td></tr>
    <tr><td>80</td><td>quatre-vingts</td></tr>
    <tr><td>91</td><td>quatre-vingt-onze</td></tr>
    <tr><td>200</td><td>deux cents</td></tr>
    <tr><td>280 000</td><td>deux cent quatre-vingt mille</td></tr>
    <tr><td>2 000 000</td><td>deux millions</td></tr>
  </table></div>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
