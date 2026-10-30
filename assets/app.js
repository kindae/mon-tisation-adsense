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
