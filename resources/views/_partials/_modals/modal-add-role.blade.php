<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="role-title mb-2">Add New Role</h3>
          <p class="text-muted">Set role permissions</p>
        </div>
        <!-- Add role form -->
        <form id="addRoleForm" class="row g-3" onsubmit="return false">
          <input type="hidden" id="role_id" name="id" />
          <div class="col-12 mb-4">
            <label class="form-label" for="modalRoleName">Role Name</label>
            <input type="text" id="modalRoleName" name="name" class="form-control" placeholder="Enter a role name" tabindex="-1" />
            <div class="form-text">Lowercase letters, numbers, and underscores only (example: plant_foreman).</div>
          </div>
          <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="mb-0">Role Permissions</h5>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="selectAll" />
                <label class="form-check-label" for="selectAll">Select All</label>
              </div>
            </div>
            @foreach ($permissionGroups as $group => $perms)
              <div class="d-flex align-items-center mb-2 {{ $loop->first ? '' : 'mt-3' }}">
                <input class="form-check-input role-group-select me-2" type="checkbox" id="selectGroup{{ $group }}" data-group="{{ $group }}" aria-label="Select all {{ $group }}" />
                <label class="fw-medium text-uppercase text-muted mb-0" for="selectGroup{{ $group }}">{{ str_replace('_', ' ', $group) }}</label>
              </div>
              <div class="row">
                @foreach ($perms as $perm)
                <div class="col-4">
                  <div class="form-check mb-2">
                    <input class="form-check-input role-permission" type="checkbox" id="perm{{ $perm->id }}" name="permissions[]" value="{{ $perm->name }}" data-group="{{ $group }}" />
                    <label class="form-check-label" for="perm{{ $perm->id }}">
                      {{ \Illuminate\Support\Str::after($perm->name, '.') ?: $perm->name }}
                    </label>
                  </div>
                </div>
                @endforeach
              </div>
            @endforeach
          </div>
          <div class="col-12 text-center mt-4">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Submit</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
          </div>
        </form>
        <!--/ Add role form -->
      </div>
    </div>
  </div>
</div>
<!--/ Add Role Modal -->
