<div class="dso-card">
    <h2>Sign In</h2>
    <p class="dso-guest-subtitle">Sales team &amp; staff login</p>
    <?php if (!empty($error)): ?>
        <div class="dso-alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    <form class="dso-form" method="post" action="<?php echo base_url('dyafa/auth/authenticate'); ?>">
        <label>Username</label>
        <input type="text" name="username" required autofocus>
        <label>Password</label>
        <input type="password" name="password" required>
        <br><br>
        <button type="submit" class="dso-btn">Sign In</button>
    </form>
    <p style="margin-top:16px;font-size:12px;color:var(--color-muted);text-align:center;">Corporate clients: use <a href="<?php echo base_url('dyafa/portal/login'); ?>">the client portal login</a>.</p>
</div>
