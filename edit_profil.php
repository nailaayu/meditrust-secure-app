<?php
include 'config/database.php';
include 'includes/header.php';

$user_id = $_GET['id']; // Celah: Mahasiswa bisa mengubah ID di URL untuk akses data user lain

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    // Update data tanpa verifikasi kepemilikan ID
    $update = "UPDATE users SET username = '$new_username' WHERE id = $user_id";
    mysqli_query($conn, $update);
    echo "Profil berhasil diperbarui!";
}

$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<h2>Edit Profil Pengguna</h2>
<form method="POST">
    Username: <input type="text" name="username" value="<?php echo $data['username']; ?>">
    <button type="submit">Simpan</button>
</form>

<?php include 'includes/footer.php'; ?>