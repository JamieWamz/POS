<?php
$routeKey = isset($_GET['route']) ? (string) $_GET['route'] : 'home';
$routeNames = [
    'home' => 'Dashboard',
    'create-sale' => 'New sale',
    'edit-sale' => 'Edit sale',
    'sales' => 'Sales',
    'products' => 'Products',
    'categories' => 'Categories',
    'customers' => 'Customers',
    'expenses' => 'Expenses',
    'reports' => 'Sales reports',
    'expenses-report' => 'Expense reports',
    'users' => 'Team access',
    'activity' => 'Activity log',
];
$routeName = $routeNames[$routeKey] ?? 'Golden Tap POS';
$canSell = user_has_role(['Administrator', 'Seller']);
?>
<header class="pos-topbar">
  <div class="pos-topbar__context">
    <button class="btn pos-nav-toggle" type="button" data-pos-nav-toggle aria-controls="posSidebar" aria-expanded="false">
      <i class="fa fa-bars" aria-hidden="true"></i>
      <span class="visually-hidden">Open navigation</span>
    </button>
    <div>
      <span class="pos-topbar__date"><?php echo e(date('l, j F')); ?></span>
      <strong><?php echo e($routeName); ?></strong>
    </div>
  </div>

  <div class="pos-topbar__actions">
    <?php if ($canSell && !in_array($routeKey, ['home', 'create-sale'], true)): ?>
      <a class="btn btn-primary d-none d-sm-inline-flex align-items-center gap-2" href="create-sale">
        <i class="fa fa-plus" aria-hidden="true"></i>
        New sale
      </a>
    <?php endif; ?>

    <div class="dropdown">
      <button class="btn pos-user-button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="<?php echo e($_SESSION['photo'] ?: 'views/img/users/default/prfplaceholder.png'); ?>" alt="">
        <span class="d-none d-md-flex">
          <strong><?php echo e($_SESSION['name']); ?></strong>
          <small><?php echo e($_SESSION['profile']); ?></small>
        </span>
      </button>
      <div class="dropdown-menu dropdown-menu-end pos-account-menu">
        <div class="pos-account-menu__identity">
          <strong><?php echo e($_SESSION['name']); ?></strong>
          <span>@<?php echo e($_SESSION['user']); ?></span>
        </div>
        <div class="dropdown-divider"></div>
        <form method="post" action="logout">
          <?php echo csrf_field(); ?>
          <button class="dropdown-item" type="submit">
            <i class="fa fa-sign-out" aria-hidden="true"></i>
            Sign out
          </button>
        </form>
      </div>
    </div>
  </div>
</header>
