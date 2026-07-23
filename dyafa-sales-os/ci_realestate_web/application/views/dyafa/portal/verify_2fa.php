<div class="dso-card">
    <h2>Two-Factor Authentication</h2>
    <p class="dso-guest-subtitle">Enter the 6-digit code from your authenticator app to continue.</p>
    <?php if (!empty($error)): ?><div class="dso-alert error"><?php echo $error; ?></div><?php endif; ?>
    <form class="dso-form" method="post">
        <label>6-digit code</label>
        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
        <br><br>
        <button type="submit" class="dso-btn">Verify</button>
    </form>
</div>
