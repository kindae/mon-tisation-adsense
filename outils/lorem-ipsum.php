<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('lorem-ipsum');

$WORDS = explode(' ', 'lorem ipsum dolor sit amet consectetur adipiscing elit sed do eiusmod tempor incididunt ut labore et dolore '
    . 'magna aliqua enim ad minim veniam quis nostrud exercitation ullamco laboris nisi aliquip ex ea commodo consequat duis aute irure '
    . 'in reprehenderit voluptate velit esse cillum fugiat nulla pariatur excepteur sint occaecat cupidatat non proident sunt culpa qui '
    . 'officia deserunt mollit anim id est laborum curabitur pretium tincidunt lacus nunc pulvinar sapien ligula vitae viverra justo '
    . 'nec ultrices dui morbi tristique senectus netus malesuada fames ac turpis egestas integer feugiat scelerisque varius quam '
    . 'pellentesque habitant mauris rhoncus aenean vel elementum sagittis vitae purus faucibus ornare suspendisse sed nisi');

function lorem_sentence(array $words): string
{
    $n = random_int(7, 16);
    $s = [];
    for ($i = 0; $i < $n; $i++) {
        $s[] = $words[random_int(0, count($words) - 1)];
        if ($i > 2 && $i < $n - 2 && random_int(1, 9) === 1) {
            $s[count($s) - 1] .= ',';
        }
    }
    return ucfirst(implode(' ', $s)) . '.';
}

function lorem_paragraph(array $words): string
{
    $p = [];
    for ($i = 0, $n = random_int(4, 7); $i < $n; $i++) {
        $p[] = lorem_sentence($words);
    }
    return implode(' ', $p);
}

$type = post('type', 'paragraphes');
$nombre = max(1, min(100, (int) post('nombre', '3')));
$classique = is_post() ? isset($_POST['classique']) : true;
$html = isset($_POST['html']);
$output = null;

if (is_post()) {
    if ($type === 'mots') {
        $w = [];
        for ($i = 0; $i < $nombre; $i++) {
            $w[] = $WORDS[random_int(0, count($WORDS) - 1)];
        }
        if ($classique) {
            $w = array_merge(array_slice(['lorem', 'ipsum', 'dolor', 'sit', 'amet'], 0, $nombre), array_slice($w, 5));
            $w = array_slice($w, 0, $nombre);
        }
        $blocks = [ucfirst(implode(' ', $w)) . '.'];
    } else {
        $blocks = [];
        for ($i = 0; $i < $nombre; $i++) {
            $blocks[] = $type === 'phrases' ? lorem_sentence($WORDS) : lorem_paragraph($WORDS);
        }
        if ($classique) {
            $blocks[0] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. ' . ($type === 'phrases' ? '' : $blocks[0]);
            $blocks[0] = trim($blocks[0]);
        }
        if ($type === 'phrases') {
            $blocks = [implode(' ', $blocks)];
        }
    }
    $output = $html
        ? implode("\n", array_map(fn($b) => '<p>' . $b . '</p>', $blocks))
        : implode("\n\n", $blocks);
}
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Générez instantanément du faux texte pour remplir vos maquettes de sites web, de flyers ou de documents,
en choisissant le nombre de paragraphes, de phrases ou de mots.</p>
  </div>
</div>

<form method="post" class="tool">
  <div class="row">
    <div><label for="nombre">Quantité</label>
      <input type="number" id="nombre" name="nombre" min="1" max="100" value="<?= $nombre ?>"></div>
    <div><label for="type">Unité</label>
      <select id="type" name="type">
        <?php foreach (['paragraphes' => 'Paragraphes', 'phrases' => 'Phrases', 'mots' => 'Mots'] as $k => $l): ?>
          <option value="<?= $k ?>" <?= $k === $type ? 'selected' : '' ?>><?= $l ?></option>
        <?php endforeach; ?>
      </select></div>
  </div>
  <label class="inline"><input type="checkbox" name="classique" <?= $classique ? 'checked' : '' ?>> Commencer par « Lorem ipsum dolor sit amet »</label>
  <label class="inline"><input type="checkbox" name="html" <?= $html ? 'checked' : '' ?>> Ajouter les balises HTML &lt;p&gt;</label>
  <br><button type="submit">Générer le texte</button>
</form>

<?php if ($output !== null): ?>
<div class="result">
  <textarea id="sortie" readonly style="min-height:260px"><?= e($output) ?></textarea>
  <button type="button" class="secondary" data-copy="#sortie">Copier le texte</button>
</div>
<?php endif; ?>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Qu'est-ce que le Lorem Ipsum ?</h2>
  <p>Le Lorem Ipsum est un faux texte utilisé depuis des siècles par les imprimeurs, et aujourd'hui par les graphistes et les
  développeurs web. Il permet de juger de l'apparence d'une mise en page (polices, interlignage, colonnes) sans que le
  lecteur soit distrait par le sens du texte.</p>

  <h2>D'où vient-il ?</h2>
  <p>Contrairement à une idée reçue, ce n'est pas du latin aléatoire. Il est tiré d'un passage du <em>De finibus bonorum et
  malorum</em> (« Des termes extrêmes des biens et des maux ») de Cicéron, écrit en 45 av. J.-C. Le mot « lorem » est
  lui-même un fragment de « dolorem » (la douleur). Le texte a été popularisé dans les années 1960 par les feuilles de
  transfert Letraset, puis par les logiciels de PAO.</p>

  <h2>Pourquoi utiliser un faux texte ?</h2>
  <ul>
    <li><strong>Neutralité</strong> : un texte lisible attire l'attention sur le contenu plutôt que sur le design.</li>
    <li><strong>Réalisme</strong> : la répartition des longueurs de mots ressemble à celle d'une langue européenne.</li>
    <li><strong>Rapidité</strong> : on peut avancer sur la maquette avant que les textes définitifs soient rédigés.</li>
  </ul>

  <h2>Un conseil</h2>
  <p>N'oubliez pas de remplacer tout le Lorem Ipsum avant la mise en ligne ! Un texte de remplissage oublié donne une impression
  de site inachevé et nuit au référencement : les moteurs de recherche et les régies publicitaires considèrent ces pages comme
  sans contenu.</p>
</article>
<?php include __DIR__ . '/../inc/footer.php'; ?>
