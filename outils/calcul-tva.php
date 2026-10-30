<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-tva');

$rates = ['20' => '20 % (taux normal)', '10' => '10 % (taux intermédiaire)', '5.5' => '5,5 % (taux réduit)', '2.1' => '2,1 % (taux particulier)', 'autre' => 'Autre taux'];
$amount = post_num('montant');
$rateKey = post('taux', '20');
$custom = post_num('taux_perso');
$sens = post('sens', 'ht');
$result = null;
$error = null;

if (is_post()) {
    $rate = $rateKey === 'autre' ? $custom : (isset($rates[$rateKey]) ? (float) $rateKey : null);
    if ($amount === null || $amount < 0) {
        $error = 'Veuillez saisir un montant valide.';
    } elseif ($rate === null || $rate < 0 || $rate > 100) {
        $error = 'Veuillez saisir un taux de TVA valide (entre 0 et 100 %).';
    } else {
        $ht = $sens === 'ttc' ? $amount / (1 + $rate / 100) : $amount;
        $ttc = $ht * (1 + $rate / 100);
        $result = ['ht' => $ht, 'tva' => $ttc - $ht, 'ttc' => $ttc, 'rate' => $rate];
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Calculez en un instant un prix TTC à partir d'un prix hors taxes, ou retrouvez le montant HT et la TVA
contenus dans un prix TTC. Tous les taux français sont disponibles, ainsi qu'un taux personnalisé.</p>
  </div>
</div>

<form method="post" class="tool">
  <div class="row">
    <div><label for="montant">Montant (€)</label>
      <input type="text" inputmode="decimal" id="montant" name="montant" value="<?= e(post('montant')) ?>" placeholder="100" required></div>
    <div><label for="taux">Taux de TVA</label>
      <select id="taux" name="taux">
        <?php foreach ($rates as $k => $label): ?>
          <option value="<?= $k ?>" <?= (string) $k === $rateKey ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select></div>
    <div><label for="taux_perso">Taux personnalisé (%)</label>
      <input type="text" inputmode="decimal" id="taux_perso" name="taux_perso" value="<?= e(post('taux_perso')) ?>" placeholder="ex. 8,5"></div>
  </div>
  <label>Le montant saisi est :</label>
  <label class="inline"><input type="radio" name="sens" value="ht" <?= $sens !== 'ttc' ? 'checked' : '' ?>> Hors taxes (HT → TTC)</label>
  <label class="inline"><input type="radio" name="sens" value="ttc" <?= $sens === 'ttc' ? 'checked' : '' ?>> Toutes taxes comprises (TTC → HT)</label>
  <br><button type="submit">Calculer</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="stats">
  <div class="stat"><b><?= fmt($result['ht']) ?> €</b>Montant HT</div>
  <div class="stat"><b><?= fmt($result['tva']) ?> €</b>TVA (<?= fmt($result['rate']) ?> %)</div>
  <div class="stat"><b><?= fmt($result['ttc']) ?> €</b>Montant TTC</div>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Comment calculer la TVA ?</h2>
  <p>La taxe sur la valeur ajoutée (TVA) est un impôt sur la consommation, ajouté au prix hors taxes (HT) pour obtenir le
  prix toutes taxes comprises (TTC) payé par le client.</p>
  <ul>
    <li><strong>Du HT au TTC</strong> : TTC = HT × (1 + taux ÷ 100). Exemple à 20 % : 100 € HT × 1,20 = 120 € TTC.</li>
    <li><strong>Du TTC au HT</strong> : HT = TTC ÷ (1 + taux ÷ 100). Exemple à 20 % : 120 € TTC ÷ 1,20 = 100 € HT.</li>
    <li><strong>Montant de la TVA</strong> : TVA = TTC − HT, soit 20 € dans notre exemple.</li>
  </ul>
  <p>Erreur fréquente : pour retrouver le HT, il ne faut pas retirer 20 % du TTC. 120 € − 20 % = 96 €, ce qui est faux.
  Il faut bien diviser par 1,20.</p>

  <h2>Les taux de TVA en France</h2>
  <div class="table-scroll"><table>
    <tr><th>Taux</th><th>Principaux biens et services concernés</th></tr>
    <tr><td>20 %</td><td>Taux normal : la majorité des biens et des prestations de services.</td></tr>
    <tr><td>10 %</td><td>Restauration, transports de voyageurs, certains travaux de rénovation dans les logements.</td></tr>
    <tr><td>5,5 %</td><td>Produits alimentaires de base, livres, énergie, travaux de rénovation énergétique.</td></tr>
    <tr><td>2,1 %</td><td>Médicaments remboursables, publications de presse, redevance audiovisuelle.</td></tr>
  </table></div>
  <p>Des taux spécifiques s'appliquent en Corse et dans les départements d'outre-mer. Utilisez alors le champ
  « Taux personnalisé ». Les taux évoluent : vérifiez la réglementation en vigueur sur le site officiel de l'administration fiscale.</p>

  <h2>Et dans les autres pays ?</h2>
  <p>Le taux normal est de 21 % en Belgique, 17 % au Luxembourg, 8,1 % en Suisse et de 5 % pour la TPS au Canada (à laquelle
  s'ajoute la taxe provinciale). Choisissez « Autre taux » et saisissez le taux voulu.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
