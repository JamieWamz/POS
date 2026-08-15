<?php
$users = ControllerUsers::ctrShowUsers(null, null) ?: [];
$userPlaceholder = 'views/img/users/default/prfplaceholder.png';
?>
<div class="content-wrapper">
  <section class="content-header">
    <div>
      <span class="pos-section-label">Administration</span>
      <h1>Team access</h1>
      <p class="pos-page-description">Create staff accounts, add profile photos, assign roles and control register access.</p>
    </div>
    <ol class="breadcrumb"><li><a href="home"><i class="fa fa-dashboard" aria-hidden="true"></i> Home</a></li><li class="active">Team access</li></ol>
  </section>

  <section class="content">
    <div class="box">
      <div class="box-header">
        <div><span class="pos-eyebrow">People</span><h2 class="box-title">Staff directory</h2></div>
        <button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#addUser"><i class="fa fa-user-plus" aria-hidden="true"></i> Add team member</button>
      </div>
      <div class="box-body">
        <table class="table table-bordered table-hover table-striped dt-responsive tables" width="100%">
          <thead><tr><th>#</th><th>Team member</th><th>Username</th><th>Role</th><th>Status</th><th>Last login</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($users as $index => $user): ?>
              <?php $photo = trim((string) ($user['photo'] ?? '')) ?: $userPlaceholder; ?>
              <tr>
                <td><?php echo $index + 1; ?></td>
                <td>
                  <div class="pos-team-member">
                    <img src="<?php echo e($photo); ?>" alt="<?php echo e($user['name']); ?> profile photo">
                    <strong><?php echo e($user['name']); ?></strong>
                  </div>
                </td>
                <td><?php echo e($user['user']); ?></td>
                <td><?php echo e($user['profile']); ?></td>
                <td>
                  <button
                    class="btn <?php echo (int) $user['status'] === 1 ? 'btn-success' : 'btn-danger'; ?> btn-sm btnActivate"
                    type="button"
                    data-user-id="<?php echo (int) $user['id']; ?>"
                    data-user-status="<?php echo (int) $user['status'] === 1 ? 0 : 1; ?>"
                    <?php echo (int) $user['id'] === (int) $_SESSION['id'] ? 'disabled title="You cannot deactivate your own account"' : ''; ?>
                  ><?php echo (int) $user['status'] === 1 ? 'Active' : 'Inactive'; ?></button>
                </td>
                <td><?php echo !empty($user['lastLogin']) ? e($user['lastLogin']) : '<span class="text-muted">Never</span>'; ?></td>
                <td>
                  <div class="btn-group" role="group" aria-label="Actions for <?php echo e($user['name']); ?>">
                    <button class="btn btn-primary btnEditUser" type="button" data-user-id="<?php echo (int) $user['id']; ?>" data-bs-toggle="modal" data-bs-target="#editUser" aria-label="Edit <?php echo e($user['name']); ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                    <button class="btn btn-danger btnDeleteUser" type="button" data-user-id="<?php echo (int) $user['id']; ?>" <?php echo (int) $user['id'] === (int) $_SESSION['id'] ? 'disabled title="You cannot delete your own account"' : ''; ?> aria-label="Delete <?php echo e($user['name']); ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

<div id="addUser" class="modal fade" tabindex="-1" aria-labelledby="addUserTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="users" enctype="multipart/form-data">
        <div class="modal-header">
          <div><span class="pos-eyebrow">New account</span><h2 class="modal-title" id="addUserTitle">Add team member</h2></div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label" for="newName">Full name</label>
                  <input class="form-control" type="text" id="newName" name="newName" maxlength="100" autocomplete="name" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="newUser">Username</label>
                  <input class="form-control" type="text" id="newUser" name="newUser" minlength="2" maxlength="50" pattern="[A-Za-z0-9_.-]+" autocomplete="username" required>
                  <div id="newUserFeedback" class="form-text" aria-live="polite">Letters, numbers, dots, underscores and hyphens.</div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="newProfile">Role</label>
                  <select class="form-select" id="newProfile" name="newProfile" required>
                    <option value="">Select a role</option>
                    <option value="Administrator">Administrator</option>
                    <option value="Special">Inventory specialist</option>
                    <option value="Seller">Seller</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label" for="newPasswd">Temporary password</label>
                  <input class="form-control" type="password" id="newPasswd" name="newPasswd" minlength="12" maxlength="256" autocomplete="new-password" required>
                  <div class="form-text">Use at least 12 characters. Share it privately and have the team member replace it.</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="pos-profile-photo-field">
                <span class="form-label">Profile photo</span>
                <img id="newUserPhotoPreview" src="<?php echo e($userPlaceholder); ?>" alt="New user profile photo preview">
                <label class="btn btn-default w-100" for="newPhoto"><i class="fa fa-camera" aria-hidden="true"></i> Choose photo</label>
                <input class="visually-hidden pos-user-photo-input" type="file" id="newPhoto" name="newPhoto" accept="image/jpeg,image/png,image/webp" data-preview-target="newUserPhotoPreview">
                <p class="help-block">Optional. JPEG, PNG or WebP, up to 5 MB.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create account</button>
        </div>
      </form>
      <?php (new ControllerUsers())->ctrCreateUser(); ?>
    </div>
  </div>
</div>

<div id="editUser" class="modal fade" tabindex="-1" aria-labelledby="editUserTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="post" action="users" enctype="multipart/form-data">
        <div class="modal-header">
          <div><span class="pos-eyebrow">Account details</span><h2 class="modal-title" id="editUserTitle">Edit team member</h2></div>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-md-8">
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label" for="EditName">Full name</label>
                  <input class="form-control" type="text" id="EditName" name="EditName" maxlength="100" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="EditUser">Username</label>
                  <input class="form-control" type="text" id="EditUser" name="EditUser" readonly required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="EditProfile">Role</label>
                  <select class="form-select" id="EditProfile" name="EditProfile" required>
                    <option value="Administrator">Administrator</option>
                    <option value="Special">Inventory specialist</option>
                    <option value="Seller">Seller</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label" for="EditPasswd">New password</label>
                  <input class="form-control" type="password" id="EditPasswd" name="EditPasswd" minlength="12" maxlength="256" autocomplete="new-password">
                  <div class="form-text">Leave blank to keep the current password.</div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="pos-profile-photo-field">
                <span class="form-label">Profile photo</span>
                <img id="editUserPhotoPreview" src="<?php echo e($userPlaceholder); ?>" alt="Current user profile photo">
                <label class="btn btn-default w-100" for="editPhoto"><i class="fa fa-camera" aria-hidden="true"></i> Replace photo</label>
                <input class="visually-hidden pos-user-photo-input" type="file" id="editPhoto" name="editPhoto" accept="image/jpeg,image/png,image/webp" data-preview-target="editUserPhotoPreview">
                <div class="form-check mt-3">
                  <input class="form-check-input" type="checkbox" value="1" id="removePhoto" name="removePhoto">
                  <label class="form-check-label" for="removePhoto">Remove current photo</label>
                </div>
                <p class="help-block">JPEG, PNG or WebP, up to 5 MB.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
      <?php (new ControllerUsers())->ctrEditUser(); ?>
    </div>
  </div>
</div>

<?php (new ControllerUsers())->ctrDeleteUser(); ?>
