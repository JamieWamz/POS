
<div class="content-wrapper">

  <section class="content-header">

    <div><span class="pos-section-label">Administration</span><h1>Team access</h1><p class="pos-page-description">Create staff accounts, assign roles and control register access.</p></div>

    <ol class="breadcrumb">

      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Team access</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUser">

         <i class="fa fa-plus"></i> Add User

        </button>

      </div>

      <div class="box-body">

        <table class="table table-bordered table-hover table-striped dt-responsive tables" width="100%">

          <thead>

           <tr>

             <th style="width:10px">#</th>
             <th>Name</th>
             <th>Username</th>
             <th>Photo</th>
             <th>Profile</th>
             <th>Status</th>
             <th>Last Login</th>
             <th>Actions</th>

           </tr>

          </thead>

          <tbody>

            <?php

              $item = null;
              $value = null;

              $users = ControllerUsers::ctrShowUsers($item, $value);

              // var_dump($users);

              foreach ($users as $key => $value) {

                echo '

                  <tr>
                    <td>'.($key+1).'</td>
                    <td>'.e($value["name"]).'</td>
                    <td>'.e($value["user"]).'</td>';

                    if ($value["photo"] != ""){

                      echo '<td><img src="'.e($value["photo"]).'" class="img-thumbnail" width="40" height="40" alt=""></td>';

                    }else{

                      echo '<td><img src="views/img/users/default/prfplaceholder.png" class="img-thumbnail" width="40" alt=""></td>';

                    }

                    echo '<td>'.e($value["profile"]).'</td>';

                    if($value["status"] != 0){

                      echo '<td><button class="btn btn-success btnActivate btn-xs" userId="'.(int) $value["id"].'" userStatus="0">Activated</button></td>';

                    }else{

                      echo '<td><button class="btn btn-danger btnActivate btn-xs" userId="'.(int) $value["id"].'" userStatus="1">Deactivated</button></td>';
                    }

                    echo '<td>'.e($value["lastLogin"]).'</td>

                    <td>

                      <div class="btn-group">

                        <button class="btn btn-primary btnEditUser" idUser="'.$value["id"].'" data-bs-toggle="modal" data-bs-target="#editUser"><i class="fa fa-pencil"></i></button>

                        <button class="btn btn-danger btnDeleteUser" userId="'.(int)$value["id"].'"><i class="fa fa-trash"></i></button>

                      </div>

                    </td>

                  </tr>';
              }

            ?>

          </tbody>

        </table>

      </div>

    </div>

  </section>

</div>

<!--=====================================
=            module add user            =
======================================-->

<!-- Modal -->
<div id="addUser" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <form role="form" method="POST" enctype="multipart/form-data">

        <!--=====================================
        HEADER
        ======================================-->

        <div class="modal-header" style="background: #DD4B39; color: #fff">

          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>

          <h4 class="modal-title">Add User</h4>

        </div>

        <!--=====================================
        BODY
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!--Input name -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <input class="form-control input-lg" type="text" name="newName" placeholder="Enter fullname" required>

              </div>

            </div>

            <!-- input username -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-key"></i></span>

                <input class="form-control input-lg" type="text" id="newUser" name="newUser" placeholder="Enter username" required>

              </div>

            </div>

            <!-- input password -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-lock"></i></span>

                <input class="form-control input-lg" type="password" name="newPasswd" placeholder="At least 12 characters" minlength="12" maxlength="256" autocomplete="new-password" required>

              </div>

            </div>

            <!-- input profile -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-key"></i></span>

                <select class="form-control input-lg" name="newProfile">

                  <option value="">Select Level</option>
                  <option value="Administrator">Administrator</option>
                  <option value="Special">Special</option>
                  <option value="Seller">Seller</option>

                </select>

              </div>

            </div>

            <!-- Uploading image -->
            <div class="form-group">

              <div class="panel">Upload image</div>

              <input class="newPics" type="file" name="newPhoto">

              <p class="help-block">Maximum size 2Mb</p>

              <img class="thumbnail preview" src="views/img/users/default/prfplaceholder.png" width="100px">

            </div>

          </div>

        </div>

        <!--=====================================
        FOOTER
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-bs-dismiss="modal">Close</button>

          <button type="submit" class="btn btn-success">Save</button>

        </div>

          <?php
            $createUser = new ControllerUsers();
            $createUser -> ctrCreateUser();
          ?>

      </form>

    </div>

  </div>

</div>
<!--====  End of module add user  ====-->

<!--=====================================
=            module edit user            =
======================================-->

<!-- Modal -->
<div id="editUser" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">

      <form role="form" method="POST" enctype="multipart/form-data">

        <!--=====================================
        HEADER
        ======================================-->

        <div class="modal-header" style="background: #DD4B39; color: #fff">

          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>

          <h4 class="modal-title">Edit User</h4>

        </div>

        <!--=====================================
        BODY
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!--Input name -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <input class="form-control input-lg" type="text" id="EditName" name="EditName" placeholder="Edit name" required>

              </div>

            </div>

            <!-- input username -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-key"></i></span>

                <input class="form-control input-lg" type="text" id="EditUser" name="EditUser" placeholder="Edit username" readonly>

              </div>

            </div>

            <!-- input password -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-lock"></i></span>

                <input class="form-control input-lg" type="password" name="EditPasswd" placeholder="Leave blank to keep current password" minlength="12" maxlength="256" autocomplete="new-password">

              </div>

            </div>

            <!-- input profile -->
            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-key"></i></span>

                <select class="form-control input-lg" name="EditProfile">

                  <option value="" id="EditProfile"></option>
                  <option value="Administrator">Administrator</option>
                  <option value="Special">Special</option>
                  <option value="Seller">Seller</option>

                </select>

              </div>

            </div>

            <!-- Uploading image -->
            <div class="form-group">

              <div class="panel">Upload image</div>

              <input class="newPics" type="file" name="editPhoto">

              <p class="help-block">Maximum size 2Mb</p>

              <img class="thumbnail preview" src="views/img/users/default/prfplaceholder.png" alt="" width="100">

            </div>

          </div>

        </div>

        <!--=====================================
        FOOTER
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-bs-dismiss="modal">Close</button>

          <button type="submit" class="btn btn-success">Edit User</button>

        </div>

          <?php
            $editUser = new ControllerUsers();
            $editUser -> ctrEditUser();
          ?>

      </form>

    </div>

  </div>

</div>

<?php

  $deleteUser = new ControllerUsers();
  $deleteUser -> ctrDeleteUser();

?>
