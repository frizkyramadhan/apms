<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content p-3 p-md-5">
      <button type="button" class="btn-close btn-pinned" data-bs-dismiss="modal" aria-label="Close"></button>
      <div class="modal-body">
        <div class="text-center mb-4">
          <h3 class="mb-2">Edit Permission</h3>
          <p class="text-muted">Edit permission as per your requirements.</p>
        </div>
        <div class="alert alert-warning" role="alert">
          <h6 class="alert-heading mb-2">Warning</h6>
          <p class="mb-0">By editing the permission name, you might break the system permissions functionality. Please ensure you're absolutely certain before proceeding.</p>
        </div>
        <form id="editPermissionForm" class="row" onsubmit="return false">
          <input type="hidden" id="editPermissionId" name="id" />
          <div class="col-12 mb-3">
            <label class="form-label" for="editPermissionName">Permission Name</label>
            <input type="text" id="editPermissionName" name="name" class="form-control" placeholder="Permission Name" tabindex="-1" />
          </div>
          <div class="col-12 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="mb-0">Assign to roles</h6>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="editSelectAllRoles" />
                <label class="form-check-label" for="editSelectAllRoles">Select All</label>
              </div>
            </div>
            <div class="row">
              @foreach ($roles as $role)
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input edit-permission-role" type="checkbox" id="editRole{{ $loop->index }}" name="roles[]" value="{{ $role }}" />
                  <label class="form-check-label" for="editRole{{ $loop->index }}">{{ str_replace('_', ' ', $role) }}</label>
                </div>
              </div>
              @endforeach
            </div>
          </div>
          <div class="col-12 text-center">
            <button type="submit" class="btn btn-primary me-sm-3 me-1">Update</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal" aria-label="Close">Discard</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!--/ Edit Permission Modal -->
