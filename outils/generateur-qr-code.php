<?php
require __DIR__ . '/../inc/functions.php';
$tool = tool('generateur-qr-code');
include __DIR__ . '/../inc/header.php';
?>
<div class="tool-head">
  <span class="tool-icon"><?= $tool['icon'] ?></span>
  <div>
    <h1><?= e($tool['title']) ?></h1>
    <p class="lead">Créez gratuitement un QR code pour un site web, un texte, un réseau Wi-Fi, un e-mail ou un numéro de téléphone.
Personnalisez ses couleurs et téléchargez-le en image PNG haute résolution. Sans limite et sans date d'expiration.</p>
  </div>
</div>

<div class="tool">
  <nav class="tabs" style="margin-top:0">
    <?php foreach (['url' => '🔗 Lien', 'texte' => '📝 Texte', 'wifi' => '📶 Wi-Fi', 'email' => '✉️ E-mail', 'tel' => '📞 Téléphone', 'sms' => '💬 SMS'] as $k => $l): ?>
      <a href="#<?= $k ?>" data-type="<?= $k ?>" class="<?= $k === 'url' ? 'active' : '' ?>"><?= $l ?></a>
    <?php endforeach; ?>
  </nav>
  <div data-fields="url"><label for="q-url">Adresse du site</label><input type="url" id="q-url" placeholder="https://www.exemple.fr" value="<?= e(cfg('site_url')) ?>"></div>
  <div data-fields="texte" hidden><label for="q-texte">Texte</label><textarea id="q-texte" style="min-height:100px" placeholder="Votre texte…"></textarea></div>
  <div data-fields="wifi" hidden>
    <div class="row">
      <div><label for="q-ssid">Nom du réseau (SSID)</label><input type="text" id="q-ssid"></div>
      <div><label for="q-pass">Mot de passe</label><input type="text" id="q-pass"></div>
      <div><label for="q-enc">Sécurité</label><select id="q-enc"><option value="WPA">WPA / WPA2 / WPA3</option><option value="WEP">WEP</option><option value="nopass">Aucune</option></select></div>
    </div>
  </div>
  <div data-fields="email" hidden>
    <div class="row">
      <div><label for="q-mail">Adresse e-mail</label><input type="email" id="q-mail"></div>
      <div><label for="q-subj">Objet</label><input type="text" id="q-subj"></div>
    </div>
    <label for="q-body">Message</label><textarea id="q-body" style="min-height:80px"></textarea>
  </div>
  <div data-fields="tel" hidden><label for="q-tel">Numéro de téléphone</label><input type="text" id="q-tel" placeholder="+33 6 12 34 56 78"></div>
  <div data-fields="sms" hidden>
    <div class="row">
      <div><label for="q-smsnum">Numéro</label><input type="text" id="q-smsnum"></div>
      <div><label for="q-smsmsg">Message</label><input type="text" id="q-smsmsg"></div>
    </div>
  </div>

  <div class="row">
    <div><label for="q-fg">Couleur</label><input type="color" id="q-fg" value="#0f172a"></div>
    <div><label for="q-bg">Fond</label><input type="color" id="q-bg" value="#ffffff"></div>
    <div><label for="q-ec">Correction d'erreur</label><select id="q-ec"><option value="L">Faible (7 %)</option><option value="M" selected>Moyenne (15 %)</option><option value="Q">Élevée (25 %)</option><option value="H">Maximale (30 %)</option></select></div>
    <div><label for="q-size">Taille de l'image</label><select id="q-size"><option value="512">512 px</option><option value="1024" selected>1024 px</option><option value="2048">2048 px</option></select></div>
  </div>

  <div class="qr-out" style="margin-top:20px">
    <canvas id="qr-canvas" width="1024" height="1024" style="width:240px; height:240px; image-rendering:pixelated"></canvas>
    <p id="qr-msg" class="muted" style="margin:0; color:#5b6478"></p>
    <a class="btn" id="qr-dl" download="qr-code.png" href="#">⬇️ Télécharger le PNG</a>
  </div>
</div>

<?php ad('in_content'); ?>

<article class="content">
  <h2>Qu'est-ce qu'un QR code ?</h2>
  <p>Le QR code (<em>Quick Response code</em>) est un code-barres en deux dimensions inventé en 1994 par l'entreprise japonaise
  Denso Wave pour suivre des pièces automobiles. Il peut contenir jusqu'à plusieurs milliers de caractères et se lit en une
  fraction de seconde avec l'appareil photo de n'importe quel smartphone récent.</p>

  <h2>Les types de QR codes</h2>
  <ul>
    <li><strong>Lien</strong> : ouvre un site web, un menu de restaurant, une page de réseau social ou un formulaire.</li>
    <li><strong>Wi-Fi</strong> : vos invités se connectent à votre réseau sans avoir à taper le mot de passe.</li>
    <li><strong>E-mail et SMS</strong> : préremplit un message, pratique pour un service client ou un jeu-concours.</li>
    <li><strong>Téléphone</strong> : lance un appel en un geste, idéal sur une carte de visite ou une affiche.</li>
  </ul>

  <h2>Nos QR codes expirent-ils ?</h2>
  <p>Non. Ce générateur crée des QR codes <strong>statiques</strong> : l'information est inscrite directement dans le dessin du
  code, sans passer par un service de redirection. Ils fonctionneront donc indéfiniment, gratuitement, et sans suivi de vos
  utilisateurs. La contrepartie : on ne peut pas modifier la destination d'un QR code déjà imprimé.</p>

  <h2>Conseils pour un QR code bien lisible</h2>
  <ul>
    <li>Gardez un <strong>fort contraste</strong> : couleur foncée sur fond clair. Les QR codes inversés (clair sur foncé) ne
    sont pas lus par tous les téléphones.</li>
    <li>Respectez une <strong>taille minimale</strong> d'environ 2 × 2 cm à l'impression, plus si le code sera scanné de loin.</li>
    <li>Augmentez la <strong>correction d'erreur</strong> si le code risque d'être abîmé ou si vous comptez placer un logo au centre.</li>
    <li><strong>Testez toujours</strong> votre QR code avec plusieurs téléphones avant d'imprimer en grande quantité.</li>
  </ul>

  <h2>Vos données restent privées</h2>
  <p>Le QR code est généré directement dans votre navigateur : le contenu saisi (y compris le mot de passe Wi-Fi) n'est jamais
  envoyé à notre serveur.</p>
</article>

<script src="<?= e(url('assets/vendor/qrcode.js')) ?>"></script>
<script>
(function () {
  qrcode.stringToBytes = qrcode.stringToBytesFuncs['UTF-8'];
  var type = 'url';
  var $ = function (id) { return document.getElementById(id); };
  var esc = function (s) { return s.replace(/([\;,:"])/g, '\\$1'); };
  function content() {
    switch (type) {
      case 'url': return $('q-url').value.trim();
      case 'texte': return $('q-texte').value;
      case 'wifi': return $('q-ssid').value ? 'WIFI:T:' + $('q-enc').value + ';S:' + esc($('q-ssid').value) + ';' +
        ($('q-enc').value === 'nopass' ? '' : 'P:' + esc($('q-pass').value) + ';') + ';' : '';
      case 'email': return $('q-mail').value ? 'mailto:' + $('q-mail').value.trim() + '?subject=' + encodeURIComponent($('q-subj').value) +
        '&body=' + encodeURIComponent($('q-body').value) : '';
      case 'tel': return $('q-tel').value ? 'tel:' + $('q-tel').value.replace(/[^\d+]/g, '') : '';
      case 'sms': return $('q-smsnum').value ? 'SMSTO:' + $('q-smsnum').value.replace(/[^\d+]/g, '') + ':' + $('q-smsmsg').value : '';
    }
  }
  function draw() {
    var canvas = $('qr-canvas'), ctx = canvas.getContext('2d'), size = +$('q-size').value, text = content();
    canvas.width = canvas.height = size;
    ctx.fillStyle = $('q-bg').value; ctx.fillRect(0, 0, size, size);
    if (!text) { $('qr-msg').textContent = 'Remplissez le formulaire pour générer votre QR code.'; $('qr-dl').hidden = true; return; }
    try {
      var qr = qrcode(0, $('q-ec').value); qr.addData(text); qr.make();
      var n = qr.getModuleCount(), quiet = 4, cell = size / (n + quiet * 2);
      ctx.fillStyle = $('q-fg').value;
      for (var r = 0; r < n; r++) for (var c = 0; c < n; c++) {
        if (qr.isDark(r, c)) ctx.fillRect(Math.floor((c + quiet) * cell), Math.floor((r + quiet) * cell), Math.ceil(cell), Math.ceil(cell));
      }
      $('qr-msg').textContent = text.length + ' caractère' + (text.length > 1 ? 's' : '') + ' · version ' + ((n - 17) / 4);
      $('qr-dl').hidden = false; $('qr-dl').href = canvas.toDataURL('image/png');
    } catch (e) {
      $('qr-msg').textContent = 'Contenu trop long pour un QR code : réduisez le texte ou baissez la correction d\'erreur.';
      $('qr-dl').hidden = true;
    }
  }
  document.querySelectorAll('[data-type]').forEach(function (a) {
    a.addEventListener('click', function (e) {
      e.preventDefault(); type = a.dataset.type;
      document.querySelectorAll('[data-type]').forEach(function (x) { x.classList.toggle('active', x === a); });
      document.querySelectorAll('[data-fields]').forEach(function (f) { f.hidden = f.dataset.fields !== type; });
      draw();
    });
  });
  document.querySelectorAll('.tool input, .tool textarea, .tool select').forEach(function (el) { el.addEventListener('input', draw); });
  draw();
})();
</script>
<?php include __DIR__ . '/../inc/footer.php'; ?>
