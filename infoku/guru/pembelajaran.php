<?php
declare(strict_types=1);
require __DIR__ . '/_layout.php';
require_login('guru');
header('Location: ai_materi.php');
exit;
