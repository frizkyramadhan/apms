@php
  $displayName = $user->name ?: $user->username;
  $parts = preg_split('/\s+/', trim($displayName)) ?: [];
  $initials = strtoupper(substr($parts[0] ?? '?', 0, 1) . substr($parts[count($parts) - 1] ?? '', 0, 1));
  $primaryRole = $user->roles->pluck('name')->first();
@endphp

@extends('layouts/layoutMaster')

@section('title', 'User View')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/animate-css/animate.css')}}" />
@endsection

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/page-user-view.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
@endsection

@section('page-script')
<script>
  window.apmsUsers = {
    data: @json(route('users.data')),
    store: @json(route('users.store')),
    update: @json(url('users')),
    show: @json(url('users')),
    toggle: @json(url('users'))
  };
</script>
<script src="{{asset('assets/js/app-user-list.js')}}"></script>
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light"><a href="{{ route('users.index') }}" class="text-muted">User</a> / View /</span> Account
</h4>
<div class="row">
  <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
    <div class="card mb-4">
      <div class="card-body">
        <div class="user-avatar-section">
          <div class="d-flex align-items-center flex-column">
            <span class="avatar avatar-xl mb-3 pt-1 mt-4">
              <span class="avatar-initial rounded bg-label-primary">{{ $initials }}</span>
            </span>
            <div class="user-info text-center">
              <h4 class="mb-2">{{ $displayName }}</h4>
              @if ($primaryRole)
                <span class="badge bg-label-secondary mt-1">{{ str_replace('_', ' ', $primaryRole) }}</span>
              @endif
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-around flex-wrap mt-3 pt-3 pb-4 border-bottom">
          <div class="d-flex align-items-start me-4 mt-3 gap-2">
            <span class="badge bg-label-primary p-2 rounded"><i class="ti ti-key ti-sm"></i></span>
            <div>
              <p class="mb-0 fw-medium">{{ $permissions->count() }}</p>
              <small>Permissions</small>
            </div>
          </div>
          <div class="d-flex align-items-start mt-3 gap-2">
            <span class="badge bg-label-primary p-2 rounded"><i class="ti ti-map-pin ti-sm"></i></span>
            <div>
              <p class="mb-0 fw-medium">{{ $user->projects->count() }}</p>
              <small>Sites</small>
            </div>
          </div>
        </div>
        <p class="mt-4 small text-uppercase text-muted">Details</p>
        <div class="info-container">
          <ul class="list-unstyled">
            <li class="mb-2">
              <span class="fw-medium me-1">Username:</span>
              <span>{{ $user->username }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-medium me-1">Email:</span>
              <span>{{ $user->email ?: '-' }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-medium me-1">Status:</span>
              <span class="badge {{ $user->is_active ? 'bg-label-success' : 'bg-label-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-medium me-1">Role:</span>
              <span>{{ $user->roles->pluck('name')->map(fn ($n) => str_replace('_', ' ', $n))->implode(', ') ?: '-' }}</span>
            </li>
            <li class="mb-2 pt-1">
              <span class="fw-medium me-1">Sites:</span>
              <span>{{ $user->projects->pluck('project_code')->implode(', ') ?: '-' }}</span>
            </li>
            <li class="pt-1">
              <span class="fw-medium me-1">Last login:</span>
              <span>{{ $user->last_login?->format('d M Y H:i') ?: '-' }}</span>
            </li>
          </ul>
          <div class="d-flex justify-content-center">
            <a href="javascript:;" class="btn btn-primary me-3 edit-record" data-id="{{ $user->id }}" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">Edit</a>
            <a href="javascript:;" class="btn btn-label-danger suspend-record" data-id="{{ $user->id }}">Suspend</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
    <ul class="nav nav-pills flex-column flex-md-row mb-4">
      <li class="nav-item"><a class="nav-link active" href="javascript:void(0);"><i class="ti ti-user-check ti-xs me-1"></i>Account</a></li>
    </ul>

    <div class="card mb-4">
      <h5 class="card-header">Assigned Sites</h5>
      <div class="table-responsive mb-3">
        <table class="table border-top">
          <thead>
            <tr>
              <th>Project code</th>
              <th>Access</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($user->projects as $project)
              <tr>
                <td>{{ $project->project_code }}</td>
                <td>{{ $project->project_code === '000H' ? 'All sites' : 'Site' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="text-muted">No sites assigned</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="card mb-4">
      <h5 class="card-header">Effective Permissions</h5>
      <div class="card-body">
        @forelse ($permissions->groupBy(fn ($p) => explode('.', $p->name)[0]) as $group => $perms)
          <p class="fw-medium text-uppercase mb-2">{{ $group }}</p>
          <div class="mb-3">
            @foreach ($perms as $perm)
              <span class="badge bg-label-primary me-1 mb-1">{{ $perm->name }}</span>
            @endforeach
          </div>
        @empty
          <p class="text-muted mb-0">No permissions</p>
        @endforelse
      </div>
    </div>
  </div>
</div>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
  <div class="offcanvas-header">
    <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Edit User</h5>
    <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body mx-0 flex-grow-0 pt-0 h-100">
    <form class="add-new-user pt-0" id="addNewUserForm" onsubmit="return false">
      <input type="hidden" id="user_id" name="id" />
      <div class="mb-3">
        <label class="form-label" for="add-user-fullname">Full Name</label>
        <input type="text" class="form-control" id="add-user-fullname" name="name" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="add-user-username">Username</label>
        <input type="text" class="form-control" id="add-user-username" name="username" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="add-user-email">Email</label>
        <input type="text" id="add-user-email" class="form-control" name="email" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="add-user-password">Password</label>
        <input type="password" id="add-user-password" class="form-control" name="password" autocomplete="new-password" placeholder="Leave blank to keep current" />
      </div>
      <div class="mb-3">
        <label class="form-label" for="user-role">User Role</label>
        <select id="user-role" name="roles[]" class="select2 form-select" multiple>
          @foreach ($roles as $role)
            <option value="{{ $role }}">{{ str_replace('_', ' ', $role) }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label" for="user-sites">Sites</label>
        <select id="user-sites" name="sites[]" class="select2 form-select" multiple>
          @foreach ($sites as $site)
            <option value="{{ $site }}">{{ $site }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-4">
        <label class="form-label" for="user-status">Status</label>
        <select id="user-status" name="is_active" class="form-select">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Submit</button>
      <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancel</button>
    </form>
  </div>
</div>
@endsection
