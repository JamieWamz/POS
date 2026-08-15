(function ($) {
  'use strict';

  var placeholder = 'views/img/users/default/prfplaceholder.png';
  var allowedPhotoTypes = ['image/jpeg', 'image/png', 'image/webp'];
  var maxPhotoBytes = 5 * 1024 * 1024;

  function showError(title, message) {
    swal({type: 'error', title: title, text: message, confirmButtonText: 'Close'});
  }

  function resetPreview(input) {
    var preview = document.getElementById(input.dataset.previewTarget || '');
    input.value = '';
    if (preview) preview.src = preview.dataset.savedSource || placeholder;
  }

  $(document).on('change', '.pos-user-photo-input', function () {
    var input = this;
    var file = input.files && input.files[0];
    var preview = document.getElementById(input.dataset.previewTarget || '');
    if (!file || !preview) return;

    if (allowedPhotoTypes.indexOf(file.type) === -1) {
      resetPreview(input);
      showError('Unsupported photo', 'Choose a JPEG, PNG or WebP image.');
      return;
    }
    if (file.size > maxPhotoBytes) {
      resetPreview(input);
      showError('Photo is too large', 'Choose an image that is 5 MB or smaller.');
      return;
    }

    var reader = new FileReader();
    reader.addEventListener('load', function (event) {
      preview.src = event.target.result;
      if (input.id === 'editPhoto') $('#removePhoto').prop('checked', false);
    });
    reader.readAsDataURL(file);
  });

  $('#removePhoto').on('change', function () {
    if (!this.checked) return;
    var input = document.getElementById('editPhoto');
    if (input) input.value = '';
    $('#editUserPhotoPreview').attr('src', placeholder);
  });

  $(document).on('click', '.btnEditUser', function () {
    var idUser = $(this).data('user-id');
    $.ajax({
      url: 'ajax/users.ajax.php',
      method: 'POST',
      data: {idUser: idUser},
      dataType: 'json'
    }).done(function (answer) {
      $('#EditName').val(answer.name || '');
      $('#EditUser').val(answer.user || '');
      $('#EditProfile').val(answer.profile || '');
      $('#EditPasswd').val('');
      $('#editPhoto').val('');
      $('#removePhoto').prop('checked', false);
      $('#editUserPhotoPreview')
        .attr('src', answer.photo || placeholder)
        .attr('data-saved-source', answer.photo || placeholder);
    }).fail(function (xhr) {
      showError('User could not be loaded', (xhr.responseJSON && xhr.responseJSON.error) || 'Refresh the page and try again.');
    });
  });

  $(document).on('click', '.btnActivate', function () {
    var button = $(this);
    var nextStatus = Number(button.data('user-status'));
    button.prop('disabled', true);
    $.ajax({
      url: 'ajax/users.ajax.php',
      method: 'POST',
      data: {activateId: button.data('user-id'), activateUser: nextStatus},
      dataType: 'json'
    }).done(function (answer) {
      if (!answer.ok) {
        showError('Status was not changed', answer.error || 'Refresh the page and try again.');
        return;
      }
      var nowActive = nextStatus === 1;
      button.toggleClass('btn-success', nowActive).toggleClass('btn-danger', !nowActive);
      button.text(nowActive ? 'Active' : 'Inactive');
      button.data('user-status', nowActive ? 0 : 1);
    }).fail(function (xhr) {
      showError('Status was not changed', (xhr.responseJSON && xhr.responseJSON.error) || 'Refresh the page and try again.');
    }).always(function () {
      button.prop('disabled', false);
    });
  });

  $('#newUser').on('change', function () {
    var input = $(this);
    var feedback = $('#newUserFeedback');
    $.ajax({
      url: 'ajax/users.ajax.php',
      method: 'POST',
      data: {validateUser: input.val()},
      dataType: 'json'
    }).done(function (answer) {
      if (answer) {
        input.val('').addClass('is-invalid');
        feedback.text('That username is already in use.').removeClass('text-success').addClass('text-danger');
      } else {
        input.removeClass('is-invalid');
        feedback.text('Username is available.').removeClass('text-danger').addClass('text-success');
      }
    });
  });

  $(document).on('click', '.btnDeleteUser', function () {
    var userId = $(this).data('user-id');
    swal({
      title: 'Delete this team member?',
      text: 'Accounts linked to financial history cannot be deleted.',
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#8f2f2f',
      cancelButtonText: 'Cancel',
      confirmButtonText: 'Delete account'
    }).then(function (result) {
      if (result.value) submitSecurePost('users', {deleteUserId: userId});
    });
  });

  $('#addUser').on('hidden.bs.modal', function () {
    var form = this.querySelector('form');
    if (form) form.reset();
    $('#newUserPhotoPreview').attr('src', placeholder);
    $('#newUserFeedback').text('Letters, numbers, dots, underscores and hyphens.').removeClass('text-danger text-success');
  });
})(jQuery);
