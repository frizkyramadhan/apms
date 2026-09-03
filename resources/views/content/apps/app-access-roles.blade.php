@php
$configData = Helper::appClasses();
$roleIcons = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Roles')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/sweetalert2/sweetalert2.js')}}"></script>
@endsection

@section('page-script')
<script>
  window.apmsUsers = {
    data: @json(route('users.data')),
    show: @json(url('users'))
  };
  window.apmsRoles = {
    store: @json(route('roles.store')),
    update: @json(url('roles')),
    show: @json(url('roles'))
  };
</script>
<script src="{{asset('assets/js/app-access-roles.js')}}"></script>
<script src="{{asset('assets/js/modal-add-role.js')}}"></script>
@endsection

@section('content')
<h4 class="mb-4">Roles List</h4>

<p class="mb-4">A role provided access to predefined menus and features so that depending on <br> assigned role an administrator can have access to what user needs.</p>
<!-- Role cards -->
<div class="row g-4">
  @foreach ($roles as $index => $role)
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <h6 class="fw-normal mb-2">Total {{ $role->users_count }} users</h6>
          <ul class="list-unstyled d-flex align-items-center avatar-group mb-0">
            @foreach ($role->users->take(5) as $member)
              @php
                $parts = preg_split('/\s+/', trim($member->name ?: $member->username)) ?: [];
                $initials = strtoupper(substr($parts[0] ?? '?', 0, 1) . substr($parts[count($parts) - 1] ?? '', 0, 1));
              @endphp
              <li data-bs-toggle="tooltip" data-popup="tooltip-custom" data-bs-placement="top" title="{{ $member->name ?: $member->username }}" class="avatar avatar-sm pull-up">
                <span class="avatar-initial rounded-circle bg-label-{{ $roleIcons[$loop->index % count($roleIcons)] }}">{{ $initials }}</span>
              </li>
            @endforeach
          </ul>
        </div>
        <div class="d-flex justify-content-between align-items-end mt-1">
          <div class="role-heading">
            <h4 class="mb-1">{{ str_replace('_', ' ', $role->name) }}</h4>
            <a href="javascript:;" data-bs-toggle="modal" data-bs-target="#addRoleModal" class="role-edit-modal" data-id="{{ $role->id }}"><span>Edit Role</span></a>
          </div>
          @if ($role->name !== 'administrator')
            <a href="javascript:void(0);" class="text-muted delete-role" data-id="{{ $role->id }}" title="Delete"><i class="ti ti-trash ti-md"></i></a>
          @else
            <span class="text-muted"><i class="ti ti-lock ti-md"></i></span>
          @endif
        </div>
      </div>
    </div>
  </div>
  @endforeach
  <div class="col-xl-4 col-lg-6 col-md-6">
    <div class="card h-100">
      <div class="row h-100">
        <div class="col-sm-5">
          <div class="d-flex align-items-end h-100 justify-content-center mt-sm-0 mt-3">
            <img src="{{ asset('assets/img/illustrations/add-new-roles.png') }}" class="img-fluid mt-sm-4 mt-md-0" alt="add-new-roles" width="83">
          </div>
        </div>
        <div class="col-sm-7">
          <div class="card-body text-sm-end text-center ps-sm-0">
            <button data-bs-target="#addRoleModal" data-bs-toggle="modal" class="btn btn-primary mb-2 text-nowrap add-new-role">Add New Role</button>
            <p class="mb-0 mt-1">Add role, if it does not exist</p>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-12">
    <!-- Role Table -->
    <div class="card">
      <div class="card-datatable table-responsive">
        <table class="datatables-users table border-top">
          <thead>
            <tr>
              <th></th>
              <th>User</th>
              <th>Role</th>
              <th>Sites</th>
              <th>Username</th>
              <th>Status</th>
              <th>Actions</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
    <!--/ Role Table -->
  </div>
</div>
<!--/ Role cards -->

<!-- Add Role Modal -->
@include('_partials/_modals/modal-add-role')
<!-- / Add Role Modal -->
@endsection
