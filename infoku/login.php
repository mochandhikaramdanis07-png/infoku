<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

if (!empty($_SESSION['uid'])) {
    header('Location: index.php');
    exit;
}

$err = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = strtolower(trim((string)($_POST['username'] ?? '')));
    $password = (string)($_POST['password'] ?? '');

    if (!csrf_ok($_POST['csrf'] ?? null)) {
        $err = 'Sesi formulir kedaluwarsa. Muat ulang halaman lalu coba lagi.';
    } elseif (($_SESSION['fail'] ?? 0) >= 5 && time() < ($_SESSION['lock'] ?? 0)) {
        $err = 'Terlalu banyak percobaan. Coba lagi beberapa menit lagi.';
    } elseif ($username === '' || $password === '') {
        $err = 'Isi username dan kata sandi.';
    } else {
        $st = db()->prepare('SELECT id, password_hash, role FROM users WHERE username = ?');
        $st->execute([$username]);
        $u = $st->fetch();
        // Verifikasi tetap dijalankan walau username tidak ada, supaya waktu responsnya serupa.
        $hash = $u['password_hash'] ?? password_hash('dummy', PASSWORD_DEFAULT);
        if (password_verify($password, $hash) && $u) {
            if ($u['role'] !== 'siswa') {
                $err = 'Ini akun guru. Masuk lewat halaman guru.';
            } else {
                session_regenerate_id(true);
                $_SESSION['uid'] = (int)$u['id'];
                unset($_SESSION['fail'], $_SESSION['lock']);
                db()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$u['id']]);
                header('Location: index.php');
                exit;
            }
        } else {
            $_SESSION['fail'] = ($_SESSION['fail'] ?? 0) + 1;
            if ($_SESSION['fail'] >= 5) {
                $_SESSION['lock'] = time() + 300;
            }
            $err = 'Username atau kata sandi salah.';
        }
    }
}

auth_head('Masuk');
?>
    <h2 style="margin-top:0">Masuk</h2>
    <?php if ($err): ?><p class="fb bad" role="alert"><?= h($err) ?></p><?php endif; ?>
    <form method="post" class="f" autocomplete="on">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <div class="fld">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" value="<?= h($username) ?>" autocomplete="username" autocapitalize="off" required autofocus>
      </div>
      <div class="fld">
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
      </div>
      <button class="btn" type="submit">Masuk</button>
    </form>
    <p class="small" style="margin:16px 0 0">Belum punya akun? <a href="register.php">Daftar di sini</a>.</p>
    <p class="small" style="margin:8px 0 0">Guru? <a href="guru/login.php">Masuk sebagai guru</a>.</p>
<?php auth_foot();
