<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';

if (!empty($_SESSION['uid'])) {
    header('Location: index.php');
    exit;
}

$kodeBenar = (string)(config()['kode_guru'] ?? '');
$err = '';
$v = ['nama' => '', 'username' => ''];

if ($kodeBenar === '') {
    $err = 'Pendaftaran guru belum diaktifkan. Isi kode_guru di config.php terlebih dahulu.';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nama']     = trim((string)($_POST['nama'] ?? ''));
    $v['username'] = strtolower(trim((string)($_POST['username'] ?? '')));
    $pw   = (string)($_POST['password'] ?? '');
    $pw2  = (string)($_POST['password2'] ?? '');
    $kode = trim((string)($_POST['kode'] ?? ''));

    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $err = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (($_SESSION['kfail'] ?? 0) >= 5 && time() < ($_SESSION['klock'] ?? 0)) {
        $err = 'Terlalu banyak percobaan kode. Coba lagi beberapa menit lagi.';
    } elseif (!hash_equals($kodeBenar, $kode)) {
        $_SESSION['kfail'] = ($_SESSION['kfail'] ?? 0) + 1;
        if ($_SESSION['kfail'] >= 5) {
            $_SESSION['klock'] = time() + 300;
        }
        $err = 'Kode guru salah.';
    } elseif (mb_strlen($v['nama']) < 2 || mb_strlen($v['nama']) > 100) {
        $err = 'Nama lengkap harus 2 sampai 100 karakter.';
    } elseif (!preg_match('/^[a-z0-9_.]{3,30}$/', $v['username'])) {
        $err = 'Username 3 sampai 30 karakter, hanya huruf kecil, angka, titik, atau garis bawah. Spasi tidak boleh.';
    } elseif (strlen($pw) < 8) {
        $err = 'Kata sandi minimal 8 karakter.';
    } elseif ($pw !== $pw2) {
        $err = 'Konfirmasi kata sandi tidak sama.';
    } else {
        try {
            $st = db()->prepare("INSERT INTO users (nama, username, kelas, password_hash, role) VALUES (?, ?, NULL, ?, 'guru')");
            $st->execute([$v['nama'], $v['username'], password_hash($pw, PASSWORD_DEFAULT)]);
            session_regenerate_id(true);
            $_SESSION['uid'] = (int)db()->lastInsertId();
            unset($_SESSION['kfail'], $_SESSION['klock']);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $err = $e->getCode() === '23000'
                ? 'Username sudah dipakai. Pilih username lain.'
                : 'Pendaftaran gagal. Coba lagi.';
        }
    }
}

guru_auth_head('Daftar Guru');
?>
    <span class="tag">Guru</span>
    <h2 style="margin-top:0">Buat akun guru</h2>
    <?php if ($err): ?><p class="fb bad" role="alert"><?= h($err) ?></p><?php endif; ?>
    <form method="post" class="f" autocomplete="on">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <div class="fld">
        <label for="nama">Nama lengkap</label>
        <input id="nama" name="nama" type="text" value="<?= h($v['nama']) ?>" autocomplete="name" maxlength="100" required autofocus>
      </div>
      <div class="fld">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?= h($v['username']) ?>" autocomplete="username" autocapitalize="off" maxlength="30" required>
        <span class="hintx">Huruf kecil, angka, titik, atau garis bawah. Tanpa spasi.</span>
      </div>
      <div class="fld">
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="new-password" minlength="8" required>
        <span class="hintx">Minimal 8 karakter.</span>
      </div>
      <div class="fld">
        <label for="password2">Ulangi kata sandi</label>
        <input id="password2" name="password2" type="password" autocomplete="new-password" minlength="8" required>
      </div>
      <div class="fld">
        <label for="kode">Kode guru</label>
        <input id="kode" name="kode" type="password" autocomplete="off" required>
        <span class="hintx">Kode ada di berkas config.php (kode_guru). Hanya guru yang boleh tahu.</span>
      </div>
      <button class="btn" type="submit">Daftar</button>
    </form>
    <p class="small" style="margin:16px 0 0">Sudah punya akun? <a href="login.php">Masuk guru</a>.</p>
<?php guru_auth_foot();
