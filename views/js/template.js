(function () {
  'use strict';

  function closeNavigation() {
    document.body.classList.remove('pos-nav-open');
    var toggle = document.querySelector('[data-pos-nav-toggle]');
    if (toggle) toggle.setAttribute('aria-expanded', 'false');
  }

  document.addEventListener('click', function (event) {
    var toggle = event.target.closest('[data-pos-nav-toggle]');
    if (toggle) {
      var open = document.body.classList.toggle('pos-nav-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      return;
    }

    if (event.target.closest('[data-pos-nav-close]') || event.target.closest('.pos-nav-link')) {
      closeNavigation();
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') closeNavigation();
  });

  window.addEventListener('resize', function () {
    if (window.innerWidth >= 992) closeNavigation();
  });

  if (window.jQuery) {
    if ($.fn.dataTable) $('.tables').DataTable({retrieve: true, responsive: true});

    if ($.fn.iCheck) {
      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
      });
    }

    if ($.fn.inputmask) {
      $('#datemask').inputmask('dd/mm/yyyy', {'placeholder': 'dd/mm/yyyy'});
      $('#datemask2').inputmask('mm/dd/yyyy', {'placeholder': 'mm/dd/yyyy'});
      $('[data-mask]').inputmask();
    }
  }
})();
