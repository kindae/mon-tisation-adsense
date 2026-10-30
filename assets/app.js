// Boutons « Copier » : <button type="button" data-copy="#id-de-la-cible">
document.querySelectorAll('[data-copy]').forEach(function (btn) {
  btn.addEventListener('click', function () {
    var target = document.querySelector(btn.getAttribute('data-copy'));
    if (!target) return;
    var text = 'value' in target ? target.value : target.textContent;
    navigator.clipboard.writeText(text.trim()).then(function () {
      var label = btn.textContent;
      btn.textContent = 'Copié ✓';
      setTimeout(function () { btn.textContent = label; }, 1500);
    });
  });
});

// Recherche d'outils sur la page d'accueil
(function () {
  var input = document.getElementById('tool-search');
  if (!input) return;
  var norm = function (s) { return s.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, ''); };
  input.addEventListener('input', function () {
    var q = norm(input.value.trim());
    var visible = 0;
    document.querySelectorAll('.cat-section').forEach(function (section) {
      var shown = 0;
      section.querySelectorAll('.card').forEach(function (card) {
        var match = !q || norm(card.dataset.search).indexOf(q) !== -1;
        card.hidden = !match;
        if (match) shown++;
      });
      section.hidden = shown === 0;
      visible += shown;
    });
    document.getElementById('no-result').hidden = visible > 0;
  });
  input.addEventListener('keydown', function (e) {
    if (e.key !== 'Enter') return;
    var first = document.querySelector('.cat-section:not([hidden]) .card:not([hidden])');
    if (first) location.href = first.href;
  });
})();

// Résultat : faire défiler jusqu'au résultat après un calcul
(function () {
  var r = document.querySelector('.result, .stats, .alert');
  if (r && document.querySelector('form.tool') && performance.getEntriesByType('navigation')[0] &&
      performance.getEntriesByType('navigation')[0].type !== 'reload' && document.referrer.split('?')[0] === location.href.split('?')[0]) {
    r.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
})();
