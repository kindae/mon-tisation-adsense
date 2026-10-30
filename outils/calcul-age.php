<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-age');

$jours = ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'];
$naissance = post('naissance');
$reference = post('reference', date('Y-m-d'));
$result = null;
$error = null;

if (is_post()) {
    $birth = DateTimeImmutable::createFromFormat('!Y-m-d', $naissance);
    $ref = DateTimeImmutable::createFromFormat('!Y-m-d', $reference);
    if (!$birth || !$ref) {
        $error = 'Veuillez saisir des dates valides.';
    } elseif ($birth > $ref) {
        $error = 'La date de naissance doit être antérieure à la date de référence.';
    } else {
        $diff = $birth->diff($ref);
        $next = $birth->setDate((int) $ref->format('Y'), (int) $birth->format('m'), (int) $birth->format('d'));
        if ($next < $ref) {
            $next = $next->modify('+1 year');
        }
        $result = [
            'y' => $diff->y, 'm' => $diff->m, 'd' => $diff->d,
            'days' => $diff->days,
            'weeks' => intdiv($diff->days, 7),
            'months' => $diff->y * 12 + $diff->m,
            'next' => $ref->diff($next)->days,
            'weekday' => $jours[(int) $birth->format('w')],
        ];
    }
}
include __DIR__ . '/../inc/header.php';
?>
<h1><?= e($tool['title']) ?></h1>
<p class="lead">Indiquez une date de naissance pour connaître l'âge exact en années, mois et jours, le nombre total de jours
vécus et le temps restant avant le prochain anniversaire.</p>

<form method="post" class="tool">
  <div class="row">
    <div><label for="naissance">Date de naissance</label>
      <input type="date" id="naissance" name="naissance" value="<?= e($naissance) ?>" required></div>
    <div><label for="reference">Âge à la date du</label>
      <input type="date" id="reference" name="reference" value="<?= e($reference) ?>" required></div>
  </div>
  <button type="submit">Calculer l'âge</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="result">
  <span class="big"><?= $result['y'] ?> ans, <?= $result['m'] ?> mois et <?= $result['d'] ?> jour<?= $result['d'] > 1 ? 's' : '' ?></span><br>
  Vous êtes né(e) un <strong><?= $result['weekday'] ?></strong>.
  <?= $result['next'] === 0 ? '<strong>Joyeux anniversaire ! 🎉</strong>' : 'Prochain anniversaire dans <strong>' . $result['next'] . ' jour' . ($result['next'] > 1 ? 's' : '') . '</strong>.' ?>
</div>
<div class="stats">
  <div class="stat"><b><?= fmt($result['months'], 0) ?></b>mois</div>
  <div class="stat"><b><?= fmt($result['weeks'], 0) ?></b>semaines</div>
  <div class="stat"><b><?= fmt($result['days'], 0) ?></b>jours</div>
  <div class="stat"><b><?= fmt($result['days'] * 24, 0) ?></b>heures (env.)</div>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Comment l'âge est-il calculé ?</h2>
  <p>L'outil compte d'abord le nombre d'années complètes écoulées depuis la date de naissance, puis les mois complets
  restants, puis les jours. Il tient compte de la longueur réelle de chaque mois (28, 29, 30 ou 31 jours) et des années
  bissextiles. Le nombre total de jours correspond à l'écart exact entre les deux dates.</p>

  <h2>Calculer un âge à une date passée ou future</h2>
  <p>Le champ « Âge à la date du » est réglé sur aujourd'hui, mais vous pouvez le modifier. C'est pratique pour savoir quel
  âge aura un enfant à la rentrée scolaire, quel âge vous aurez au moment de votre départ à la retraite, ou quel âge avait
  un personnage historique lors d'un événement.</p>

  <h2>Et si je suis né un 29 février ?</h2>
  <p>Les personnes nées un 29 février n'ont un « vrai » anniversaire que tous les quatre ans. Les années non bissextiles,
  l'outil considère que l'anniversaire tombe le 1<sup>er</sup> mars, ce qui correspond à l'usage administratif le plus courant
  en France pour le calcul de la majorité.</p>

  <h2>Le saviez-vous ?</h2>
  <p>Une personne de 30 ans a vécu environ 10 957 jours, soit plus de 260 000 heures. On fête ses 10 000 jours vers
  27 ans et 4 mois : une occasion originale de célébrer !</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
