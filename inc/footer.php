<?php
if (!defined('APP')) { exit; }
$tool = $tool ?? null;
if ($tool):
    $same = array_filter(tools(), fn($t, $k) => $k !== $tool['slug'] && $t['cat'] === $tool['cat'], ARRAY_FILTER_USE_BOTH);
    $others = array_filter(tools(), fn($t, $k) => $k !== $tool['slug'] && $t['cat'] !== $tool['cat'], ARRAY_FILTER_USE_BOTH);
?>
<?php ad('bottom'); ?>
<section class="related">
  <h2>Ces outils peuvent aussi vous servir</h2>
  <div class="grid small">
  <?php foreach (array_slice($others, 0, 6, true) as $slug => $t): ?>
    <a class="card" href="<?= e(tool_url($slug)) ?>" style="--accent: <?= cat_color($t['cat']) ?>"><span class="icon"><?= $t['icon'] ?></span><strong><?= e($t['title']) ?></strong></a>
  <?php endforeach; ?>
  </div>
</section>
</div>
<aside class="sidebar">
  <?php if ($same): ?>
  <div class="side-box">
    <h3><?= e($tool['cat']) ?></h3>
    <ul class="side-list">
    <?php foreach ($same as $slug => $t): ?>
      <li><a href="<?= e(tool_url($slug)) ?>"><span><?= $t['icon'] ?></span><?= e($t['title']) ?></a></li>
    <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>
  <?php ad('sidebar'); ?>
  <div class="side-box side-cta">
    <h3>Une idée d'outil ?</h3>
    <p>Dites-nous ce qui vous manque : nous ajoutons régulièrement de nouveaux outils.</p>
    <a class="btn small" href="<?= e(url('contact.php')) ?>">Proposer un outil</a>
  </div>
</aside>
<?php endif; ?>
</main>
<footer class="site-footer">
  <div class="wrap footer-grid">
    <div>
      <a class="logo light" href="<?= e(url()) ?>"><img src="<?= e(url('assets/logo.svg')) ?>" alt="" width="30" height="30"><span><?= e(cfg('site_name')) ?></span></a>
      <p><?= e(cfg('tagline')) ?>. <?= count(tools()) ?> outils gratuits, sans inscription, sur ordinateur et mobile.</p>
    </div>
    <div>
      <h4>Catégories</h4>
      <?php foreach (categories() as $cat => [$color]): ?>
        <a href="<?= e(url()) ?>#<?= cat_slug($cat) ?>"><?= e($cat) ?></a>
      <?php endforeach; ?>
    </div>
    <div>
      <h4>Informations</h4>
      <a href="<?= e(url('a-propos.php')) ?>">À propos</a>
      <a href="<?= e(url('contact.php')) ?>">Contact</a>
      <a href="<?= e(url('confidentialite.php')) ?>">Politique de confidentialité</a>
      <a href="<?= e(url('mentions-legales.php')) ?>">Mentions légales</a>
      <a href="<?= e(url('cgu.php')) ?>">Conditions d'utilisation</a>
    </div>
  </div>
  <div class="wrap footer-bottom">© <?= date('Y') ?> <?= e(cfg('site_name')) ?> · Les résultats sont fournis à titre indicatif.</div>
</footer>
<script src="<?= e(url('assets/app.js')) ?>?v=2"></script>
</body>
</html>
