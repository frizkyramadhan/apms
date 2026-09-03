/**
 * Add Permission Modal JS
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  var form = document.getElementById('addPermissionForm');
  if (!form) return;

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  var selectAll = document.getElementById('addSelectAllRoles');
  var roleBoxes = document.querySelectorAll('.add-permission-role');
  if (selectAll) {
    selectAll.addEventListener('change', function () {
      roleBoxes.forEach(function (el) {
        el.checked = selectAll.checked;
      });
    });
  }

  FormValidation.formValidation(form, {
    fields: {
      name: {
        validators: {
          notEmpty: { message: 'Please enter permission name' }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: '.col-12'
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    $.ajax({
      data: $(form).serialize(),
      url: window.apmsPermissions.store,
      type: 'POST',
      success: function () {
        bootstrap.Modal.getInstance(document.getElementById('addPermissionModal')).hide();
        form.reset();
        if (selectAll) selectAll.checked = false;
        if (window.apmsPermissionTable) window.apmsPermissionTable.ajax.reload(null, false);
        Swal.fire({
          icon: 'success',
          title: 'Successfully Created!',
          text: 'Permission Created Successfully.',
          customClass: { confirmButton: 'btn btn-success' }
        });
      },
      error: function (xhr) {
        var msg =
          (xhr.responseJSON && (xhr.responseJSON.message || Object.values(xhr.responseJSON.errors || {})[0])) ||
          'Request failed.';
        if (Array.isArray(msg)) msg = msg[0];
        Swal.fire({ icon: 'error', title: 'Error', text: msg, customClass: { confirmButton: 'btn btn-success' } });
      }
    });
  });
});
