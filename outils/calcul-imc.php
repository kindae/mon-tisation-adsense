<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('calcul-imc');

$poids = post_num('poids');
$taille = post_num('taille');
$result = null;
$error = null;
$classes = [
    [0, 16.5, 'Dénutrition'],
    [16.5, 18.5, 'Maigreur'],
    [18.5, 25, 'Corpulence normale'],
    [25, 30, 'Surpoids'],
    [30, 35, 'Obésité modérée'],
    [35, 40, 'Obésité sévère'],
    [40, 999, 'Obésité morbide'],
];

if (is_post()) {
    if ($poids === null || $poids < 2 || $poids > 400) {
        $error = 'Veuillez saisir un poids valide en kilogrammes.';
    } elseif ($taille === null || $taille < 50 || $taille > 260) {
        $error = 'Veuillez saisir une taille valide en centimètres (ex. 175).';
    } else {
        $m = $taille / 100;
        $imc = $poids / ($m * $m);
        foreach ($classes as [$min, $max, $label]) {
            if ($imc >= $min && $imc < $max) {
                $classe = $label;
            }
        }
        $result = ['imc' => $imc, 'classe' => $classe, 'min' => 18.5 * $m * $m, 'max' => 24.9 * $m * $m];
    }
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Calculez votre indice de masse corporelle (IMC) à partir de votre poids et de votre taille, et découvrez
dans quelle catégorie il se situe selon l'Organisation mondiale de la santé.</p>
  </div>
</div>

<form method="post" class="tool">
  <div class="row">
    <div><label for="poids">Poids (kg)</label>
      <input type="text" inputmode="decimal" id="poids" name="poids" value="<?= e(post('poids')) ?>" placeholder="70" required></div>
    <div><label for="taille">Taille (cm)</label>
      <input type="text" inputmode="decimal" id="taille" name="taille" value="<?= e(post('taille')) ?>" placeholder="175" required></div>
  </div>
  <button type="submit">Calculer mon IMC</button>
</form>

<?php if ($error): ?><div class="alert"><?= e($error) ?></div><?php endif; ?>
<?php if ($result): ?>
<div class="result">
  Votre IMC : <span class="big"><?= fmt($result['imc'], 1) ?></span><br>
  Catégorie : <strong><?= e($result['classe']) ?></strong><br>
  Pour votre taille, un IMC « normal » correspond à un poids compris entre <strong><?= fmt($result['min'], 1) ?> kg</strong>
  et <strong><?= fmt($result['max'], 1) ?> kg</strong>.
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Qu'est-ce que l'IMC ?</h2>
  <p>L'indice de masse corporelle est un indicateur simple, utilisé dans le monde entier, qui met en relation le poids et la
  taille d'une personne. Il se calcule en divisant le poids (en kilogrammes) par le carré de la taille (en mètres) :</p>
  <p><strong>IMC = poids ÷ taille²</strong></p>
  <p>Exemple : pour 70 kg et 1,75 m, IMC = 70 ÷ (1,75 × 1,75) = 22,9.</p>

  <h2>Interprétation selon l'OMS</h2>
  <div class="table-scroll"><table>
    <tr><th>IMC</th><th>Catégorie</th></tr>
    <tr><td>moins de 16,5</td><td>Dénutrition</td></tr>
    <tr><td>16,5 à 18,5</td><td>Maigreur</td></tr>
    <tr><td>18,5 à 25</td><td>Corpulence normale</td></tr>
    <tr><td>25 à 30</td><td>Surpoids</td></tr>
    <tr><td>30 à 35</td><td>Obésité modérée</td></tr>
    <tr><td>35 à 40</td><td>Obésité sévère</td></tr>
    <tr><td>plus de 40</td><td>Obésité morbide</td></tr>
  </table></div>

  <h2>Les limites de l'IMC</h2>
  <p>L'IMC ne distingue pas la masse musculaire de la masse grasse : un sportif très musclé peut avoir un IMC élevé sans
  excès de graisse. Il ne tient pas compte non plus de la répartition des graisses, alors que le tour de taille est un
  indicateur important du risque cardiovasculaire. Enfin, il n'est pas adapté aux enfants et adolescents (on utilise des
  courbes de corpulence spécifiques), aux femmes enceintes, ni aux personnes âgées de plus de 65 ans sans interprétation
  médicale.</p>
  <p class="muted">Cet outil est fourni à titre informatif et ne remplace pas l'avis d'un professionnel de santé.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
