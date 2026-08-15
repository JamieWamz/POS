<?php

if($_SESSION["profile"] == "Special"){
  echo '<script>window.location = "home";</script>';
  return;
}

?>
<div class="content-wrapper">

  <section class="content-header">
    <div><span class="pos-section-label">Business</span><h1>Expense report</h1><p class="pos-page-description">Review spend by date, category and team member.</p></div>
    <ol class="breadcrumb">
      <li><a href="home"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Expenses Report</li>
    </ol>
  </section>

  <section class="content">

    <div class="box">

      <div class="box-header with-border">

        <div class="input-group">
          <button type="button" class="btn btn-default" id="daterange-btn-expenses">
            <span>
              <i class="fa fa-calendar"></i> Date range
            </span>
            <i class="fa fa-caret-down"></i>
          </button>
        </div>

        <div class="box-tools pull-right">
          <?php
          if(valid_date(isset($_GET["initialDate"]) ? (string)$_GET["initialDate"] : null) && valid_date(isset($_GET["finalDate"]) ? (string)$_GET["finalDate"] : null)){
            echo '<a href="views/modules/download-expenses-report.php?report=expenses&amp;initialDate='.rawurlencode($_GET["initialDate"]).'&amp;finalDate='.rawurlencode($_GET["finalDate"]).'">';
          }else{
            echo '<a href="views/modules/download-expenses-report.php?report=expenses">';
          }
          ?>
             <button class="btn btn-success" style="margin-top:5px">Export CSV</button>
          </a>
        </div>

      </div>

      <div class="box-body">
        <div class="row">

          <!-- Expenses Graph -->
          <div class="col-xs-12">
            <?php include "reports/expenses-graph.php"; ?>
          </div>

          <!-- Expenses by Category -->
          <div class="col-md-6 col-xs-12">
            <?php include "reports/expenses-category.php"; ?>
          </div>

          <!-- Expenses by User -->
          <div class="col-md-6 col-xs-12">
            <?php include "reports/expenses-users.php"; ?>
          </div>

        </div>
      </div>

    </div>

  </section>

</div>

<script>
$('#daterange-btn-expenses').daterangepicker(
  {
    ranges   : {
      'Today'       : [moment(), moment()],
      'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
      'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
      'Last 30 Days': [moment().subtract(29, 'days'), moment()],
      'This Month'  : [moment().startOf('month'), moment().endOf('month')],
      'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    },
    startDate: moment(),
    endDate  : moment()
  },
  function (start, end) {
    $('#daterange-btn-expenses span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    var initialDate = start.format('YYYY-MM-DD');
    var finalDate = end.format('YYYY-MM-DD');
    window.location = "index.php?route=expenses-report&initialDate=" + initialDate + "&finalDate=" + finalDate;
  }
);
</script>
