/**
 * Add new role Modal JS
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {
  var addRoleForm = document.getElementById('addRoleForm');
  if (!addRoleForm) return;

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  FormValidation.formValidation(addRoleForm, {
    fields: {
      name: {
        validators: {
          notEmpty: { message: 'Please enter role name' }
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
    var roleId = $('#role_id').val();
    var payload = $('#addRoleForm').serialize();
    if (roleId) payload += '&_method=PUT';
    $.ajax({
      data: payload,
      url: roleId ? window.apmsRoles.update + '/' + roleId : window.apmsRoles.store,
      type: 'POST',
      success: function () {
        location.reload();
      },
      error: function (xhr) {
        var msg =
          (xhr.responseJSON && (xhr.responseJSON.message || Object.values(xhr.responseJSON.errors || {})[0])) ||
          'Request failed.';
        if (Array.isArray(msg)) msg = msg[0];
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: msg,
          customClass: { confirmButton: 'btn btn-success' }
        });
      }
    });
  });

  const selectAll = document.querySelector('#selectAll');
  const checkboxList = document.querySelectorAll('.role-permission');
  const groupSelects = document.querySelectorAll('.role-group-select');

  function syncGroupSelect(group) {
    var boxes = document.querySelectorAll('.role-permission[data-group="' + group + '"]');
    var master = document.querySelector('.role-group-select[data-group="' + group + '"]');
    if (!master) return;
    master.checked = boxes.length > 0 && Array.prototype.every.call(boxes, function (el) {
      return el.checked;
    });
  }

  function syncRolePermissionSelects() {
    groupSelects.forEach(function (master) {
      syncGroupSelect(master.getAttribute('data-group'));
    });
    if (selectAll) {
      selectAll.checked =
        checkboxList.length > 0 &&
        Array.prototype.every.call(checkboxList, function (el) {
          return el.checked;
        });
    }
  }

  window.syncRolePermissionSelects = syncRolePermissionSelects;

  if (selectAll) {
    selectAll.addEventListener('change', function () {
      checkboxList.forEach(function (e) {
        e.checked = selectAll.checked;
      });
      groupSelects.forEach(function (el) {
        el.checked = selectAll.checked;
      });
    });
  }

  groupSelects.forEach(function (master) {
    master.addEventListener('change', function () {
      document.querySelectorAll('.role-permission[data-group="' + master.getAttribute('data-group') + '"]').forEach(function (el) {
        el.checked = master.checked;
      });
      syncRolePermissionSelects();
    });
  });

  checkboxList.forEach(function (el) {
    el.addEventListener('change', function () {
      syncRolePermissionSelects();
    });
  });
});
