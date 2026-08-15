<?php

if($_SESSION["profile"] == "Special"){

  echo '<script>

    window.location = "home";

  </script>';

  return;

}

?>

<div class="content-wrapper">

  <section class="content-header">

    <div><span class="pos-section-label">Sales</span><h1>Customers</h1><p class="pos-page-description">Maintain contact details and purchase history for real customers.</p></div>

    <ol class="breadcrumb">

      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>

      <li class="active">Customers</li>

    </ol>

  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addCustomer">

        Add Customer

        </button>

      </div>
      <div class="box-body">
        <table class="table table-bordered table-hover table-striped dt-responsive tables" width="100%">

          <thead>

           <tr>

             <th style="width:10px">#</th>
             <th>Name</th>
             <th>Email</th>
             <th>Contact</th>
             <th>Address</th>
             <th>Birthday</th>
             <th>Total Purchases</th>
             <th>Last Purchase</th>
             <th>Last login</th>
             <th>Actions</th>

           </tr>

          </thead>

          <tbody>

          <?php

            $item = null;
            $valor = null;

            $Customers = controllerCustomers::ctrShowCustomers($item, $valor);

            foreach ($Customers as $key => $value) {


              echo '<tr>

                      <td>'.($key+1).'</td>

                      <td>'.e($value["name"]).'</td>

                      <td>'.e($value["email"]).'</td>

                      <td>'.e($value["phone"]).'</td>

                      <td>'.e($value["address"]).'</td>

                      <td>'.e($value["birthdate"]).'</td>

                      <td>'.$value["purchases"].'</td>

                      <td>'.e($value["lastPurchase"]).'</td>

                      <td>'.e($value["registerDate"]).'</td>

                      <td>

                        <div class="btn-group">

                          <button class="btn btn-primary btnEditCustomer" data-bs-toggle="modal" data-bs-target="#modalEditCustomer" idCustomer="'.$value["id"].'"><i class="fa fa-pencil"></i></button>

                          '.(current_user_role() === 'Administrator' ? '<button class="btn btn-danger btnDeleteCustomer" idCustomer="'.(int)$value["id"].'"><i class="fa fa-trash"></i></button>' : '').'

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
MODAL ADD CUSTOMER
======================================-->

<div id="addCustomer" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="POST">

        <!--=====================================
        MODAL HEADER
        ======================================-->

        <div class="modal-header" style="background: #DD4B39; color: #fff">

          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>

          <h4 class="modal-title">Add Customer</h4>

        </div>

        <!--=====================================
        MODAL BODY
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

             <!-- NAME INPUT -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input class="form-control input-lg" type="text" name="newCustomer" placeholder="Write name" required>
              </div>
            </div>

            <!-- EMAIL INPUT -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                <input class="form-control input-lg" type="text" name="newEmail" placeholder="Email" required>
              </div>
            </div>

            <!-- PHONE INPUT -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                <input class="form-control input-lg" type="text" name="newPhone" placeholder="phone" data-inputmask="'mask':'(999) 999-999999'" data-mask required>
              </div>
            </div>

            <!-- ADDRESS INPUT -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
                <input class="form-control input-lg" type="text" name="newAddress" placeholder="Address" required>
              </div>
            </div>

             <!-- BIRTH DATE INPUT -->

            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input class="form-control input-lg" type="text" name="newBirthdate" placeholder="Birth Date" data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>
              </div>
            </div>

          </div>

        </div>

        <!--=====================================
        MODAL FOOTER
        ======================================-->

        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-success">Save Customer</button>
        </div>
      </form>

      <?php

        $createCustomer = new ControllerCustomers();
        $createCustomer -> ctrCreateCustomer();

      ?>
    </div>

  </div>

</div>

<!--=====================================
MODAL EDIT CUSTOMER
======================================-->

<div id="modalEditCustomer" class="modal fade" role="dialog">

  <div class="modal-dialog">

    <div class="modal-content">

      <form role="form" method="post">

        <!--=====================================
        MODAL HEADER
        ======================================-->

        <div class="modal-header" style="background:#DD4B39; color:white">

          <button type="button" class="close" data-bs-dismiss="modal">&times;</button>

          <h4 class="modal-title">Edit Customer</h4>

        </div>

        <!--=====================================
        MODAL BODY
        ======================================-->

        <div class="modal-body">

          <div class="box-body">

            <!-- NAME INPUT -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-user"></i></span>

                <input type="text" class="form-control input-lg" name="editCustomer" id="editCustomer" required>
                <input type="hidden" id="idCustomer" name="idCustomer">
              </div>

            </div>

            <!-- EMAIL INPUT -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-envelope"></i></span>

                <input type="email" class="form-control input-lg" name="editEmail" id="editEmail" required>

              </div>

            </div>

            <!-- PHONE INPUT -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-phone"></i></span>

                <input type="text" class="form-control input-lg" name="editPhone" id="editPhone" data-inputmask="'mask':'(999) 999-999999'" data-mask required>

              </div>

            </div>

            <!-- ADDRESS INPUT -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-map-marker"></i></span>

                <input type="text" class="form-control input-lg" name="editAddress" id="editAddress"  required>

              </div>

            </div>

            <!-- BIRTH DATE INPUT -->

            <div class="form-group">

              <div class="input-group">

                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>

                <input type="text" class="form-control input-lg" name="editBirthdate" id="editBirthdate"  data-inputmask="'alias': 'yyyy/mm/dd'" data-mask required>

              </div>

            </div>

          </div>

        </div>

        <!--=====================================
        MODAL FOOTER
        ======================================-->

        <div class="modal-footer">

          <button type="button" class="btn btn-danger pull-left" data-bs-dismiss="modal">Close</button>

          <button type="submit" class="btn btn-success">Save Changes</button>

        </div>

      </form>

      <?php

        $EditCustomer = new ControllerCustomers();
        $EditCustomer -> ctrEditCustomer();

      ?>

    </div>

  </div>

</div>

<?php

  $deleteCustomer = new ControllerCustomers();
  $deleteCustomer -> ctrDeleteCustomer();

?>
