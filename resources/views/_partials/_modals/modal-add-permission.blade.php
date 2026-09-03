<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="mb-2">Add New Permission</h3>
          <p class="text-muted">Permissions you may use and assign to your users.</p>
        </div>
        <form id="addPermissionForm" class="row" onsubmit="return false">
          <div class="col-12 mb-3">
            <label class="form-label" for="modalPermissionName">Permission Name</label>
            <input type="text" id="modalPermissionName" name="name" class="form-control" placeholder="users.access" autofocus />
            <div class="form-text">Lowercase, dots allowed (example: dmbd.dashboard).</div>
          </div>
          <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">Assign to roles</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="addSelectAllRoles" />
                <label class="form-check-label" for="addSelectAllRoles">Select All</label>
              </div>
            </div>
            <div class="row">
              @foreach ($roles as $role)
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input add-permission-role" type="checkbox" id="addRole{{ $loop->index }}" name="roles[]" value="{{ $role }}" />
                  <label class="form-check-label" for="addRole{{ $loop->index }}">{{ str_replace('_', ' ', $role) }}</label>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="col-12 text-center demo-vertical-spacing">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Create Permission</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Discard</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Add Permission Modal -->
