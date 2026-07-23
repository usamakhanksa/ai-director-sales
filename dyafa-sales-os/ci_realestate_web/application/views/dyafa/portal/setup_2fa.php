<div class="dso-card">
    <h2>Set Up Two-Factor Authentication</h2>
    <p class="dso-guest-subtitle">Your Finance role requires two-factor authentication. Add this account to an authenticator app (Google Authenticator, Microsoft Authenticator, Authy, etc.), then enter the 6-digit code it shows to finish signing in.</p>
    <?php if (!empty($error)): ?><div class="dso-alert error"><?php echo $error; ?></div><?php endif; ?>

    <p style="font-size:12.5px;">Can't scan a QR code? Enter this manually in your authenticator app:</p>
    <p style="font-family:monospace;font-size:15px;letter-spacing:0.05em;background:var(--color-muted-soft);padding:10px 12px;border-radius:var(--radius-sm);word-break:break-all;"><?php echo htmlspecialchars($secret); ?></p>
    <p style="font-size:11.5px;color:var(--color-muted);word-break:break-all;">Setup URI: <?php echo htmlspecialchars($otpauth_uri); ?></p>

    <form class="dso-form" method="post">
        <label>6-digit code</label>
        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" autocomplete="one-time-code" required autofocus>
        <br><br>
        <button type="submit" class="dso-btn">Verify & Continue</button>
    </form>
</div>
