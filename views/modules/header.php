<header class="main-header">
  <a href="home" class="logo" aria-label="Golden Tap POS home">
    <span class="logo-mini">GT</span>
    <span class="logo-lg"><strong>Golden Tap</strong><small> POS</small></span>
  </a>
  <nav class="navbar navbar-static-top" role="navigation" aria-label="Account controls">
    <a class="sidebar-toggle" data-toggle="push-menu" role="button" href="#" aria-label="Toggle navigation">
      <span class="sr-only">Toggle navigation</span>
    </a>
    <div class="pos-register-title">
      <span class="pos-eyebrow">Point of sale</span>
      <strong><?php echo e(date('l, j F')); ?></strong>
    </div>
    <div class="navbar-custom-menu">
      <ul class="nav navbar-nav">
        <li class="dropdown user user-menu">
          <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
            <img class="user-image" src="<?php echo e($_SESSION['photo'] ?: 'views/img/users/default/prfplaceholder.png'); ?>" alt="">
            <span class="hidden-xs pos-user-copy">
              <strong><?php echo e($_SESSION['name']); ?></strong>
              <small><?php echo e($_SESSION['profile']); ?></small>
            </span>
          </a>
          <ul class="dropdown-menu pos-account-menu">
            <li class="user-header">
              <strong><?php echo e($_SESSION['name']); ?></strong>
              <span><?php echo e($_SESSION['user']); ?> · <?php echo e($_SESSION['profile']); ?></span>
            </li>
            <li class="user-footer">
              <form method="post" action="logout">
                <?php echo csrf_field(); ?>
                <button class="btn btn-default btn-block" type="submit"><i class="fa fa-sign-out"></i> Sign out</button>
              </form>
            </li>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
</header>
