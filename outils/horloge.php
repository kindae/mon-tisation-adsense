<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('horloge');
$villes = [
    'Europe/Paris' => 'Paris', 'Europe/London' => 'Londres', 'America/New_York' => 'New York', 'America/Montreal' => 'Montréal',
    'America/Los_Angeles' => 'Los Angeles', 'Africa/Dakar' => 'Dakar', 'Africa/Casablanca' => 'Casablanca', 'Africa/Abidjan' => 'Abidjan',
    'Asia/Dubai' => 'Dubaï', 'Asia/Tokyo' => 'Tokyo', 'Australia/Sydney' => 'Sydney', 'Indian/Reunion' => 'La Réunion',
];
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">L'heure exacte de votre appareil, affichée en grand et mise à jour chaque seconde. Idéal en plein écran pour
une salle de classe, une réunion ou un examen.</p>
  </div>
</div>

<div class="tool">
  <div id="clock" class="clock">--:--:--</div>
  <div id="clock-date" class="clock-date"></div>
  <p style="text-align:center">
    <button type="button" id="toggle-format" class="secondary">Passer au format 12 h</button>
    <button type="button" id="fullscreen">Plein écran</button>
  </p>
</div>

<h2>Heure dans le monde</h2>
<div class="stats">
  <?php foreach ($villes as $tz => $nom): ?>
    <div class="stat"><b data-tz="<?= e($tz) ?>">--:--</b><?= e($nom) ?></div>
  <?php endforeach; ?>
</div>

<?php ad('in_content'); ?>

<article class="content">
  <h2>D'où vient l'heure affichée ?</h2>
  <p>L'horloge utilise l'heure de votre ordinateur ou de votre téléphone. La plupart des appareils se synchronisent
  automatiquement avec des serveurs de temps (protocole NTP), qui sont eux-mêmes reliés à des horloges atomiques : l'heure
  affichée est donc généralement précise à quelques fractions de seconde près.</p>

  <h2>Format 12 heures ou 24 heures ?</h2>
  <p>En France et dans la majeure partie du monde, on utilise le format 24 heures (14:30). Les pays anglo-saxons, notamment
  les États-Unis, utilisent plutôt le format 12 heures avec les mentions AM (<em>ante meridiem</em>, avant midi) et PM
  (<em>post meridiem</em>, après midi) : 14:30 devient alors 2:30 PM. Minuit s'écrit 12:00 AM et midi 12:00 PM.</p>

  <h2>Les fuseaux horaires</h2>
  <p>La Terre est découpée en fuseaux horaires définis par rapport au temps universel coordonné (UTC). La France
  métropolitaine est à UTC+1 en hiver et UTC+2 en été. Le changement d'heure a lieu le dernier dimanche de mars (on avance
  d'une heure) et le dernier dimanche d'octobre (on recule d'une heure). Les heures mondiales ci-dessus tiennent compte
  automatiquement de l'heure d'été de chaque pays.</p>

  <h2>Utiliser l'horloge en plein écran</h2>
  <p>Cliquez sur « Plein écran » pour afficher uniquement l'horloge, en grand, sur tout l'écran. Appuyez sur la touche Échap
  pour revenir à l'affichage normal. Le format choisi (12 h ou 24 h) est mémorisé par votre navigateur.</p>
</article>

<script>
(function () {
  var h12 = false;
  try { h12 = localStorage.getItem('horloge12') === '1'; } catch (e) {}
  var btn = document.getElementById('toggle-format');
  function render() {
    var now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString(h12 ? 'en-US' : 'fr-FR', { hour12: h12 });
    document.getElementById('clock-date').textContent = now.toLocaleDateString('fr-FR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    document.querySelectorAll('[data-tz]').forEach(function (el) {
      el.textContent = now.toLocaleTimeString(h12 ? 'en-US' : 'fr-FR', { timeZone: el.dataset.tz, hour: '2-digit', minute: '2-digit', hour12: h12 });
    });
    btn.textContent = h12 ? 'Passer au format 24 h' : 'Passer au format 12 h';
  }
  btn.addEventListener('click', function () {
    h12 = !h12;
    try { localStorage.setItem('horloge12', h12 ? '1' : '0'); } catch (e) {}
    render();
  });
  document.getElementById('fullscreen').addEventListener('click', function () {
    var el = document.getElementById('clock');
    if (el.requestFullscreen) el.requestFullscreen();
  });
  render();
  setInterval(render, 1000);
})();
</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
