<?php
if (!defined('APP')) { exit; }
$tool = $tool ?? null;
if ($tool):
    $others = array_filter(tools(), fn($k) => $k !== $tool['slug'], ARRAY_FILTER_USE_KEY);
?>
<?php ad('bottom'); ?>
<section class="related">
  <h2>Autres outils gratuits</h2>
  <div class="grid small">
  <?php foreach (array_slice($others, 0, 6, true) as $slug => $t): ?>
    <a class="card" href="<?= e(tool_url($slug)) ?>"><span class="icon"><?= $t['icon'] ?></span><strong><?= e($t['title']) ?></strong></a>
  <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
</main>
<footer class="site-footer">
  <div class="wrap">
    <p><strong><?= e(cfg('site_name')) ?></strong> — <?= e(cfg('tagline')) ?>. Tous les outils sont gratuits et sans inscription.</p>
    <nav>
      <a href="<?= e(url('a-propos.php')) ?>">À propos</a>
      <a href="<?= e(url('contact.php')) ?>">Contact</a>
      <a href="<?= e(url('confidentialite.php')) ?>">Politique de confidentialité</a>
      <a href="<?= e(url('mentions-legales.php')) ?>">Mentions légales</a>
      <a href="<?= e(url('cgu.php')) ?>">Conditions d'utilisation</a>
    </nav>
    <p class="muted">© <?= date('Y') ?> <?= e(cfg('site_name')) ?>. Les résultats sont fournis à titre indicatif.</p>
  </div>
</footer>
<script src="<?= e(url('assets/app.js')) ?>"></script>
</body>
</html>
