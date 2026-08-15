<?php
$currentRoute = isset($_GET['route']) ? (string) $_GET['route'] : 'home';
$navigationGroups = [
    'Overview' => [
        ['route' => 'home', 'label' => 'Dashboard', 'icon' => 'fa-dashboard', 'roles' => ['Administrator', 'Special']],
    ],
    'Sales' => [
        ['route' => 'create-sale', 'label' => 'New sale', 'icon' => 'fa-plus-circle', 'roles' => ['Administrator', 'Seller']],
        ['route' => 'sales', 'label' => 'Sales history', 'icon' => 'fa-shopping-bag', 'roles' => ['Administrator', 'Seller']],
        ['route' => 'customers', 'label' => 'Customers', 'icon' => 'fa-users', 'roles' => ['Administrator', 'Seller']],
    ],
    'Stock' => [
        ['route' => 'products', 'label' => 'Products', 'icon' => 'fa-cube', 'roles' => ['Administrator', 'Special']],
        ['route' => 'categories', 'label' => 'Categories', 'icon' => 'fa-tags', 'roles' => ['Administrator', 'Special']],
    ],
    'Business' => [
        ['route' => 'expenses', 'label' => 'Expenses', 'icon' => 'fa-credit-card', 'roles' => ['Administrator', 'Seller']],
        ['route' => 'reports', 'label' => 'Sales reports', 'icon' => 'fa-line-chart', 'roles' => ['Administrator', 'Seller']],
        ['route' => 'expenses-report', 'label' => 'Expense reports', 'icon' => 'fa-pie-chart', 'roles' => ['Administrator', 'Seller']],
    ],
    'Administration' => [
        ['route' => 'users', 'label' => 'Team access', 'icon' => 'fa-user-circle', 'roles' => ['Administrator']],
        ['route' => 'activity', 'label' => 'Activity log', 'icon' => 'fa-history', 'roles' => ['Administrator']],
    ],
];
?>
<aside class="pos-sidebar" id="posSidebar" aria-label="Primary navigation">
  <a class="pos-sidebar__brand" href="home">
    <span class="pos-brand-mark">GT</span>
    <span>
      <strong>Golden Tap</strong>
      <small>Point of sale</small>
    </span>
  </a>

  <nav class="pos-sidebar__nav">
    <?php foreach ($navigationGroups as $groupLabel => $items): ?>
      <?php
      $visibleItems = array_values(array_filter($items, static fn (array $item): bool => user_has_role($item['roles'])));
      if ($visibleItems === []) {
          continue;
      }
      ?>
      <div class="pos-nav-group">
        <span class="pos-nav-group__label"><?php echo e($groupLabel); ?></span>
        <?php foreach ($visibleItems as $item): ?>
          <a class="pos-nav-link <?php echo $currentRoute === $item['route'] ? 'active' : ''; ?>" href="<?php echo e($item['route']); ?>" <?php echo $currentRoute === $item['route'] ? 'aria-current="page"' : ''; ?>>
            <i class="fa <?php echo e($item['icon']); ?>" aria-hidden="true"></i>
            <span><?php echo e($item['label']); ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>
  </nav>

  <div class="pos-sidebar__footer">
    <span class="pos-status-dot" aria-hidden="true"></span>
    <span>
      <strong>Register ready</strong>
      <small><?php echo e(app_config('business.currency', 'K')); ?> · <?php echo e(app_config('business.tax_label', 'VAT')); ?> <?php echo e(number_format((float) app_config('business.default_tax_rate', 16), 0)); ?>%</small>
    </span>
  </div>
</aside>
