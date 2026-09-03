/**
 * Master Unit — DataTable + column filters (same fields as arka-pcr Units).
 */
'use strict';

$(function () {
  var table = $('.datatables-units');
  if (!table.length || !window.apmsUnits) return;

  var statusClass = {
    ACTIVE: 'bg-label-success',
    'IN-ACTIVE': 'bg-label-warning',
    INACTIVE: 'bg-label-warning',
    SCRAP: 'bg-label-secondary',
    SOLD: 'bg-label-info'
  };

  function escapeHtml(text) {
    return $('<div>')
      .text(text == null ? '' : String(text))
      .html();
  }

  var dt = table.DataTable({
    processing: true,
    serverSide: true,
    ajax: {
      url: window.apmsUnits.data,
      data: function (d) {
        d.unitNo = $('#filter-unit-no').val();
        d.model = $('#filter-model').val();
        d.project = $('#filter-project').val();
        d.manufacture = $('#filter-manufacture').val();
        d.plantGroup = $('#filter-plant-group').val();
        d.status = $('#filter-status').val();
      }
    },
    columns: [
      { data: 'unit_no' },
      { data: 'description' },
      { data: 'project_code' },
      { data: 'model_name' },
      { data: 'manufacture' },
      { data: 'plant_group' },
      { data: 'plant_type' },
      { data: 'unit_status' }
    ],
    columnDefs: [
      {
        targets: 0,
        render: function (data) {
          return '<span class="fw-medium">' + escapeHtml(data) + '</span>';
        }
      },
      {
        targets: 7,
        render: function (data) {
          var key = String(data || '').toUpperCase();
          var cls = statusClass[key] || 'bg-label-danger';
          return '<span class="badge rounded-pill ' + cls + '">' + escapeHtml(data || '—') + '</span>';
        }
      }
    ],
    order: [[0, 'asc']],
    pageLength: 10,
    lengthMenu: [10, 25, 50],
    searching: false,
    dom:
      '<"row mx-2"' +
      '<"col-sm-12 col-md-6"l>' +
      '<"col-sm-12 col-md-6"p>' +
      '>t' +
      '<"row mx-2"' +
      '<"col-sm-12 col-md-6"i>' +
      '<"col-sm-12 col-md-6"p>' +
      '>',
    language: {
      sLengthMenu: '_MENU_',
      processing: 'Loading…'
    }
  });

  var debounce;
  $('.datatables-units')
    .closest('.card')
    .find('#filter-unit-no, #filter-model, #filter-project, #filter-manufacture, #filter-plant-group')
    .on('keyup', function () {
      clearTimeout(debounce);
      debounce = setTimeout(function () {
        dt.ajax.reload();
      }, 300);
    });

  $('#filter-status').on('change', function () {
    dt.ajax.reload();
  });
});
