/**
 * App user list (roles page)
 */

'use strict';

$(function () {
  var dtUserTable = $('.datatables-users'),
    statusObj = {
      Active: { title: 'Active', class: 'bg-label-success' },
      Inactive: { title: 'Inactive', class: 'bg-label-secondary' }
    };

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

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
      ' me-3 w-px-30 h-px-30"><i class="ti ' +
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

  if (dtUserTable.length && window.apmsUsers) {
    dtUserTable.DataTable({
      ajax: { url: window.apmsUsers.data, dataSrc: 'data' },
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
          orderable: false,
          searchable: false,
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
            var output =
              '<span class="avatar-initial rounded-circle bg-label-' +
              avatarState(name) +
              '">' +
              escapeHtml(initials(name)) +
              '</span>';
            return (
              '<div class="d-flex justify-content-left align-items-center">' +
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
              '<small class="text-muted">@' +
              escapeHtml(full.username) +
              '</small></div></div>'
            );
          }
        },
        {
          targets: 2,
          render: function (data, type, full) {
            if (!full.role) return '<span class="text-muted">-</span>';
            return (
              "<span class='text-truncate d-flex align-items-center'>" +
              roleBadge(full.role) +
              escapeHtml(prettyRole(full.role)) +
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
            return (
              '<span class="badge ' +
              statusObj[full.status].class +
              '" text-capitalized>' +
              statusObj[full.status].title +
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
              '<a href="' +
              window.apmsUsers.show +
              '/' +
              full.id +
              '" class="btn btn-sm btn-icon"><i class="ti ti-eye"></i></a>' +
              '</div>'
            );
          }
        }
      ],
      order: [[1, 'desc']],
      dom:
        '<"row mx-2"' +
        '<"col-sm-12 col-md-4 col-lg-6" l>' +
        '<"col-sm-12 col-md-8 col-lg-6"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center align-items-center flex-sm-nowrap flex-wrap me-1"<"me-3"f><"user_role w-px-200 pb-3 pb-sm-0">>>' +
        '>t' +
        '<"row mx-2"' +
        '<"col-sm-12 col-md-6"i>' +
        '<"col-sm-12 col-md-6"p>' +
        '>',
      language: {
        sLengthMenu: 'Show _MENU_',
        search: 'Search',
        searchPlaceholder: 'Search..'
      },
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
                if (d) select.append('<option value="' + d + '" class="text-capitalize">' + prettyRole(d) + '</option>');
              });
          });
      }
    });
  }

  setTimeout(function () {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);

  $(document).on('click', '.delete-role', function () {
    var id = $(this).data('id');
    Swal.fire({
      title: 'Are you sure?',
      text: "You won't be able to revert this!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'Yes, delete it!',
      customClass: {
        confirmButton: 'btn btn-primary me-3',
        cancelButton: 'btn btn-label-secondary'
      },
      buttonsStyling: false
    }).then(function (result) {
      if (!result.value) return;
      $.ajax({
        type: 'DELETE',
        url: window.apmsRoles.update + '/' + id,
        success: function () {
          location.reload();
        },
        error: function (xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Request failed.';
          Swal.fire({ icon: 'error', title: 'Error', text: msg, customClass: { confirmButton: 'btn btn-success' } });
        }
      });
    });
  });
});

(function () {
  var roleEditList = document.querySelectorAll('.role-edit-modal'),
    roleAdd = document.querySelector('.add-new-role'),
    roleTitle = document.querySelector('.role-title');

  function resetRoleForm() {
    var form = document.getElementById('addRoleForm');
    if (form) form.reset();
    document.getElementById('role_id').value = '';
    document.querySelectorAll('.role-permission').forEach(function (el) {
      el.checked = false;
    });
    document.querySelectorAll('.role-group-select').forEach(function (el) {
      el.checked = false;
    });
    var selectAll = document.getElementById('selectAll');
    if (selectAll) selectAll.checked = false;
  }

  if (roleAdd) {
    roleAdd.onclick = function () {
      resetRoleForm();
      roleTitle.innerHTML = 'Add New Role';
    };
  }
  if (roleEditList) {
    roleEditList.forEach(function (roleEditEl) {
      roleEditEl.onclick = function () {
        roleTitle.innerHTML = 'Edit Role';
        var id = roleEditEl.getAttribute('data-id');
        $.get(window.apmsRoles.show + '/' + id, function (data) {
          $('#role_id').val(data.id);
          $('#modalRoleName').val(data.name);
          document.querySelectorAll('.role-permission').forEach(function (el) {
            el.checked = data.permissions.indexOf(el.value) !== -1;
          });
          if (window.syncRolePermissionSelects) window.syncRolePermissionSelects();
        });
      };
    });
  }
})();
