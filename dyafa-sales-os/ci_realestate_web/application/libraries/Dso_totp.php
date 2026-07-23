<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Dso_totp
 *
 * Pure-PHP RFC 6238 (TOTP) / RFC 4226 (HOTP) implementation - no Composer
 * package, matching this app's convention of vendoring small self-contained
 * libraries (see Dso_llm_client's adapter classes) rather than adding a
 * dependency manager. Used to make 2FA mandatory for the CorporateFinance
 * portal sub-role (see Portal.php) without requiring any external service.
 *
 * There is no QR code generation here (that needs an image/canvas encoder,
 * out of scope for a hand-rolled library) - enrollment shows the secret and
 * the otpauth:// URI as plain text for manual entry into any authenticator
 * app (Google Authenticator, Microsoft Authenticator, Authy, etc.), which is
 * the standard fallback every one of those apps already supports.
 */
class Dso_totp
{
    const PERIOD = 30;
    const DIGITS = 6;

    /** Generates a random 20-byte (160-bit) secret, Base32-encoded. */
    public function generate_secret()
    {
        $bytes = function_exists('random_bytes') ? random_bytes(20) : openssl_random_pseudo_bytes(20);
        return $this->base32_encode($bytes);
    }

    /** otpauth:// URI for manual entry / authenticator app import. */
    public function otpauth_uri($secret, $account_label, $issuer = 'Dyafa Sales OS')
    {
        return 'otpauth://totp/' . rawurlencode($issuer) . ':' . rawurlencode($account_label)
            . '?secret=' . $secret . '&issuer=' . rawurlencode($issuer)
            . '&period=' . self::PERIOD . '&digits=' . self::DIGITS . '&algorithm=SHA1';
    }

    /** Current 6-digit code for $secret (used only internally by verify_code's window check). */
    public function code_at($secret, $timestamp)
    {
        $counter = (int) floor($timestamp / self::PERIOD);
        $key = $this->base32_decode($secret);
        $binary_counter = pack('N*', 0, $counter);
        $hash = hash_hmac('sha1', $binary_counter, $key, true);
        $offset = ord($hash[19]) & 0x0f;
        $code = ((ord($hash[$offset]) & 0x7f) << 24)
            | (ord($hash[$offset + 1]) << 16)
            | (ord($hash[$offset + 2]) << 8)
            | (ord($hash[$offset + 3]));
        $code %= pow(10, self::DIGITS);
        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Verifies a user-submitted code against $secret, allowing +/- $window
     * time steps (default 1 = +/-30s) to tolerate clock drift between the
     * server and the user's phone.
     */
    public function verify_code($secret, $submitted_code, $window = 1)
    {
        $submitted_code = preg_replace('/\s+/', '', (string) $submitted_code);
        if (!preg_match('/^\d{6}$/', $submitted_code)) {
            return false;
        }
        $now = time();
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->code_at($secret, $now + ($i * self::PERIOD)), $submitted_code)) {
                return true;
            }
        }
        return false;
    }

    protected function base32_encode($binary)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        for ($i = 0; $i < strlen($binary); $i++) {
            $bits .= str_pad(decbin(ord($binary[$i])), 8, '0', STR_PAD_LEFT);
        }
        $output = '';
        foreach (str_split($bits, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= $alphabet[bindec($chunk)];
        }
        return $output;
    }

    protected function base32_decode($base32)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32 = strtoupper(rtrim($base32, '='));
        $bits = '';
        for ($i = 0; $i < strlen($base32); $i++) {
            $pos = strpos($alphabet, $base32[$i]);
            if ($pos === false) {
                continue;
            }
            $bits .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $binary = '';
        foreach (str_split($bits, 8) as $byte) {
            if (strlen($byte) === 8) {
                $binary .= chr(bindec($byte));
            }
        }
        return $binary;
    }
}
