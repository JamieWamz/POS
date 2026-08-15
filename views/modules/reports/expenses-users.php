<?php

$expenses = ControllerExpenses::ctrExpensesDatesRange($initialDate, $finalDate);
$usersSpent = array();

if(!empty($expenses)){
  foreach ($expenses as $key => $value) {
    $userId = $value["id_user"];

    if(!isset($usersSpent[$userId])){
      $usersSpent[$userId] = 0;
    }
    $usersSpent[$userId] += $value["amount"];
  }
}

?>

<div class="box box-primary">
  <div class="box-header with-border">
    <h3 class="box-title">Expenses Issued by Users</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>User</th>
          <th>Total Issued</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if(!empty($usersSpent)){
          foreach ($usersSpent as $uId => $total) {
            $user = ControllerUsers::ctrShowUsers("id", $uId);
            $userName = (isset($user["name"])) ? $user["name"] : "N/A";

            echo '<tr>
                    <td>'.e($userName).'</td>
                    <td>K '.number_format($total, 2).'</td>
                  </tr>';
          }
        } else {
          echo '<tr><td colspan="2">No data available</td></tr>';
        }
        ?>
      </tbody>
    </table>
  </div>
</div>
