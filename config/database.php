<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi dan Koneksi Database
|--------------------------------------------------------------------------
| Password database dan encryption key dibaca dari database.local.php.
| File database.local.php tidak boleh dimasukkan ke GitHub.
*/

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$configFile = __DIR__ . '/database.local.php';

if (!is_file($configFile)) {
    http_response_code(500);
    exit('Konfigurasi aplikasi belum tersedia.');
}

$config = require $configFile;

$requiredConfig = [
    'host',
    'port',
    'username',
    'password',
    'database',
    'encryption_key'
];

foreach ($requiredConfig as $key) {
    if (!array_key_exists($key, $config)) {
        error_log("Konfigurasi '{$key}' belum tersedia.");

        http_response_code(500);
        exit('Konfigurasi aplikasi tidak lengkap.');
    }
}

/*
|--------------------------------------------------------------------------
| Validasi Encryption Key
|--------------------------------------------------------------------------
| Encryption key harus berupa 64 karakter hexadecimal atau 32 byte.
*/

$encryptionKeyHex = trim((string) $config['encryption_key']);

if (!preg_match('/\A[a-f0-9]{64}\z/i', $encryptionKeyHex)) {
    error_log('Encryption key harus terdiri dari 64 karakter hexadecimal.');

    http_response_code(500);
    exit('Konfigurasi keamanan aplikasi tidak valid.');
}

$masterKey = hex2bin($encryptionKeyHex);

if ($masterKey === false || strlen($masterKey) !== 32) {
    error_log('Encryption key gagal dikonversi.');

    http_response_code(500);
    exit('Konfigurasi keamanan aplikasi tidak valid.');
}

define('ENCRYPTION_KEY', $masterKey);

/*
|--------------------------------------------------------------------------
| Membuat Koneksi Database
|--------------------------------------------------------------------------
*/

try {
    $conn = new mysqli(
        (string) $config['host'],
        (string) $config['username'],
        (string) $config['password'],
        (string) $config['database'],
        (int) $config['port']
    );

    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $exception) {
    /*
     * Detail error hanya dicatat pada log server.
     * Detail database tidak ditampilkan kepada pengguna.
     */
    error_log(
        'Koneksi database gagal: ' .
        $exception->getMessage()
    );

    http_response_code(500);
    exit('Koneksi database gagal.');
}

/*
|--------------------------------------------------------------------------
| Membuat Key Enkripsi dan Key Autentikasi Terpisah
|--------------------------------------------------------------------------
*/

function getNIKEncryptionKeys(): array
{
    $encryptionKey = hash_hmac(
        'sha256',
        'meditrust-nik-encryption-v1',
        ENCRYPTION_KEY,
        true
    );

    $authenticationKey = hash_hmac(
        'sha256',
        'meditrust-nik-authentication-v1',
        ENCRYPTION_KEY,
        true
    );

    return [
        'encryption' => $encryptionKey,
        'authentication' => $authenticationKey
    ];
}

/*
|--------------------------------------------------------------------------
| Enkripsi NIK
|--------------------------------------------------------------------------
| Format data:
| MT1 + IV + ciphertext + HMAC
*/

function encryptNIK($plaintext): string
{
    if ($plaintext === null || $plaintext === '') {
        return '';
    }

    $plaintext = (string) $plaintext;
    $cipher = 'aes-256-cbc';

    $ivLength = openssl_cipher_iv_length($cipher);

    if ($ivLength === false) {
        throw new RuntimeException(
            'Algoritma enkripsi tidak tersedia.'
        );
    }

    /*
     * Membuat IV acak yang aman untuk kebutuhan kriptografi.
     */
    $iv = random_bytes($ivLength);

    $keys = getNIKEncryptionKeys();

    $ciphertextRaw = openssl_encrypt(
        $plaintext,
        $cipher,
        $keys['encryption'],
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($ciphertextRaw === false) {
        throw new RuntimeException(
            'Proses enkripsi NIK gagal.'
        );
    }

    /*
     * Penanda versi digunakan agar format data dapat dikenali.
     */
    $version = 'MT1';

    /*
     * IV ikut dilindungi oleh HMAC.
     */
    $authenticatedData = $version . $iv . $ciphertextRaw;

    $hmac = hash_hmac(
        'sha256',
        $authenticatedData,
        $keys['authentication'],
        true
    );

    return base64_encode(
        $authenticatedData . $hmac
    );
}

/*
|--------------------------------------------------------------------------
| Dekripsi NIK
|--------------------------------------------------------------------------
*/

function decryptNIK($ciphertext): string
{
    if ($ciphertext === null || $ciphertext === '') {
        return '';
    }

    $cipher = 'aes-256-cbc';
    $hmacLength = 32;
    $versionLength = 3;

    $ivLength = openssl_cipher_iv_length($cipher);

    if ($ivLength === false) {
        return '[Gagal Dekripsi / Algoritma Tidak Tersedia]';
    }

    /*
     * Parameter true membuat base64_decode menolak format
     * Base64 yang tidak valid.
     */
    $decodedData = base64_decode(
        (string) $ciphertext,
        true
    );

    if ($decodedData === false) {
        return '[Gagal Dekripsi / Format Data Tidak Valid]';
    }

    $minimumLength =
        $versionLength +
        $ivLength +
        $hmacLength +
        1;

    if (strlen($decodedData) < $minimumLength) {
        return '[Gagal Dekripsi / Data Tidak Lengkap]';
    }

    $version = substr(
        $decodedData,
        0,
        $versionLength
    );

    if ($version !== 'MT1') {
        return '[Gagal Dekripsi / Versi Data Tidak Didukung]';
    }

    $iv = substr(
        $decodedData,
        $versionLength,
        $ivLength
    );

    $receivedHmac = substr(
        $decodedData,
        -$hmacLength
    );

    $ciphertextLength =
        strlen($decodedData) -
        $versionLength -
        $ivLength -
        $hmacLength;

    if ($ciphertextLength <= 0) {
        return '[Gagal Dekripsi / Data Tidak Lengkap]';
    }

    $ciphertextRaw = substr(
        $decodedData,
        $versionLength + $ivLength,
        $ciphertextLength
    );

    $authenticatedData = substr(
        $decodedData,
        0,
        -$hmacLength
    );

    $keys = getNIKEncryptionKeys();

    $calculatedHmac = hash_hmac(
        'sha256',
        $authenticatedData,
        $keys['authentication'],
        true
    );

    /*
     * Periksa HMAC sebelum melakukan dekripsi.
     */
    if (!hash_equals($receivedHmac, $calculatedHmac)) {
        return '[Gagal Dekripsi / Data Rusak atau Diubah]';
    }

    $plaintext = openssl_decrypt(
        $ciphertextRaw,
        $cipher,
        $keys['encryption'],
        OPENSSL_RAW_DATA,
        $iv
    );

    if ($plaintext === false) {
        return '[Gagal Dekripsi / Kunci Tidak Sesuai]';
    }

    return $plaintext;
}
