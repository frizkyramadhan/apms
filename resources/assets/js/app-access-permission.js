/**
 * App permissions list
 */

'use strict';

$(function () {
  var dataTablePermissions = $('.datatables-permissions'),
    dt_permission,
    userList = window.apmsPermissions.users,
    roleColors = ['primary', 'warning', 'success', 'info', 'danger', 'secondary'];

  $.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
  });

  function escapeHtml(text) {
    return $('<div>').text(text == null ? '' : String(text)).html();
  }

  function prettyRole(role) {
    return String(role || '').replace(/_/g, ' ').replace(/\b\w/g, function (c) {
      return c.toUpperCase();
    });
  }

  if (dataTablePermissions.length) {
    dt_permission = dataTablePermissions.DataTable({
      ajax: { url: window.apmsPermissions.data, dataSrc: 'data' },
      autoWidth: false,
      columns: [
        { data: 'id' },
        { data: 'name' },
        { data: 'assigned_to' },
        { data: 'created_date' },
        { data: 'id' }
      ],
      columnDefs: [
        {
          targets: 0,
          searchable: false,
          visible: false
        },
        {
          targets: 1,
          width: '18%',
          render: function (data, type, full) {
            return '<span class="text-nowrap">' + escapeHtml(full.name) + '</span>';
          }
        },
        {
          targets: 2,
          orderable: false,
          width: '52%',
          className: 'assigned-to',
          render: function (data, type, full) {
            var assignedTo = full.assigned_to || [];
            if (!assignedTo.length) return '<span class="text-muted">-</span>';
            var output = '';
            assignedTo.forEach(function (role, i) {
              output +=
                '<a href="' +
                userList +
                '"><span class="badge bg-label-' +
                roleColors[i % roleColors.length] +
                ' me-1 mb-1">' +
                escapeHtml(prettyRole(role)) +
                '</span></a>';
            });
            return '<div class="d-flex flex-wrap align-items-center">' + output + '</div>';
          }
        },
        {
          targets: 3,
          orderable: false,
          width: '18%',
          render: function (data, type, full) {
            return '<span class="text-nowrap">' + escapeHtml(full.created_date) + '</span>';
          }
        },
        {
          targets: -1,
          searchable: false,
          title: 'Actions',
          orderable: false,
          width: '12%',
          render: function (data, type, full) {
            return (
              '<span class="text-nowrap"><button class="btn btn-sm btn-icon me-2 edit-record" data-id="' +
              full.id +
              '" data-name="' +
              escapeHtml(full.name) +
              '" data-roles="' +
              escapeHtml((full.assigned_to || []).join(',')) +
              '" data-bs-target="#editPermissionModal" data-bs-toggle="modal"><i class="ti ti-edit"></i></button>' +
              '<button class="btn btn-sm btn-icon delete-record" data-id="' +
              full.id +
              '"><i class="ti ti-trash"></i></button></span>'
            );
          }
        }
      ],
      order: [[1, 'asc']],
      dom:
        '<"row mx-1"' +
        '<"col-sm-12 col-md-3" l>' +
        '<"col-sm-12 col-md-9"<"dt-action-buttons text-xl-end text-lg-start text-md-end text-start d-flex align-items-center justify-content-md-end justify-content-center flex-wrap me-1"<"me-3"f>B>>' +
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
      buttons: [
        {
          text: 'Add Permission',
          className: 'add-new btn btn-primary mb-3 mb-md-0 waves-effect waves-light',
          attr: {
            'data-bs-toggle': 'modal',
            'data-bs-target': '#addPermissionModal'
          },
          init: function (api, node) {
            $(node).removeClass('btn-secondary');
          }
        }
      ]
    });
  }

  window.apmsPermissionTable = dt_permission;

  $(document).on('click', '.edit-record', function () {
    var roles = String($(this).attr('data-roles') || '')
      .split(',')
      .filter(Boolean);
    $('#editPermissionId').val($(this).data('id'));
    $('#editPermissionName').val($(this).data('name'));
    $('.edit-permission-role').each(function () {
      this.checked = roles.indexOf(this.value) !== -1;
    });
    var boxes = document.querySelectorAll('.edit-permission-role');
    var selectAll = document.getElementById('editSelectAllRoles');
    if (selectAll) {
      selectAll.checked = boxes.length > 0 && Array.prototype.every.call(boxes, function (el) {
        return el.checked;
      });
    }
  });

  $(document).on('click', '.delete-record', function () {
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
        url: window.apmsPermissions.update + '/' + id,
        success: function () {
          dt_permission.ajax.reload(null, false);
          Swal.fire({
            icon: 'success',
            title: 'Deleted!',
            text: 'The permission has been deleted!',
            customClass: { confirmButton: 'btn btn-success' }
          });
        },
        error: function (xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Request failed.';
          Swal.fire({ icon: 'error', title: 'Error', text: msg, customClass: { confirmButton: 'btn btn-success' } });
        }
      });
    });
  });

  setTimeout(function () {
    $('.dataTables_filter .form-control').removeClass('form-control-sm');
    $('.dataTables_length .form-select').removeClass('form-select-sm');
  }, 300);
});
