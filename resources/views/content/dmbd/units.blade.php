@extends('layouts/layoutMaster')

@section('title', 'Master Unit')

@section('content')
<h4 class="mb-3">Master Unit</h4>
<form class="row g-3 mb-3" method="get">
  <div class="col-md-4">
    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Unit / model">
  </div>
  <div class="col-md-2">
    <button class="btn btn-primary">Cari</button>
  </div>
</form>
<div class="card">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Site</th>
          <th>Unit</th>
          <th>Model</th>
          <th>Manufacture</th>
          <th>SN</th>
          <th>Engine</th>
          <th>Fleet status</th>
          <th>Operasional</th>
        </tr>
      </thead>
      <tbody>
        @foreach($units as $unit)
          <tr>
            <td>{{ $unit->project_code }}</td>
            <td>{{ $unit->unit_no }}</td>
            <td>{{ $unit->model_name }}</td>
            <td>{{ $unit->manufacture }}</td>
            <td>{{ $unit->serial_number }}</td>
            <td>{{ $unit->engine_number }}</td>
            <td>{{ $unit->unit_status }}</td>
            <td>{{ $unit->operationalStatus() === 'ready' ? 'RFU' : $unit->operationalStatus() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <div class="card-body">{{ $units->links() }}</div>
</div>
@endsection
