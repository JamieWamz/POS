(function () {
  'use strict';

  var defaultImage = 'views/img/products/default/anonymous.png';
  var hiddenProfile = $('#hiddenProfile').val();

  $('.productsTable').DataTable({
    ajax: 'ajax/datatable-products.ajax.php?hiddenProfile=' + encodeURIComponent(hiddenProfile || ''),
    deferRender: true,
    retrieve: true,
    processing: true,
    responsive: true
  });

  $('#newCategory').on('change', function () {
    var categoryId = $(this).val();
    if (!categoryId) return;
    $.ajax({
      url: 'ajax/products.ajax.php',
      method: 'POST',
      data: {idCategory: categoryId},
      dataType: 'json'
    }).done(function (answer) {
      var suggested = answer && answer.length ? Number(answer[0].code) + 1 : String(categoryId) + '01';
      $('#newCode').val(Number.isFinite(suggested) ? suggested : String(categoryId) + '01');
    });
  });

  function showImageError(message) {
    swal({title: 'Image not accepted', text: message, type: 'error', confirmButtonText: 'Close'});
  }

  $('.newImage').on('change', function () {
    var input = this;
    var image = input.files && input.files[0];
    if (!image) return;
    if (!['image/jpeg', 'image/png', 'image/webp'].includes(image.type)) {
      input.value = '';
      showImageError('Use a JPEG, PNG or WebP image.');
      return;
    }
    if (image.size > 5 * 1024 * 1024) {
      input.value = '';
      showImageError('Images must be 5 MB or smaller.');
      return;
    }

    var form = input.closest('form');
    var hiddenUrl = form.querySelector('input[name$="ImageUrl"]');
    var preview = form.querySelector('.pos-product-preview img');
    if (hiddenUrl) hiddenUrl.value = '';
    form.querySelectorAll('.pos-image-result').forEach(function (result) { result.classList.remove('selected'); });

    var reader = new FileReader();
    reader.addEventListener('load', function (event) {
      if (preview) preview.src = event.target.result;
    });
    reader.readAsDataURL(image);
  });

  function resultLabel(result) {
    return [result.brand, result.name, result.quantity].filter(Boolean).join(' · ');
  }

  function selectSearchResult(button, result, hiddenUrl, preview, resultsContainer) {
    resultsContainer.querySelectorAll('.pos-image-result').forEach(function (item) { item.classList.remove('selected'); });
    button.classList.add('selected');
    hiddenUrl.value = result.image;
    preview.src = result.image;
    var form = hiddenUrl.closest('form');
    var upload = form ? form.querySelector('.newImage') : null;
    if (upload) upload.value = '';
  }

  $('.findProductImages').on('click', function () {
    var button = this;
    var nameInput = document.getElementById(button.dataset.nameTarget);
    var hiddenUrl = document.getElementById(button.dataset.urlTarget);
    var preview = document.getElementById(button.dataset.previewTarget);
    var resultsContainer = document.getElementById(button.dataset.resultsTarget);
    var query = String(nameInput ? nameInput.value : '').trim();

    if (query.length < 2) {
      showImageError('Enter the product brand and pack size first.');
      if (nameInput) nameInput.focus();
      return;
    }

    button.disabled = true;
    button.innerHTML = '<i class="fa fa-circle-o-notch fa-spin" aria-hidden="true"></i> Searching catalogue…';
    resultsContainer.innerHTML = '<p class="pos-image-status">Looking for “' + $('<div>').text(query).html() + '”…</p>';

    $.ajax({
      url: 'ajax/product-images.ajax.php',
      method: 'POST',
      data: {query: query},
      dataType: 'json'
    }).done(function (payload) {
      resultsContainer.innerHTML = '';
      var results = payload && Array.isArray(payload.results) ? payload.results : [];
      if (!results.length) {
        resultsContainer.innerHTML = '<p class="pos-image-status">No catalogue image matched. Upload a clear product photo instead.</p>';
        return;
      }

      var heading = document.createElement('p');
      heading.className = 'pos-image-status';
      heading.textContent = 'Best match selected. Choose another result if the packaging differs.';
      resultsContainer.appendChild(heading);

      var grid = document.createElement('div');
      grid.className = 'pos-image-results__grid';
      results.forEach(function (result, index) {
        var resultButton = document.createElement('button');
        resultButton.type = 'button';
        resultButton.className = 'pos-image-result';
        resultButton.title = resultLabel(result);

        var image = document.createElement('img');
        image.src = result.image;
        image.alt = '';
        image.loading = 'lazy';
        resultButton.appendChild(image);

        var label = document.createElement('span');
        label.textContent = result.brand || result.name;
        resultButton.appendChild(label);

        resultButton.addEventListener('click', function () {
          selectSearchResult(resultButton, result, hiddenUrl, preview, resultsContainer);
        });
        grid.appendChild(resultButton);

        if (index === 0) {
          selectSearchResult(resultButton, result, hiddenUrl, preview, resultsContainer);
        }
      });
      resultsContainer.appendChild(grid);
    }).fail(function (xhr) {
      var message = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : 'The catalogue search could not be completed. Upload a product photo or try again.';
      resultsContainer.innerHTML = '';
      showImageError(message);
    }).always(function () {
      button.disabled = false;
      button.innerHTML = '<i class="fa fa-search" aria-hidden="true"></i> Search product catalogue';
    });
  });

  $('.productsTable tbody').on('click', 'button.btnEditProduct', function () {
    $.ajax({
      url: 'ajax/products.ajax.php',
      method: 'POST',
      data: {idProduct: $(this).attr('idProduct')},
      dataType: 'json'
    }).done(function (answer) {
      $('#editCategory').val(answer.idCategory);
      $('#editCode').val(answer.code);
      $('#editDescription').val(answer.description);
      $('#editStock').val(answer.stock);
      $('#editBuyingPrice').val(answer.buyingPrice);
      $('#editSellingPrice').val(answer.sellingPrice);
      $('#editImagePreview').attr('src', answer.image || defaultImage);
      $('#editImageUrl').val(/^https:\/\//i.test(answer.image || '') ? answer.image : '');
      $('#editImageResults').empty();
      $('#editImage').val('');
    });
  });

  $('.productsTable tbody').on('click', 'button.btnDeleteProduct', function () {
    var productId = $(this).attr('idProduct');
    swal({
      title: 'Delete this product?',
      text: 'Products linked to completed sales may be retained for financial history.',
      type: 'warning',
      showCancelButton: true,
      cancelButtonText: 'Cancel',
      confirmButtonText: 'Delete product'
    }).then(function (result) {
      if (result.value) submitSecurePost('products', {deleteProductId: productId});
    });
  });
})();
