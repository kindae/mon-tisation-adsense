<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('salaire-brut-net');

// Taux moyens de cotisations salariales (approximation courante).
$statuts = [
    'non-cadre' => ['Salarié non-cadre', 22],
    'cadre'     => ['Salarié cadre', 25],
    'public'    => ['Fonction publique', 15],
    'liberal'   => ['Profession libérale', 45],
    'portage'   => ['Portage salarial', 51],
];
$periodes = ['mensuel' => 'Mensuel', 'annuel' => 'Annuel', 'horaire' => 'Horaire'];
$HEURES = 151.67; // heures mensuelles pour 35 h / semaine

$montant = post_num('montant');
$sens = post('sens', 'brut');
$statut = post('statut', 'non-cadre');
$periode = post('periode', 'mensuel');
$mois = (int) post('mois', '12');
$pas = post_num('pas') ?? 0.0;
$tempsPartiel = post_num('temps') ?? 100.0;
$result = null;
$error = null;

if (is_post()) {
    if (!isset($statuts[$statut], $periodes[$periode]) || !in_array($mois, [12, 13, 14], true)) {
        $error = 'Paramètres invalides.';
    } elseif ($montant === null || $montant <= 0) {
        $error = 'Veuillez saisir un montant positif.';
    } elseif ($pas < 0 || $pas > 50 || $tempsPartiel <= 0 || $tempsPartiel > 100) {
        $error = 'Vérifiez le taux de prélèvement à la source (0 à 50 %) et le temps de travail (1 à 100 %).';
    } else {
        $taux = $statuts[$statut][1] / 100;
        $heures = $HEURES * $tempsPartiel / 100;
        // Montant mensuel saisi
        $mensuel = $periode === 'annuel' ? $montant / $mois : ($periode === 'horaire' ? $montant * $heures : $montant);
        $brut = $sens === 'brut' ? $mensuel : $mensuel / (1 - $taux);
        $net = $brut * (1 - $taux);
        $apresImpot = $net * (1 - $pas / 100);
        $result = [
            'mensuel' => [$brut, $net, $apresImpot],
            'annuel'  => [$brut * $mois, $net * $mois, $apresImpot * $mois],
            'horaire' => [$brut / $heures, $net / $heures, $apresImpot / $heures],
        ];
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Estimez votre salaire net à partir du brut, ou le brut nécessaire pour un net donné, selon votre statut.
Résultats en mensuel, annuel et horaire, avant et après prélèvement à la source.</p>
  </div>
</div>

<form method="post" class="tool">
  <div class="row">
    <div><label for="montant">Montant (€)</label>
      <input type="text" inputmode="decimal" id="montant" name="montant" value="<?= e(post('montant')) ?>" placeholder="2500" required></div>
    <div><label for="sens">Ce montant est</label>
      <select id="sens" name="sens">
        <option value="brut" <?= $sens === 'brut' ? 'selected' : '' ?>>Brut → calculer le net</option>
        <option value="net" <?= $sens === 'net' ? 'selected' : '' ?>>Net → calculer le brut</option>
      </select></div>
    <div><label for="periode">Période</label>
      <select id="periode" name="periode">
        <?php foreach ($periodes as $k => $l): ?><option value="<?= $k ?>" <?= $k === $periode ? 'selected' : '' ?>><?= $l ?></option><?php endforeach; ?>
      </select></div>
  </div>
  <div class="row">
    <div><label for="statut">Statut</label>
      <select id="statut" name="statut">
        <?php foreach ($statuts as $k => [$l, $t]): ?><option value="<?= $k ?>" <?= $k === $statut ? 'selected' : '' ?>><?= e($l) ?> (≈ <?= $t ?> %)</option><?php endforeach; ?>
      </select></div>
    <div><label for="mois">Nombre de mois payés</label>
      <select id="mois" name="mois">
        <?php foreach ([12, 13, 14] as $m): ?><option value="<?= $m ?>" <?= $m === $mois ? 'selected' : '' ?>><?= $m ?> mois</option><?php endforeach; ?>
      </select></div>
    <div><label for="temps">Temps de travail (%)</label>
      <input type="text" inputmode="decimal" id="temps" name="temps" value="<?= e(post('temps', '100')) ?>"></div>
    <div><label for="pas">Prélèvement à la source (%)</label>
      <input type="text" inputmode="decimal" id="pas" name="pas" value="<?= e(post('pas', '0')) ?>"></div>
  </div>
  <button type="submit">Calculer</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="result">Salaire net mensuel estimé : <span class="big"><?= fmt($result['mensuel'][1]) ?> €</span></div>
<div class="table-scroll"><table>
  <tr><th></th><th>Brut</th><th>Net avant impôt</th><th>Net après impôt</th></tr>
  <?php foreach (['mensuel' => 'Mensuel', 'annuel' => 'Annuel', 'horaire' => 'Horaire'] as $k => $l): ?>
  <tr><th><?= $l ?></th><td><?= fmt($result[$k][0]) ?> €</td><td><?= fmt($result[$k][1]) ?> €</td><td><?= fmt($result[$k][2]) ?> €</td></tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Du salaire brut au salaire net</h2>
  <p>Le salaire brut est la rémunération prévue au contrat. Le salaire net est ce qui reste après déduction des
  <strong>cotisations salariales</strong> : assurance vieillesse, retraite complémentaire, CSG, CRDS, prévoyance…
  En moyenne, ces cotisations représentent :</p>
  <div class="table-scroll"><table>
    <tr><th>Statut</th><th>Cotisations salariales moyennes</th><th>Net pour 2 500 € brut</th></tr>
    <tr><td>Non-cadre</td><td>≈ 22 %</td><td>≈ 1 950 €</td></tr>
    <tr><td>Cadre</td><td>≈ 25 %</td><td>≈ 1 875 €</td></tr>
    <tr><td>Fonction publique</td><td>≈ 15 %</td><td>≈ 2 125 €</td></tr>
  </table></div>
  <p>Les cadres cotisent davantage, principalement pour leur retraite complémentaire. Pour les professions libérales et le
  portage salarial, le taux indiqué correspond à l'ensemble des charges rapportées au chiffre d'affaires ou au salaire.</p>

  <h2>Le prélèvement à la source</h2>
  <p>Depuis 2019, l'impôt sur le revenu est prélevé directement sur la fiche de paie. Votre taux personnalisé figure sur votre
  avis d'imposition et dans votre espace sur impots.gouv.fr. Saisissez-le pour connaître le montant réellement versé sur votre
  compte bancaire.</p>

  <h2>Salaire horaire et temps partiel</h2>
  <p>Le calcul horaire se base sur la durée légale de 35 heures par semaine, soit 151,67 heures par mois. Pour un temps partiel,
  indiquez votre pourcentage (par exemple 80 %) : le salaire horaire reste le même, mais le nombre d'heures est ajusté.</p>

  <h2>13<sup>e</sup> mois</h2>
  <p>Si votre contrat prévoit un 13<sup>e</sup> (voire un 14<sup>e</sup>) mois, choisissez le nombre de mois payés : le salaire
  annuel sera divisé ou multiplié en conséquence.</p>
  <p class="muted">Estimation indicative basée sur des taux moyens. Votre fiche de paie peut différer selon votre convention
  collective, votre mutuelle, les heures supplémentaires ou les avantages en nature.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
