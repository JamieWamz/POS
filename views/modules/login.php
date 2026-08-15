<main class="pos-login">
  <div class="container-fluid pos-login__container">
    <div class="row g-0 pos-login__layout">
      <section class="col-lg-7 pos-login__identity" aria-labelledby="loginBrandTitle">
        <div class="pos-login__brandline">
          <span class="pos-brand-mark">GT</span>
          <span>
            <strong><?php echo e(app_config('business.name', 'Golden Tap')); ?></strong>
            <small>Point of sale</small>
          </span>
        </div>

        <div class="pos-login__intro">
          <span class="pos-section-label">Staff workspace</span>
          <h1 id="loginBrandTitle">Sales, stock and daily operations.</h1>
          <p>Use this register to process transactions, issue receipts and keep inventory records current.</p>
        </div>

        <dl class="pos-login__details">
          <div>
            <dt>Location</dt>
            <dd><?php echo e(app_config('business.address', 'Kafue')); ?></dd>
          </div>
          <div>
            <dt>Business time</dt>
            <dd><?php echo e(date('H:i')); ?> · <?php echo e(app_config('timezone', 'Africa/Lusaka')); ?></dd>
          </div>
          <div>
            <dt>Tax setting</dt>
            <dd><?php echo e(app_config('business.tax_label', 'VAT')); ?> <?php echo e(number_format((float) app_config('business.default_tax_rate', 16), 0)); ?>%</dd>
          </div>
        </dl>
      </section>

      <section class="col-lg-5 pos-login__access">
        <div class="pos-login-card">
          <div class="pos-login-card__heading">
            <span class="pos-section-label">Authorised staff only</span>
            <h2>Sign in</h2>
            <p>Enter the username and password assigned by your administrator.</p>
          </div>

          <form method="post" autocomplete="on">
            <?php echo csrf_field(); ?>
            <div class="mb-3">
              <label class="form-label" for="loginUser">Username</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fa fa-user" aria-hidden="true"></i></span>
                <input id="loginUser" type="text" class="form-control" name="loginUser" autocomplete="username" required autofocus>
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label" for="loginPass">Password</label>
              <div class="input-group input-group-lg">
                <span class="input-group-text"><i class="fa fa-lock" aria-hidden="true"></i></span>
                <input id="loginPass" type="password" class="form-control" name="loginPass" autocomplete="current-password" required>
              </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg w-100">Sign in to register</button>
            <?php (new ControllerUsers())->ctrUserLogin(); ?>
          </form>

          <p class="pos-login-card__help"><i class="fa fa-shield" aria-hidden="true"></i> Sessions are protected and administrator actions are recorded.</p>
        </div>
      </section>
    </div>
  </div>
</main>
