<?php
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, nama, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    } else {
        $error = "Username atau password salah.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk</title>
  <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/vendors/bootstrap-icons/bootstrap-icons.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    body { min-height: 100vh; display: flex; align-items: center; justify-content: center; background: var(--color-bg-secondary, #f4f6fb); }
    .login-card { width: 100%; max-width: 420px; padding: 2.5rem 2rem; background: var(--color-bg-primary, #fff); border-radius: 1rem; box-shadow: 0 4px 32px rgba(0,0,0,.08); }
    .login-logo { display: flex; align-items: center; gap: .75rem; justify-content: center; margin-bottom: 1.5rem; }
    .login-logo-icon img { width: 64px; height: 64px; border-radius: .75rem; }    
    .login-logo-icon img { width: 64px; height: 64px; border-radius: .75rem; object-fit: contain; }
    .login-logo-sub { font-size: .75rem; color: var(--color-text-muted, #6b7280); }
    .login-heading .login-text { text-align: center; margin-bottom: 1.75rem; font-size: 30px; font-weight: 700; color: var(--color-text-primary, #111); }
    .login-heading h1 { font-size: 1.25rem; font-weight: 700; margin-bottom: .25rem; }
    .login-heading p { font-size: .875rem; color: var(--color-text-muted, #6b7280); margin: 0; }
    .login-footer-text { text-align: center; margin-top: 1.5rem; font-size: .8rem; color: var(--color-text-muted, #6b7280); }
    .login-text { font-size: 30px; }
  </style>
</head>
<body>
  <div class="login-card">

    <div class="login-logo">
      <div class="login-logo-icon">
        <img src="../assets/images/logos/hospital.png" alt="">
      </div>
      <div>
      </div>
    </div>



    <?php if ($error): ?>
      <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="login.php" method="post" novalidate>
      <div class="mb-3">
        <label class="form-label" for="username">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" class="form-control" id="username" name="username" placeholder="Masukkan username" required autocomplete="username">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password" required autocomplete="current-password">
          <button class="btn btn-outline-secondary" type="button" onclick="
            var p=document.getElementById('password');
            p.type=p.type==='password'?'text':'password';
            this.querySelector('i').className=p.type==='password'?'bi bi-eye':'bi bi-eye-slash';
          "><i class="bi bi-eye"></i></button>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mt-2">
        Login <i class="bi bi-box-arrow-in-right me-1"></i>
      </button>
    </form>

  </div>
</body>
</html>