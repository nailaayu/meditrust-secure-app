<?php
// KONEKSI DATABASE (Disesuaikan untuk database 'meditrust' di Laragon)
$host = 'localhost';
$user = 'root';
$pass = ''; 
$db   = 'meditrust'; 

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// STRATEGI PERLINDUNGAN DATA SENSITIF: Kriptografi AES-256-CBC + HMAC
define('ENCRYPTION_KEY', 'MediTrustSecretKey2026!@#'); 

function encryptNIK($plaintext) {
    if (empty($plaintext)) return '';
    $cipher = "aes-256-cbc";
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext_raw = openssl_encrypt($plaintext, $cipher, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);
    $hmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, true);
    return base64_encode($iv . $hmac . $ciphertext_raw);
}

function decryptNIK($ciphertext) {
    if (empty($ciphertext)) return '';
    $cipher = "aes-256-cbc";
    $c = base64_decode($ciphertext);
    $ivlen = openssl_cipher_iv_length($cipher);
    $iv = substr($c, 0, $ivlen);
    $hmac = substr($c, $ivlen, 32);
    $ciphertext_raw = substr($c, $ivlen + 32);
    $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, ENCRYPTION_KEY, OPENSSL_RAW_DATA, $iv);
    $calcmac = hash_hmac('sha256', $ciphertext_raw, ENCRYPTION_KEY, true);
    if (hash_equals($hmac, $calcmac)) {
        return $original_plaintext;
    }
    return "[Gagal Dekripsi / Data Rusak]";
}
?>