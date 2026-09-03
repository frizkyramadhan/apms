@extends('layouts/layoutMaster')

@section('title', 'Master Unit')

@section('vendor-style')
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}">
@endsection

@section('vendor-script')
<script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
<script>
  window.apmsUnits = { data: @json(route('dmbd-units.data')) };
</script>
<script src="{{ asset('assets/js/dmbd-units.js') }}"></script>
@endsection

@section('content')
<div class="mb-4">
  <h4 class="mb-1">Master Unit</h4>
  <p class="text-muted mb-0">Read-only unit list from local cache (fleet_equipment_cache). Use Sync to refresh from ARK Fleet.</p>
</div>

@if (session('success'))
  <div class="alert alert-success">{{ session('success') }}</div>
@endif
@if (session('error'))
  <div class="alert alert-danger">{{ session('error') }}</div>
@endif

<div class="card">
  <div class="card-body">
    <div class="row g-3">
      <div class="col-sm-6 col-md-2">
        <label class="form-label" for="filter-unit-no">Unit No</label>
        <input type="text" id="filter-unit-no" class="form-control" placeholder="e.g. ADT 011" autocomplete="off">
      </div>
      <div class="col-sm-6 col-md-2">
        <label class="form-label" for="filter-model">Model</label>
        <input type="text" id="filter-model" class="form-control" placeholder="e.g. HM400-3R" autocomplete="off">
      </div>
      <div class="col-sm-6 col-md-2">
        <label class="form-label" for="filter-project">Project</label>
        <input type="text" id="filter-project" class="form-control" placeholder="e.g. 022C" autocomplete="off">
      </div>
      <div class="col-sm-6 col-md-2">
        <label class="form-label" for="filter-manufacture">Manufacture</label>
        <input type="text" id="filter-manufacture" class="form-control" placeholder="e.g. Komatsu" autocomplete="off">
      </div>
      <div class="col-sm-6 col-md-2">
        <label class="form-label" for="filter-plant-group">Plant group</label>
        <input type="text" id="filter-plant-group" class="form-control" placeholder="e.g. Compressor" autocomplete="off">
      </div>
      <div class="col-sm-6 col-md-2">
        <label class="form-label" for="filter-status">Status</label>
        <select id="filter-status" class="form-select">
          <option value="">All</option>
          <option value="ACTIVE" selected>Active</option>
          <option value="IN-ACTIVE">In-active</option>
          <option value="SCRAP">Scrap</option>
          <option value="SOLD">Sold</option>
        </select>
      </div>
    </div>
  </div>
  <hr class="my-0">
  <div class="card-body py-3">
    @can('dmbd.master')
      <form method="post" action="{{ route('fleet.sync') }}" class="d-inline" onsubmit="this.querySelector('button').disabled = true">
        @csrf
        <button type="submit" class="btn btn-label-secondary">
          <i class="ti ti-refresh me-1"></i>Sync from ARK Fleet
        </button>
      </form>
    @endcan
  </div>
  <div class="card-datatable table-responsive">
    <table class="datatables-units table">
      <thead>
        <tr>
          <th>Unit No</th>
          <th>Description</th>
          <th>Project</th>
          <th>Model</th>
          <th>Manufacture</th>
          <th>Plant group</th>
          <th>Plant type</th>
          <th>Status</th>
        </tr>
      </thead>
    </table>
  </div>
</div>
@endsection
