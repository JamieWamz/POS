<div class="content-wrapper">

  <section class="content-header">
    <h1>Manage Expenses</h1>
    <ol class="breadcrumb">
      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Expenses</li>
    </ol>
  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">
        <button class="btn btn-primary" data-toggle="modal" data-target="#modalAddExpense">
          Add Expense
        </button>
      </div>

      <div class="box-body">
        <table class="table table-bordered table-striped dt-responsive dtTables" width="100%">
  <thead>
    <tr>
      <th style="width:10px">#</th>
      <th>Description</th>
      <th>Category</th>
      <th>Amount</th>
      <th>Issued By</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
      $item = null;
      $value = null;
      $expenses = ControllerExpenses::ctrShowExpenses($item, $value);

      foreach ($expenses as $key => $value) {

        // Fetch User Info (Same as bill.php)
        $itemUser = "id";
        $valueUser = $value["id_user"];
        $user = ControllerUsers::ctrShowUsers($itemUser, $valueUser);
        $userName = isset($user["name"]) ? $user["name"] : "N/A";

        echo '<tr>
                <td>'.($key + 1).'</td>
                <td>'.e($value["description"]).'</td>
                <td>'.e($value["category"]).'</td>
                <td>K'.number_format((float)$value["amount"], 2).'</td>
                <td>'.e($userName).'</td>
                <td>'.e($value["date"]).'</td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-warning btnEditExpense" idExpense="'.$value["id"].'" data-toggle="modal" data-target="#modalEditExpense"><i class="fa fa-pencil"></i></button>
                    '.(current_user_role() === 'Administrator' ? '<button class="btn btn-danger btnDeleteExpense" idExpense="'.(int)$value["id"].'"><i class="fa fa-times"></i></button>' : '').'
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
MODAL ADD EXPENSE
======================================-->
<div id="modalAddExpense" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">

      <form role="form" method="post">

        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Expense</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">

            <!-- Description -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                <input type="text" class="form-control input-lg" name="newDescription" placeholder="Description" required>
              </div>
            </div>

            <!-- Category -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-list"></i></span>
                <input type="text" class="form-control input-lg" name="newCategory" placeholder="Category (e.g., Rent, Utilities, Transport)">
              </div>
            </div>

            <!-- Amount -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money"></i></span>
                <input type="number" step="0.01" min="0" class="form-control input-lg" name="newAmount" placeholder="Amount" required>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Expense</button>
        </div>

        <?php
          $createExpense = new ControllerExpenses();
          $createExpense -> ctrCreateExpense();
        ?>

      </form>

    </div>
  </div>
</div>

<!--=====================================
MODAL EDIT EXPENSE
======================================-->
<div id="modalEditExpense" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">

      <form role="form" method="post">

        <div class="modal-header" style="background:#3c8dbc; color:white">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Expense</h4>
        </div>

        <div class="modal-body">
          <div class="box-body">

            <!-- Description -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-th"></i></span>
                <input type="text" class="form-control input-lg" id="editDescription" name="editDescription" required>
                <input type="hidden" id="idExpense" name="idExpense">
              </div>
            </div>

            <!-- Category -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-list"></i></span>
                <input type="text" class="form-control input-lg" id="editCategory" name="editCategory">
              </div>
            </div>

            <!-- Amount -->
            <div class="form-group">
              <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-money"></i></span>
                <input type="number" step="0.01" min="0" class="form-control input-lg" id="editAmount" name="editAmount" required>
              </div>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>

        <?php
          $editExpense = new ControllerExpenses();
          $editExpense -> ctrEditExpense();
        ?>

      </form>

    </div>
  </div>
</div>

<?php
  $deleteExpense = new ControllerExpenses();
  $deleteExpense -> ctrDeleteExpense();
?>
