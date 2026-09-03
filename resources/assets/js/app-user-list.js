/**
 * Page User List
 */

'use strict';

$(function () {
  let borderColor, bodyBg, headingColor;

  if (isDarkStyle) {
    borderColor = config.colors_dark.borderColor;
    bodyBg = config.colors_dark.bodyBg;
    headingColor = config.colors_dark.headingColor;
  } else {
    borderColor = config.colors.borderColor;
    bodyBg = config.colors.bodyBg;
    headingColor = config.colors.headingColor;
  }

  var dt_user_table = $('.datatables-users'),
    dt_user,
    select2 = $('.select2'),
    offCanvasForm = $('#offcanvasAddUser'),
    statusObj = {
      Active: { title: 'Active', class: 'bg-label-success' },
      Inactive: { title: 'Inactive', class: 'bg-label-secondary' }
    };

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  if (select2.length) {
    select2.each(function () {
      var $this = $(this);
      $this.wrap('<div class="position-relative"></div>').select2({
        placeholder: $this.attr('multiple') ? 'Select' : 'Select',
        dropdownParent: $this.parent(),
        closeOnSelect: !$this.attr('multiple')
      });
    });
  }

  function escapeHtml(text) {
    return $('<div>').text(text == null ? '' : String(text)).html();
  }

  function prettyRole(role) {
    return String(role || '-').replace(/_/g, ' ').replace(/\b\w/g, function (c) {
      return c.toUpperCase();
    });
  }

  function roleBadge(role) {
    var icons = {
      administrator: ['secondary', 'ti-device-laptop'],
      plant_foreman: ['success', 'ti-settings'],
      plant_superintendent: ['primary', 'ti-chart-pie-2'],
      production_superintendent: ['info', 'ti-edit'],
      project_manager: ['warning', 'ti-briefcase'],
      plant_manager: ['danger', 'ti-building'],
      operational_gm: ['primary', 'ti-users'],
      operational_director: ['secondary', 'ti-crown'],
      commercial_treasury_director: ['info', 'ti-currency-dollar'],
      president_director: ['warning', 'ti-star'],
      logistics: ['secondary', 'ti-truck']
    };
    var pair = icons[role] || ['warning', 'ti-user'];
    return (
      '<span class="badge badge-center rounded-pill bg-label-' +
      pair[0] +
      ' w-px-30 h-px-30 me-2"><i class="ti ' +
      pair[1] +
      ' ti-sm"></i></span>'
    );
  }

  function initials(name) {
    var parts = String(name || '?').match(/\b\w/g) || [];
    return ((parts.shift() || '') + (parts.pop() || '')).toUpperCase() || '?';
  }

  function avatarState(name) {
    var states = ['success', 'danger', 'warning', 'info', 'primary', 'secondary'];
    return states[(String(name).charCodeAt(0) || 0) % states.length];
  }

  function exportBody(inner) {
    if (inner.length <= 0) return inner;
    var el = $.parseHTML(inner);
    var result = '';
    $.each(el, function (index, item) {
      if (item.classList !== undefined && item.classList.contains('user-name')) {
        result = result + item.lastChild.firstChild.textContent;
      } else if (item.innerText === undefined) {
        result = result + item.textContent;
      } else result = result + item.innerText;
    });
    return result;
  }

  if (dt_user_table.length) {
    dt_user = dt_user_table.DataTable({
      ajax: {
        url: window.apmsUsers.data,
        dataSrc: 'data'
      },
      columns: [
        { data: '' },
        { data: 'full_name' },
        { data: 'role' },
        { data: 'current_plan' },
        { data: 'billing' },
        { data: 'status' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          className: 'control',
          searchable: false,
          orderable: false,
          responsivePriority: 2,
          targets: 0,
          render: function () {
            return '';
          }
        },
        {
          targets: 1,
          responsivePriority: 4,
          render: function (data, type, full) {
            var name = full.full_name;
            var email = full.email;
            var output =
              '<span class="avatar-initial rounded-circle bg-label-' +
              avatarState(name) +
              '">' +
              escapeHtml(initials(name)) +
              '</span>';
            return (
              '<div class="d-flex justify-content-start align-items-center user-name">' +
              '<div class="avatar-wrapper"><div class="avatar me-3">' +
              output +
              '</div></div>' +
              '<div class="d-flex flex-column">' +
              '<a href="' +
              window.apmsUsers.show +
              '/' +
              full.id +
              '" class="text-body text-truncate"><span class="fw-medium">' +
              escapeHtml(name) +
              '</span></a>' +
              '<small class="text-muted">' +
              escapeHtml(email) +
              '</small></div></div>'
            );
          }
        },
        {
          targets: 2,
          render: function (data, type, full) {
            var role = full.role;
            if (!role) return '<span class="text-muted">-</span>';
            return (
              "<span class='text-truncate d-flex align-items-center'>" +
              roleBadge(role) +
              escapeHtml(prettyRole(role)) +
              '</span>'
            );
          }
        },
        {
          targets: 3,
          render: function (data, type, full) {
            return '<span class="fw-medium">' + escapeHtml(full.current_plan) + '</span>';
          }
        },
        {
          targets: 5,
          render: function (data, type, full) {
            var status = full.status;
            return (
              '<span class="badge ' +
              statusObj[status].class +
              '" text-capitalized>' +
              statusObj[status].title +
              '</span>'
            );
          }
        },
        {
          targets: -1,
          title: 'Actions',
          searchable: false,
          orderable: false,
          render: function (data, type, full) {
            return (
              '<div class="d-flex align-items-center">' +
              '<a href="javascript:;" class="text-body edit-record" data-id="' +
              full.id +
              '" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser"><i class="ti ti-edit ti-sm me-2"></i></a>' +
              '<a href="javascript:;" class="text-body delete-record" data-id="' +
              full.id +
              '"><i class="ti ti-trash ti-sm mx-2"></i></a>' +
              '<a href="javascript:;" class="text-body dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical ti-sm mx-1"></i></a>' +
              '<div class="dropdown-menu dropdown-menu-end m-0">' +
              '<a href="' +
              window.apmsUsers.show +
              '/' +
              full.id +
              '" class="dropdown-item">View</a>' +
              '<a href="javascript:;" class="dropdown-item suspend-record" data-id="' +
              full.id +
              '">Suspend</a>' +
              '</div></div>'
            );
          }
        }
      ],
      order: [[1, 'desc']],
      dom:
        '<"row me-2"' +
        '<"col-md-2"<"me-3"l>>' +
        '<"col-md-10"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-end flex-md-row flex-column mb-3 mb-md-0"fB>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: '_MENU_',
        search: '',
        searchPlaceholder: 'Search..'
      },
      buttons: [
        {
          extend: 'collection',
          className: 'btn btn-label-secondary dropdown-toggle mx-3 waves-effect waves-light',
          text: '<i class="ti ti-screen-share me-1 ti-xs"></i>Export',
          buttons: [
            {
              extend: 'print',
              text: '<i class="ti ti-printer me-2" ></i>Print',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5], format: { body: exportBody } },
              customize: function (win) {
                $(win.document.body)
                  .css('color', headingColor)
                  .css('border-color', borderColor)
                  .css('background-color', bodyBg);
                $(win.document.body)
                  .find('table')
                  .addClass('compact')
                  .css('color', 'inherit')
                  .css('border-color', 'inherit')
                  .css('background-color', 'inherit');
              }
            },
            {
              extend: 'csv',
              text: '<i class="ti ti-file-text me-2" ></i>Csv',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5], format: { body: exportBody } }
            },
            {
              extend: 'excel',
              text: '<i class="ti ti-file-spreadsheet me-2"></i>Excel',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5], format: { body: exportBody } }
            },
            {
              extend: 'pdf',
              text: '<i class="ti ti-file-code-2 me-2"></i>Pdf',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5], format: { body: exportBody } }
            },
            {
              extend: 'copy',
              text: '<i class="ti ti-copy me-2" ></i>Copy',
              className: 'dropdown-item',
              exportOptions: { columns: [1, 2, 3, 4, 5], format: { body: exportBody } }
            }
          ]
        },
        {
          text: '<i class="ti ti-plus me-0 me-sm-1 ti-xs"></i><span class="d-none d-sm-inline-block">Add New User</span>',
          className: 'add-new btn btn-primary waves-effect waves-light',
          attr: {
            'data-bs-toggle': 'offcanvas',
            'data-bs-target': '#offcanvasAddUser'
          }
        }
      ],
      responsive: {
        details: {
          display: $.fn.dataTable.Responsive.display.modal({
            header: function (row) {
              return 'Details of ' + row.data().full_name;
            }
          }),
          type: 'column',
          renderer: function (api, rowIdx, columns) {
            var data = $.map(columns, function (col) {
              return col.title !== ''
                ? '<tr data-dt-row="' +
                    col.rowIndex +
                    '" data-dt-column="' +
                    col.columnIndex +
                    '">' +
                    '<td>' +
                    col.title +
                    ':' +
                    '</td> ' +
                    '<td>' +
                    col.data +
                    '</td>' +
                    '</tr>'
                : '';
            }).join('');
            return data ? $('<table class="table"/><tbody />').append(data) : false;
          }
        }
      },
      initComplete: function () {
        this.api()
          .columns(2)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="UserRole" class="form-select text-capitalize"><option value=""> Select Role </option></select>'
            )
              .appendTo('.user_role')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });
            column
              .data()
              .unique()
              .sort()
              .each(function (d) {
                if (d) select.append('<option value="' + d + '">' + prettyRole(d) + '</option>');
              });
          });
        this.api()
          .columns(3)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="UserPlan" class="form-select text-capitalize"><option value=""> Select Site </option></select>'
            )
              .appendTo('.user_plan')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? val : '', true, false).draw();
              });
            column
              .data()
              .unique()
              .sort()
              .each(function (d) {
                if (d) select.append('<option value="' + d + '">' + d + '</option>');
              });
          });
        this.api()
          .columns(5)
          .every(function () {
            var column = this;
            var select = $(
              '<select id="FilterTransaction" class="form-select text-capitalize"><option value=""> Select Status </option></select>'
            )
              .appendTo('.user_status')
              .on('change', function () {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                column.search(val ? '^' + val + '$' : '', true, false).draw();
              });
            column
              .data()
              .unique()
              .sort()
              .each(function (d) {
                select.append(
                  '<option value="' + statusObj[d].title + '" class="text-capitalize">' + statusObj[d].title + '</option>'
                );
              });
          });
      }
    });
  }

  function confirmAction(title, text, confirmButtonText, thenFn) {
    Swal.fire({
      title: title,
      text: text,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: confirmButtonText,
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (result.value) thenFn();
    });
  }

  function toastError(xhr) {
    var msg = (xhr.responseJSON && (xhr.responseJSON.message || Object.values(xhr.responseJSON.errors || {})[0])) || 'Request failed.';
    if (Array.isArray(msg)) msg = msg[0];
    Swal.fire({
      title: 'Error',
      text: msg,
      icon: 'error',
      customClass: { confirmButton: 'btn btn-success' }
    });
  }

  $(document).on('click', '.delete-record', function () {
    var userId = $(this).data('id');
    var dtrModal = $('.dtr-bs-modal.show');
    if (dtrModal.length) dtrModal.modal('hide');
    confirmAction('Are you sure?', "You won't be able to revert this!", 'Yes, delete it!', function () {
      $.ajax({
        type: 'DELETE',
        url: window.apmsUsers.update + '/' + userId,
        success: function () {
          if (dt_user) dt_user.ajax.reload(null, false);
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'The user has been deleted!',
            customClass: { confirmButton: 'btn btn-success' }
          });
        },
        error: toastError
      });
    });
  });

  $(document).on('click', '.suspend-record', function () {
    var userId = $(this).data('id');
    confirmAction('Suspend user?', 'This toggles the user active status.', 'Yes, continue', function () {
      $.ajax({
        type: 'POST',
        url: window.apmsUsers.toggle + '/' + userId + '/toggle',
        success: function (status) {
          if (dt_user) dt_user.ajax.reload(null, false);
          Swal.fire({
            icon: 'success',
            title: status,
            customClass: { confirmButton: 'btn btn-success' }
          });
        },
        error: toastError
      });
    });
  });

  $(document).on('click', '.edit-record', function () {
    var userId = $(this).data('id');
    var dtrModal = $('.dtr-bs-modal.show');
    if (dtrModal.length) dtrModal.modal('hide');
    $('#offcanvasAddUserLabel').html('Edit User');
    $.get(window.apmsUsers.update + '/' + userId + '/edit', function (data) {
      $('#user_id').val(data.id);
      $('#add-user-fullname').val(data.name);
      $('#add-user-username').val(data.username);
      $('#add-user-email').val(data.email);
      $('#add-user-password').val('');
      $('#user-status').val(data.is_active ? '1' : '0');
      $('#user-role').val(data.roles).trigger('change');
      $('#user-sites').val(data.sites).trigger('change');
    });
  });

  $('.add-new').on('click', function () {
    $('#user_id').val('');
    $('#addNewUserForm')[0].reset();
    $('#user-role').val(null).trigger('change');
    $('#user-sites').val(null).trigger('change');
    $('#offcanvasAddUserLabel').html('Add User');
  });

  setTimeout(function () {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);

  const addNewUserForm = document.getElementById('addNewUserForm');
  if (!addNewUserForm) return;

  const fv = FormValidation.formValidation(addNewUserForm, {
    fields: {
      name: {
        validators: { notEmpty: { message: 'Please enter fullname ' } }
      },
      username: {
        validators: { notEmpty: { message: 'Please enter username' } }
      },
      password: {
        validators: {
          callback: {
            message: 'Please enter password',
            callback: function (input) {
              return $('#user_id').val() ? true : input.value !== '';
            }
          }
        }
      }
    },
    plugins: {
      trigger: new FormValidation.plugins.Trigger(),
      bootstrap5: new FormValidation.plugins.Bootstrap5({
        eleValidClass: '',
        rowSelector: function () {
          return '.mb-3, .mb-4';
        }
      }),
      submitButton: new FormValidation.plugins.SubmitButton(),
      autoFocus: new FormValidation.plugins.AutoFocus()
    }
  }).on('core.form.valid', function () {
    var userId = $('#user_id').val();
    var payload = $('#addNewUserForm').serialize();
    if (userId) payload += '&_method=PUT';
    $.ajax({
      data: payload,
      url: userId ? window.apmsUsers.update + '/' + userId : window.apmsUsers.store,
      type: 'POST',
      success: function (status) {
        if (dt_user) dt_user.ajax.reload(null, false);
        offCanvasForm.offcanvas('hide');
        if (!dt_user) location.reload();
        Swal.fire({
          icon: 'success',
          title: 'Successfully ' + status + '!',
          text: 'User ' + status + ' Successfully.',
          customClass: { confirmButton: 'btn btn-success' }
        });
      },
      error: toastError
    });
  });

  offCanvasForm.on('hidden.bs.offcanvas', function () {
    fv.resetForm(true);
    $('#user_id').val('');
    $('#user-role').val(null).trigger('change');
    $('#user-sites').val(null).trigger('change');
  });
});
