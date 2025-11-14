<?php
/**
 * Helper per gestione TOTP compatibile con Google Authenticator
 */

class Totp {
    private const DEFAULT_PERIOD = 30; // secondi
    private const DEFAULT_DIGITS = 6;
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Genera un segreto in formato Base32
     */
    public static function generateSecret(int $length = 32): string {
        $length = max(16, $length);
        $bytes = random_bytes($length);
        return self::base32Encode($bytes);
    }

    /**
     * Restituisce l'URI otpauth compatibile con app TOTP
     */
    public static function getProvisioningUri(string $email, string $secret, string $issuer = 'ZPeC Professionisti'): string {
        $label = rawurlencode($issuer . ':' . $email);
        $query = http_build_query([
            'secret' => strtoupper($secret),
            'issuer' => $issuer,
            'period' => self::DEFAULT_PERIOD,
            'digits' => self::DEFAULT_DIGITS,
        ]);

        return "otpauth://totp/{$label}?{$query}";
    }

    /**
     * Restituisce l'URL per generare il QR Code (Google Chart API)
     */
    public static function getQrCodeUrl(string $provisioningUri, int $size = 250): string {
        $chl = urlencode($provisioningUri);
        $size = max(150, min(600, $size));
        return "https://chart.googleapis.com/chart?cht=qr&chs={$size}x{$size}&chld=M|0&chl={$chl}";
    }

    /**
     * Verifica un codice TOTP (tolleranza di +- $window intervalli)
     */
    public static function verifyCode(string $secret, string $code, int $window = 1, ?int $timestamp = null): bool {
        $code = trim($code);
        if (!ctype_digit($code) || strlen($code) !== self::DEFAULT_DIGITS) {
            return false;
        }

        $timestamp = $timestamp ?? time();
        $secretBinary = self::base32Decode($secret);
        if ($secretBinary === null) {
            return false;
        }

        $timeSlice = (int) floor($timestamp / self::DEFAULT_PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            $totp = self::calculateTotp($secretBinary, $timeSlice + $i);
            if (hash_equals($totp, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calcola il codice TOTP per uno specifico time slice
     */
    private static function calculateTotp(string $secretBinary, int $timeSlice): string {
        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $time, $secretBinary, true);
        $offset = ord($hash[19]) & 0xf;
        $binary = (
            ((ord($hash[$offset]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        );

        $otp = $binary % (10 ** self::DEFAULT_DIGITS);
        return str_pad((string) $otp, self::DEFAULT_DIGITS, '0', STR_PAD_LEFT);
    }

    /**
     * Encoding Base32 (RFC 4648)
     */
    private static function base32Encode(string $data): string {
        $alphabet = self::BASE32_ALPHABET;
        $binaryString = '';
        foreach (str_split($data) as $char) {
            $binaryString .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }

        $chunks = str_split($binaryString, 5);
        $base32 = '';
        foreach ($chunks as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $index = bindec($chunk);
            $base32 .= $alphabet[$index];
        }

        return $base32;
    }

    /**
     * Decoding Base32. Restituisce null se input non valido
     */
    private static function base32Decode(string $base32): ?string {
        $alphabet = self::BASE32_ALPHABET;
        $base32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $base32));
        if ($base32 === '') {
            return null;
        }

        $binaryString = '';
        foreach (str_split($base32) as $char) {
            $index = strpos($alphabet, $char);
            if ($index === false) {
                return null;
            }
            $binaryString .= str_pad(decbin($index), 5, '0', STR_PAD_LEFT);
        }

        $bytes = [];
        $chunks = str_split($binaryString, 8);
        foreach ($chunks as $chunk) {
            if (strlen($chunk) === 8) {
                $bytes[] = chr(bindec($chunk));
            }
        }

        return implode('', $bytes);
    }

    /**
     * Restituisce il codice TOTP corrente (utile per test/diagnostica)
     */
    public static function getCode(string $secret, ?int $timestamp = null): ?string {
        $secretBinary = self::base32Decode($secret);
        if ($secretBinary === null) {
            return null;
        }

        $timestamp = $timestamp ?? time();
        $timeSlice = (int) floor($timestamp / self::DEFAULT_PERIOD);

        return self::calculateTotp($secretBinary, $timeSlice);
    }
}
?>

