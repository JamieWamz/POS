<?php
$currentRoute = isset($_GET['route']) ? (string) $_GET['route'] : 'home';
$navigation = [
    ['route' => 'home', 'label' => 'Dashboard', 'icon' => 'fa-dashboard', 'roles' => ['Administrator', 'Special']],
    ['route' => 'create-sale', 'label' => 'New sale', 'icon' => 'fa-plus-circle', 'roles' => ['Administrator', 'Seller']],
    ['route' => 'sales', 'label' => 'Sales history', 'icon' => 'fa-shopping-bag', 'roles' => ['Administrator', 'Seller']],
    ['route' => 'products', 'label' => 'Products', 'icon' => 'fa-cube', 'roles' => ['Administrator', 'Special']],
    ['route' => 'categories', 'label' => 'Categories', 'icon' => 'fa-tags', 'roles' => ['Administrator', 'Special']],
    ['route' => 'customers', 'label' => 'Customers', 'icon' => 'fa-users', 'roles' => ['Administrator', 'Seller']],
    ['route' => 'expenses', 'label' => 'Expenses', 'icon' => 'fa-credit-card', 'roles' => ['Administrator', 'Seller']],
    ['route' => 'reports', 'label' => 'Sales reports', 'icon' => 'fa-line-chart', 'roles' => ['Administrator', 'Seller']],
    ['route' => 'expenses-report', 'label' => 'Expense reports', 'icon' => 'fa-pie-chart', 'roles' => ['Administrator', 'Seller']],
    ['route' => 'users', 'label' => 'Team access', 'icon' => 'fa-user-circle', 'roles' => ['Administrator']],
    ['route' => 'activity', 'label' => 'Activity log', 'icon' => 'fa-history', 'roles' => ['Administrator']],
];
?>
<aside class="main-sidebar" aria-label="Primary navigation">
  <section class="sidebar">
    <div class="pos-sidebar-label">Workspace</div>
    <ul class="sidebar-menu">
      <?php foreach ($navigation as $item): ?>
        <?php if (user_has_role($item['roles'])): ?>
          <li class="<?php echo $currentRoute === $item['route'] ? 'active' : ''; ?>">
            <a href="<?php echo e($item['route']); ?>">
              <i class="fa <?php echo e($item['icon']); ?>" aria-hidden="true"></i>
              <span><?php echo e($item['label']); ?></span>
            </a>
          </li>
        <?php endif; ?>
      <?php endforeach; ?>
    </ul>
    <div class="pos-sidebar-footer">
      <span class="pos-status-dot"></span>
      <span>Register online</span>
    </div>
  </section>
</aside>
