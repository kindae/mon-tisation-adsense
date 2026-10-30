<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('jours-entre-dates');

/** Jours fériés en France métropolitaine pour une année (format Y-m-d). */
function feries(int $y): array
{
    // Date de Pâques (algorithme de Meeus/Jones/Butcher)
    $a = $y % 19; $b = intdiv($y, 100); $c = $y % 100; $d = intdiv($b, 4); $e = $b % 4;
    $f = intdiv($b + 8, 25); $g = intdiv($b - $f + 1, 3); $h = (19 * $a + $b - $d - $g + 15) % 30;
    $i = intdiv($c, 4); $k = $c % 4; $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7; $m = intdiv($a + 11 * $h + 22 * $l, 451);
    $mois = intdiv($h + $l - 7 * $m + 114, 31); $jour = (($h + $l - 7 * $m + 114) % 31) + 1;
    $paques = new DateTimeImmutable(sprintf('%04d-%02d-%02d', $y, $mois, $jour));
    $list = [];
    foreach (['01-01' => 'Jour de l\'an', '05-01' => 'Fête du Travail', '05-08' => 'Victoire 1945', '07-14' => 'Fête nationale',
        '08-15' => 'Assomption', '11-01' => 'Toussaint', '11-11' => 'Armistice 1918', '12-25' => 'Noël'] as $md => $nom) {
        $list["$y-$md"] = $nom;
    }
    $list[$paques->modify('+1 day')->format('Y-m-d')] = 'Lundi de Pâques';
    $list[$paques->modify('+39 days')->format('Y-m-d')] = 'Ascension';
    $list[$paques->modify('+50 days')->format('Y-m-d')] = 'Lundi de Pentecôte';
    return $list;
}

$mode = post('mode', $_GET['mode'] ?? 'ecart');
$mode = $mode === 'ajout' ? 'ajout' : 'ecart';
$jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$moisNoms = ['', 'janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'];
$fr = fn(DateTimeImmutable $d) => $jours[(int) $d->format('w')] . ' ' . (int) $d->format('j') . ' ' . $moisNoms[(int) $d->format('n')] . ' ' . $d->format('Y');
$result = null;
$error = null;

if (is_post()) {
    $debut = DateTimeImmutable::createFromFormat('!Y-m-d', post('debut'));
    if (!$debut) {
        $error = 'Veuillez saisir une date de début valide.';
    } elseif ($mode === 'ecart') {
        $fin = DateTimeImmutable::createFromFormat('!Y-m-d', post('fin'));
        $inclure = isset($_POST['inclure']);
        if (!$fin) {
            $error = 'Veuillez saisir une date de fin valide.';
        } elseif (abs($fin->getTimestamp() - $debut->getTimestamp()) > 200 * 366 * 86400) {
            $error = 'L\'écart maximal est de 200 ans.';
        } else {
            [$from, $to] = $debut <= $fin ? [$debut, $fin] : [$fin, $debut];
            $total = $from->diff($to)->days + ($inclure ? 1 : 0);
            $ouvres = $ouvrables = $feriesCount = 0;
            $feriesCache = [];
            $end = $inclure ? $to : $to->modify('-1 day');
            for ($d = $from; $d <= $end; $d = $d->modify('+1 day')) {
                $y = (int) $d->format('Y');
                $feriesCache[$y] ??= feries($y);
                $w = (int) $d->format('w');
                $ferie = isset($feriesCache[$y][$d->format('Y-m-d')]);
                if ($ferie && $w >= 1 && $w <= 5) { $feriesCount++; }
                if (!$ferie && $w >= 1 && $w <= 5) { $ouvres++; }
                if (!$ferie && $w >= 1) { $ouvrables++; }
            }
            $diff = $from->diff($to);
            $result = ['total' => $total, 'semaines' => intdiv($total, 7), 'reste' => $total % 7, 'ouvres' => $ouvres,
                'ouvrables' => $ouvrables, 'feries' => $feriesCount, 'ymd' => $diff];
        }
    } else {
        $n = abs((int) post('nombre', '0'));
        $unite = post('unite', 'jours');
        $sens = post('sens') === '-' ? '-' : '+';
        if (abs($n) > 100000 || !in_array($unite, ['jours', 'semaines', 'mois', 'ans', 'ouvres'], true)) {
            $error = 'Valeur invalide.';
        } elseif ($unite === 'ouvres') {
            $d = $debut; $done = 0; $step = $sens === '+' ? '+1 day' : '-1 day';
            while ($done < $n) {
                $d = $d->modify($step);
                $w = (int) $d->format('w');
                if ($w >= 1 && $w <= 5 && !isset(feries((int) $d->format('Y'))[$d->format('Y-m-d')])) { $done++; }
            }
            $result = ['date' => $d];
        } else {
            $map = ['jours' => 'days', 'semaines' => 'weeks', 'mois' => 'months', 'ans' => 'years'];
            $result = ['date' => $debut->modify("{$sens}{$n} {$map[$unite]}")];
        }
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Calculez le nombre de jours, de semaines et de jours ouvrés entre deux dates (jours fériés français déduits),
ou trouvez la date obtenue en ajoutant ou en retirant une durée.</p>
  </div>
</div>

<nav class="tabs">
  <a href="?mode=ecart" class="<?= $mode === 'ecart' ? 'active' : '' ?>">Écart entre deux dates</a>
  <a href="?mode=ajout" class="<?= $mode === 'ajout' ? 'active' : '' ?>">Ajouter / retirer des jours</a>
</nav>
<form method="post" action="?mode=<?= $mode ?>" class="tool">
  <input type="hidden" name="mode" value="<?= $mode ?>">
  <?php if ($mode === 'ecart'): ?>
  <div class="row">
    <div><label for="debut">Date de début</label><input type="date" id="debut" name="debut" value="<?= e(post('debut', date('Y-m-d'))) ?>" required></div>
    <div><label for="fin">Date de fin</label><input type="date" id="fin" name="fin" value="<?= e(post('fin')) ?>" required></div>
  </div>
  <label class="inline"><input type="checkbox" name="inclure" <?= isset($_POST['inclure']) ? 'checked' : '' ?>> Inclure le jour de fin</label>
  <?php else: ?>
  <div class="row">
    <div><label for="debut">Date de départ</label><input type="date" id="debut" name="debut" value="<?= e(post('debut', date('Y-m-d'))) ?>" required></div>
    <div><label for="sens">Opération</label><select id="sens" name="sens"><option value="+">Ajouter</option><option value="-" <?= post('sens') === '-' ? 'selected' : '' ?>>Retirer</option></select></div>
    <div><label for="nombre">Durée</label><input type="number" id="nombre" name="nombre" min="0" value="<?= e(post('nombre', '30')) ?>"></div>
    <div><label for="unite">Unité</label><select id="unite" name="unite">
      <?php foreach (['jours' => 'jours', 'ouvres' => 'jours ouvrés', 'semaines' => 'semaines', 'mois' => 'mois', 'ans' => 'ans'] as $k => $l): ?>
        <option value="<?= $k ?>" <?= post('unite') === $k ? 'selected' : '' ?>><?= $l ?></option>
      <?php endforeach; ?></select></div>
  </div>
  <?php endif; ?>
  <br><button type="submit">Calculer</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result && $mode === 'ecart'): ?>
<div class="result">
  <span class="big"><?= fmt($result['total'], 0) ?> jour<?= $result['total'] > 1 ? 's' : '' ?></span><br>
  Soit <?= $result['semaines'] ?> semaine<?= $result['semaines'] > 1 ? 's' : '' ?> et <?= $result['reste'] ?> jour<?= $result['reste'] > 1 ? 's' : '' ?>,
  ou <?= $result['ymd']->y ?> an(s), <?= $result['ymd']->m ?> mois et <?= $result['ymd']->d ?> jour(s).
</div>
<div class="stats">
  <div class="stat"><b><?= fmt($result['ouvres'], 0) ?></b>jours ouvrés (lun.–ven.)</div>
  <div class="stat"><b><?= fmt($result['ouvrables'], 0) ?></b>jours ouvrables (lun.–sam.)</div>
  <div class="stat"><b><?= $result['feries'] ?></b>jours fériés en semaine</div>
</div>
<?php elseif ($result): ?>
<div class="result">Date obtenue : <span class="big"><?= e($fr($result['date'])) ?></span></div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Jours calendaires, ouvrables et ouvrés</h2>
  <ul>
    <li><strong>Jours calendaires</strong> : tous les jours du calendrier, week-ends et jours fériés compris.</li>
    <li><strong>Jours ouvrables</strong> : du lundi au samedi, hors jours fériés. Ils servent notamment au calcul des congés
    payés « en jours ouvrables » (30 jours par an).</li>
    <li><strong>Jours ouvrés</strong> : les jours effectivement travaillés dans l'entreprise, généralement du lundi au vendredi,
    hors jours fériés (25 jours de congés par an).</li>
  </ul>

  <h2>Les jours fériés pris en compte</h2>
  <p>L'outil déduit les 11 jours fériés légaux de France métropolitaine : 1<sup>er</sup> janvier, lundi de Pâques,
  1<sup>er</sup> mai, 8 mai, Ascension, lundi de Pentecôte, 14 juillet, 15 août, 1<sup>er</sup> novembre, 11 novembre et
  25 décembre. Les dates mobiles (Pâques, Ascension, Pentecôte) sont recalculées automatiquement pour chaque année.
  En Alsace-Moselle, le Vendredi saint et le 26 décembre sont également fériés.</p>

  <h2>Faut-il inclure le jour de fin ?</h2>
  <p>Par défaut, l'outil calcule l'écart entre les deux dates : du 1<sup>er</sup> au 3 du mois, il y a 2 jours. Si vous
  comptez une durée de séjour ou de congé où le premier et le dernier jour sont tous deux concernés (du lundi au vendredi
  inclus = 5 jours), cochez « Inclure le jour de fin ».</p>

  <h2>Exemples d'utilisation</h2>
  <p>Calculer un délai de préavis, la date limite d'un paiement à 30 jours, le nombre de jours travaillés sur un projet,
  la durée d'une grossesse, ou le compte à rebours avant des vacances ou un mariage.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
