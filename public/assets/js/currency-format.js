/**
 * Wires a visible "Rp"-prefixed text input to format its value with
 * thousand separators (Indonesian style, e.g. 750.000) as the user types,
 * while a paired hidden input holds the plain numeric value that actually
 * gets submitted.
 *
 * Markup: a visible input with data-currency-display="<hidden field name>"
 * plus a hidden input with that name in the same form.
 */
(function () {
  function formatRibuan(digits) {
    return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  function init(root) {
    root = root || document;

    root.querySelectorAll('[data-currency-display]').forEach(function (display) {
      var hidden = root.querySelector('[name="' + display.dataset.currencyDisplay + '"]');
      if (!hidden) {
        return;
      }

      if (hidden.value) {
        display.value = formatRibuan(String(hidden.value).replace(/\D/g, ''));
      }

      display.addEventListener('input', function () {
        var digits = display.value.replace(/\D/g, '');
        display.value = formatRibuan(digits);
        hidden.value = digits;
      });
    });
  }

  window.CurrencyFormat = { init: init };
})();
