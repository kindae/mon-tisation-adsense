<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('chronometre-minuteur');
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Un chronomètre précis au centième avec enregistrement des tours, et un minuteur (compte à rebours) avec
alarme sonore. Fonctionne directement dans le navigateur, même en plein écran.</p>
  </div>
</div>

<nav class="tabs" role="tablist">
  <a href="#chrono" class="active" data-tab="chrono">⏱️ Chronomètre</a>
  <a href="#minuteur" data-tab="minuteur">⏳ Minuteur</a>
</nav>

<section class="tool fs" id="panel-chrono">
  <div class="clock" id="chrono-display">00:00.00</div>
  <p class="center">
    <button type="button" id="chrono-start">Démarrer</button>
    <button type="button" class="secondary" id="chrono-lap" disabled>Tour</button>
    <button type="button" class="secondary" id="chrono-reset">Réinitialiser</button>
    <button type="button" class="secondary" data-fs="#panel-chrono">Plein écran</button>
  </p>
  <div class="lap-list"><table id="laps" hidden><thead><tr><th>Tour</th><th>Temps du tour</th><th>Temps total</th></tr></thead><tbody></tbody></table></div>
</section>

<section class="tool fs" id="panel-minuteur" hidden>
  <div class="row" id="timer-inputs">
    <div><label for="t-h">Heures</label><input type="number" id="t-h" min="0" max="99" value="0"></div>
    <div><label for="t-m">Minutes</label><input type="number" id="t-m" min="0" max="59" value="5"></div>
    <div><label for="t-s">Secondes</label><input type="number" id="t-s" min="0" max="59" value="0"></div>
  </div>
  <p class="center" style="margin:10px 0 0">
    <?php foreach ([1 => '1 min', 3 => '3 min', 5 => '5 min', 10 => '10 min', 15 => '15 min', 25 => '25 min', 30 => '30 min', 60 => '1 h'] as $m => $l): ?>
      <button type="button" class="secondary btn small" data-preset="<?= $m ?>"><?= $l ?></button>
    <?php endforeach; ?>
  </p>
  <div class="clock" id="timer-display">05:00</div>
  <p class="center">
    <button type="button" id="timer-start">Démarrer</button>
    <button type="button" class="secondary" id="timer-reset">Réinitialiser</button>
    <button type="button" class="secondary" data-fs="#panel-minuteur">Plein écran</button>
  </p>
</section>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Comment utiliser le chronomètre ?</h2>
  <p>Cliquez sur « Démarrer » pour lancer le chronomètre, puis sur « Pause » pour l'arrêter. Le bouton « Tour » enregistre un
  temps intermédiaire sans arrêter le chronomètre : pratique pour mesurer chaque tour de piste, chaque longueur de piscine ou
  chaque étape d'une recette. Le meilleur tour est mis en évidence.</p>
  <p>Raccourci clavier : la <strong>barre d'espace</strong> démarre ou met en pause l'outil affiché.</p>

  <h2>Comment utiliser le minuteur ?</h2>
  <p>Réglez la durée avec les champs heures, minutes et secondes, ou choisissez une durée prédéfinie, puis cliquez sur
  « Démarrer ». Une alarme sonore retentit et le titre de l'onglet clignote à la fin du compte à rebours, même si vous
  consultez un autre onglet. Pensez à activer le son de votre appareil.</p>

  <h2>Idées d'utilisation</h2>
  <ul>
    <li><strong>Technique Pomodoro</strong> : travaillez 25 minutes, puis faites une pause de 5 minutes. Après quatre cycles,
    accordez-vous une pause plus longue de 15 à 30 minutes.</li>
    <li><strong>Cuisine</strong> : temps de cuisson des pâtes, des œufs (3 minutes pour un œuf à la coque, 9 pour un œuf dur).</li>
    <li><strong>Sport</strong> : fractionné, gainage, temps de récupération entre deux séries.</li>
    <li><strong>Classe et réunions</strong> : en plein écran, le minuteur aide à gérer le temps de parole ou d'un exercice.</li>
  </ul>

  <h2>Quelle est la précision ?</h2>
  <p>Le chronomètre se base sur l'horloge haute précision de votre navigateur et affiche les centièmes de seconde. Il reste
  exact même si l'onglet passe en arrière-plan, car le temps écoulé est recalculé à partir de l'heure de départ plutôt que
  compté seconde par seconde.</p>
</article>

<script>
(function () {
  var pad = function (n, l) { return String(n).padStart(l || 2, '0'); };
  var fmtChrono = function (ms) {
    var h = Math.floor(ms / 3600000), m = Math.floor(ms / 60000) % 60, s = Math.floor(ms / 1000) % 60, c = Math.floor(ms / 10) % 100;
    return (h ? h + ':' : '') + pad(m) + ':' + pad(s) + '.' + pad(c);
  };
  var fmtTimer = function (sec) {
    var h = Math.floor(sec / 3600), m = Math.floor(sec / 60) % 60, s = sec % 60;
    return (h ? h + ':' : '') + pad(m) + ':' + pad(s);
  };

  // Onglets
  var current = 'chrono';
  function show(tab) {
    current = tab;
    document.querySelectorAll('[data-tab]').forEach(function (a) { a.classList.toggle('active', a.dataset.tab === tab); });
    document.getElementById('panel-chrono').hidden = tab !== 'chrono';
    document.getElementById('panel-minuteur').hidden = tab !== 'minuteur';
  }
  document.querySelectorAll('[data-tab]').forEach(function (a) {
    a.addEventListener('click', function (e) { e.preventDefault(); show(a.dataset.tab); history.replaceState(null, '', '#' + a.dataset.tab); });
  });
  if (location.hash === '#minuteur') show('minuteur');
  document.querySelectorAll('[data-fs]').forEach(function (b) {
    b.addEventListener('click', function () { var el = document.querySelector(b.dataset.fs); if (el.requestFullscreen) el.requestFullscreen(); });
  });

  // Chronomètre
  var cDisp = document.getElementById('chrono-display'), cStart = document.getElementById('chrono-start'), cLap = document.getElementById('chrono-lap');
  var startAt = 0, elapsed = 0, running = false, raf = null, laps = [];
  function tick() { cDisp.textContent = fmtChrono(elapsed + (running ? performance.now() - startAt : 0)); if (running) raf = requestAnimationFrame(tick); }
  function toggleChrono() {
    if (running) { elapsed += performance.now() - startAt; running = false; cancelAnimationFrame(raf); cStart.textContent = 'Reprendre'; }
    else { startAt = performance.now(); running = true; cStart.textContent = 'Pause'; tick(); }
    cLap.disabled = !running;
  }
  cStart.addEventListener('click', toggleChrono);
  cLap.addEventListener('click', function () {
    var total = elapsed + performance.now() - startAt;
    var prev = laps.length ? laps[laps.length - 1] : 0;
    laps.push(total);
    var table = document.getElementById('laps'); table.hidden = false;
    var tr = document.createElement('tr');
    tr.innerHTML = '<td>' + laps.length + '</td><td>' + fmtChrono(total - prev) + '</td><td>' + fmtChrono(total) + '</td>';
    tr.dataset.lap = total - prev;
    table.tBodies[0].prepend(tr);
    var rows = Array.from(table.tBodies[0].rows), best = Math.min.apply(null, rows.map(function (r) { return +r.dataset.lap; }));
    rows.forEach(function (r) { r.style.fontWeight = +r.dataset.lap === best && rows.length > 1 ? '700' : ''; r.style.color = +r.dataset.lap === best && rows.length > 1 ? 'var(--ok)' : ''; });
  });
  document.getElementById('chrono-reset').addEventListener('click', function () {
    running = false; cancelAnimationFrame(raf); elapsed = 0; laps = [];
    cStart.textContent = 'Démarrer'; cLap.disabled = true; cDisp.textContent = fmtChrono(0);
    var t = document.getElementById('laps'); t.tBodies[0].innerHTML = ''; t.hidden = true;
  });

  // Minuteur
  var tDisp = document.getElementById('timer-display'), tStart = document.getElementById('timer-start');
  var inputs = ['t-h', 't-m', 't-s'].map(function (id) { return document.getElementById(id); });
  var endAt = 0, remaining = 300, tRunning = false, interval = null, blink = null, title = document.title, audio = null;
  function readInputs() { remaining = (+inputs[0].value || 0) * 3600 + (+inputs[1].value || 0) * 60 + (+inputs[2].value || 0); tDisp.textContent = fmtTimer(remaining); }
  inputs.forEach(function (i) { i.addEventListener('input', function () { if (!tRunning) readInputs(); }); });
  document.querySelectorAll('[data-preset]').forEach(function (b) {
    b.addEventListener('click', function () {
      var m = +b.dataset.preset; inputs[0].value = Math.floor(m / 60); inputs[1].value = m % 60; inputs[2].value = 0;
      stopTimer(); readInputs();
    });
  });
  function beep() {
    try {
      audio = audio || new (window.AudioContext || window.webkitAudioContext)();
      [0, 0.35, 0.7, 1.4, 1.75, 2.1].forEach(function (t) {
        var o = audio.createOscillator(), g = audio.createGain();
        o.frequency.value = 880; o.connect(g); g.connect(audio.destination);
        g.gain.setValueAtTime(0.3, audio.currentTime + t); g.gain.exponentialRampToValueAtTime(0.001, audio.currentTime + t + 0.3);
        o.start(audio.currentTime + t); o.stop(audio.currentTime + t + 0.3);
      });
    } catch (e) {}
  }
  function stopTimer() { tRunning = false; clearInterval(interval); clearInterval(blink); document.title = title; tStart.textContent = 'Démarrer'; }
  function update() {
    var left = Math.max(0, Math.ceil((endAt - Date.now()) / 1000));
    tDisp.textContent = fmtTimer(left);
    if (left === 0) {
      stopTimer(); remaining = 0; beep();
      var on = false; blink = setInterval(function () { document.title = (on = !on) ? '⏰ Temps écoulé !' : title; }, 800);
      setTimeout(function () { clearInterval(blink); document.title = title; }, 15000);
    }
  }
  function toggleTimer() {
    if (tRunning) { remaining = Math.max(0, Math.ceil((endAt - Date.now()) / 1000)); stopTimer(); tStart.textContent = 'Reprendre'; return; }
    if (remaining <= 0) readInputs();
    if (remaining <= 0) return;
    try { audio = audio || new (window.AudioContext || window.webkitAudioContext)(); audio.resume(); } catch (e) {}
    endAt = Date.now() + remaining * 1000; tRunning = true; tStart.textContent = 'Pause';
    update(); interval = setInterval(update, 250);
  }
  tStart.addEventListener('click', toggleTimer);
  document.getElementById('timer-reset').addEventListener('click', function () { stopTimer(); readInputs(); });

  document.addEventListener('keydown', function (e) {
    if (e.code !== 'Space' || /INPUT|TEXTAREA|BUTTON|SELECT/.test(document.activeElement.tagName)) return;
    e.preventDefault(); current === 'chrono' ? toggleChrono() : toggleTimer();
  });
})();
</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
