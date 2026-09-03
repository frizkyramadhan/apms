@extends('layouts/layoutMaster')

@section('title', $pageTitle ?? 'DMBD Dashboard')

@section('content')
<h4 class="mb-3">{{ $pageTitle ?? 'Dashboard DMBD' }}</h4>

<form class="row g-3 mb-4" method="get">
  <div class="col-md-3">
    <label class="form-label">Site</label>
    <select name="site" class="form-select" onchange="this.form.submit()">
      <option value="">Semua site</option>
      @foreach($sites as $code)
        <option value="{{ $code }}" @selected($site === $code)>{{ $code }}</option>
      @endforeach
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Status</label>
    <select name="status" class="form-select" onchange="this.form.submit()">
      <option value="">Semua</option>
      <option value="ready" @selected($status === 'ready')>RFU</option>
      <option value="breakdown" @selected($status === 'breakdown')>Breakdown</option>
      <option value="standby" @selected($status === 'standby')>Stand by</option>
    </select>
  </div>
</form>

<div class="row mb-4">
  <div class="col-md-2"><div class="card"><div class="card-body"><small>Total Unit</small><h4 class="mb-0">{{ $counts['total'] }}</h4></div></div></div>
  <div class="col-md-2"><div class="card"><div class="card-body"><small>RFU</small><h4 class="mb-0 text-success">{{ $counts['ready'] }}</h4></div></div></div>
  <div class="col-md-2"><div class="card"><div class="card-body"><small>Breakdown</small><h4 class="mb-0 text-danger">{{ $counts['breakdown'] }}</h4></div></div></div>
  <div class="col-md-2"><div class="card"><div class="card-body"><small>Stand by</small><h4 class="mb-0 text-warning">{{ $counts['standby'] }}</h4></div></div></div>
  <div class="col-md-2"><div class="card"><div class="card-body"><small>MTTR (jam, {{ $month->format('M Y') }})</small><h4 class="mb-0">{{ $mttr ?? '—' }}</h4></div></div></div>
  <div class="col-md-2"><div class="card"><div class="card-body"><small>MTBF</small><h4 class="mb-0">{{ $mtbf ?? '—' }}</h4></div></div></div>
</div>

<div class="card">
  <div class="table-responsive">
    <table class="table table-hover">
      <thead>
        <tr>
          <th>Site</th>
          <th>Unit</th>
          <th>Model</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Start BD</th>
          <th>BD Ages (jam)</th>
          <th>HM</th>
          <th>MR/PR/PO</th>
          <th>Problem</th>
        </tr>
      </thead>
      <tbody>
        @forelse($rows as $row)
          <tr>
            <td>{{ $row['unit']->project_code }}</td>
            <td>{{ $row['unit']->unit_no }}</td>
            <td>{{ $row['unit']->model_name }}</td>
            <td>{{ $row['status'] === 'ready' ? 'RFU' : ($row['status'] === 'standby' ? 'Stand by' : 'Breakdown') }}</td>
            <td>{{ $row['priority'] }}</td>
            <td>{{ $row['started_at']?->format('Y-m-d H:i') }}</td>
            <td>
              @if($row['bd_ages'] !== null)
                <span title="{{ $row['bd_ages_tip'] }}">{{ $row['bd_ages'] }}</span>
              @endif
            </td>
            <td>{{ $row['hm'] }}</td>
            <td>{{ $row['mr_pr_po'] }}</td>
            <td>{{ $row['problem'] }}</td>
          </tr>
        @empty
          <tr><td colspan="10" class="text-center text-muted">Belum ada unit. Jalankan <code>php artisan pcr:sync-fleet</code>.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
