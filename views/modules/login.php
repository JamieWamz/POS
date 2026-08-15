<main class="pos-login" role="main">
  <section class="pos-login__brand" aria-label="Golden Tap POS">
    <div class="pos-login__brand-content">
      <span class="pos-brand-mark">GT</span>
      <p class="pos-eyebrow">Golden Tap</p>
      <h1>A faster, clearer way to run every shift.</h1>
      <p>Sales, stock, customers, and daily performance in one focused workspace.</p>
      <div class="pos-login__features">
        <span><i class="fa fa-bolt"></i> Fast checkout</span>
        <span><i class="fa fa-lock"></i> Secure access</span>
        <span><i class="fa fa-line-chart"></i> Live reporting</span>
      </div>
    </div>
  </section>
  <section class="pos-login__panel">
    <div class="login-box">
      <div class="login-box-body">
        <div class="pos-login__heading">
          <span class="pos-brand-mark pos-brand-mark--small">GT</span>
          <div>
            <p class="pos-eyebrow">Welcome back</p>
            <h2>Sign in to your register</h2>
          </div>
        </div>
        <form method="post" autocomplete="on">
          <?php echo csrf_field(); ?>
          <div class="form-group">
            <label for="loginUser">Username</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-user"></i></span>
              <input id="loginUser" type="text" class="form-control input-lg" placeholder="Your username" name="loginUser" autocomplete="username" required autofocus>
            </div>
          </div>
          <div class="form-group">
            <label for="loginPass">Password</label>
            <div class="input-group">
              <span class="input-group-addon"><i class="fa fa-lock"></i></span>
              <input id="loginPass" type="password" class="form-control input-lg" placeholder="Your password" name="loginPass" autocomplete="current-password" required>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-lg btn-block">Sign in <i class="fa fa-arrow-right"></i></button>
          <?php (new ControllerUsers())->ctrUserLogin(); ?>
        </form>
      </div>
    </div>
  </section>
</main>
