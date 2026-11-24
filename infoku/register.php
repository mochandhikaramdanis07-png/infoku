<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

if (!empty($_SESSION['uid'])) {
    header('Location: index.php');
    exit;
}

$err = '';
$v = ['nama' => '', 'username' => '', 'kelas' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['nama']     = trim((string)($_POST['nama'] ?? ''));
    $v['username'] = strtolower(trim((string)($_POST['username'] ?? '')));
    $v['kelas']    = trim((string)($_POST['kelas'] ?? ''));
    $pw  = (string)($_POST['password'] ?? '');
    $pw2 = (string)($_POST['password2'] ?? '');

    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $err = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (mb_strlen($v['nama']) < 2 || mb_strlen($v['nama']) > 100) {
        $err = 'Nama lengkap harus 2 sampai 100 karakter.';
    } elseif (!preg_match('/^[a-z0-9_.]{3,30}$/', $v['username'])) {
        $err = 'Username 3 sampai 30 karakter, hanya huruf kecil, angka, titik, atau garis bawah.';
    } elseif (mb_strlen($v['kelas']) > 30) {
        $err = 'Kelas maksimal 30 karakter.';
    } elseif (strlen($pw) < 8) {
        $err = 'Kata sandi minimal 8 karakter.';
    } elseif ($pw !== $pw2) {
        $err = 'Konfirmasi kata sandi tidak sama.';
    } else {
        try {
            $st = db()->prepare('INSERT INTO users (nama, username, kelas, password_hash) VALUES (?, ?, ?, ?)');
            $st->execute([
                $v['nama'],
                $v['username'],
                $v['kelas'] !== '' ? $v['kelas'] : null,
                password_hash($pw, PASSWORD_DEFAULT),
            ]);
            session_regenerate_id(true);
            $_SESSION['uid'] = (int)db()->lastInsertId();
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $err = $e->getCode() === '23000'
                ? 'Username sudah dipakai. Pilih username lain.'
                : 'Pendaftaran gagal. Coba lagi.';
        }
    }
}

auth_head('Daftar');
?>
    <h2 style="margin-top:0">Buat akun</h2>
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
        <span class="hintx">Huruf kecil, angka, titik, atau garis bawah.</span>
      </div>
      <div class="fld">
        <label for="kelas">Kelas (opsional)</label>
        <input id="kelas" name="kelas" type="text" value="<?= h($v['kelas']) ?>" maxlength="30" placeholder="contoh: X DKV 1">
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
      <button class="btn" type="submit">Daftar</button>
    </form>
    <p class="small" style="margin:16px 0 0">Sudah punya akun? <a href="login.php">Masuk</a>.</p>
<?php auth_foot();
