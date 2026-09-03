@extends('layouts/layoutMaster')

@section('title', 'History Breakdown')

@section('content')
<h4 class="mb-3">History Breakdown</h4>
<div class="card">
  <div class="table-responsive">
    <table class="table">
      <thead>
        <tr>
          <th>Site</th>
          <th>Unit</th>
          <th>Status</th>
          <th>Priority</th>
          <th>Start</th>
          <th>End</th>
          <th>Duration (jam)</th>
          <th>Closed to</th>
          <th>Problem</th>
        </tr>
      </thead>
      <tbody>
        @forelse($events as $event)
          <tr>
            <td>{{ $event->unit?->project_code }}</td>
            <td>{{ $event->unit?->unit_no }}</td>
            <td>{{ $event->status }}</td>
            <td>{{ $event->priority }}</td>
            <td>{{ $event->started_at?->format('Y-m-d H:i') }}</td>
            <td>{{ $event->ended_at?->format('Y-m-d H:i') }}</td>
            <td>{{ $event->durationHours() }}</td>
            <td>{{ $event->closed_to }}</td>
            <td>{{ $event->problem }}</td>
          </tr>
        @empty
          <tr><td colspan="9" class="text-center text-muted">Belum ada history.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
