<?php

$expenses = ControllerExpenses::ctrExpensesDatesRange($initialDate, $finalDate);
$categories = array();

if(!empty($expenses)){
  foreach ($expenses as $key => $value) {
    $cat = !empty($value["category"]) ? $value["category"] : "Uncategorized";
    if(!isset($categories[$cat])){
      $categories[$cat] = 0;
    }
    $categories[$cat] += $value["amount"];
  }
}

?>

<div class="box box-warning">
  <div class="box-header with-border">
    <h3 class="box-title">Expenses by Category</h3>
  </div>
  <div class="box-body">
    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Category</th>
          <th>Total Spent</th>
        </tr>
      </thead>
      <tbody>
        <?php
        if(!empty($categories)){
          foreach ($categories as $catName => $total) {
            echo '<tr>
                    <td>'.e($catName).'</td>
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
