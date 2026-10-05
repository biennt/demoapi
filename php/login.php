<?php
declare(strict_types=1);

/**
 * Simple JWT issuer for demonstration purposes.
 */

function base64UrlEncode(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

$token = null;
$error = null;

$roles = ["user", "admin", "readonly"];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $header = base64UrlEncode(json_encode([
            'alg' => 'HS256',
            'typ' => 'JWT'
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        $issuedAt = time();
        $payload = base64UrlEncode(json_encode([
            'sub' => $username,
            'iat' => $issuedAt,
            'role' => $roles[rand(0,2)],
            'exp' => $issuedAt + 900
        ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));

        $secret = 'very_long_long_secret';
        $signature = base64UrlEncode(hash_hmac('sha256', $header . '.' . $payload, $secret, true));

        $token = $header . '.' . $payload . '.' . $signature;
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        setcookie('jwt', $token, [
            'expires' => $issuedAt + 900,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JWT Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      color-scheme: light;
      --bg-gradient: radial-gradient(circle at 20% 20%, #1a2a6c 0%, #2a3291 40%, #1e90ff 100%);
      --panel-bg: rgba(255, 255, 255, 0.94);
      --panel-border: rgba(32, 47, 119, 0.15);
      --text-primary: #212b53;
      --text-muted: rgba(33, 43, 83, 0.72);
      --accent: #ff7c6b;
      --accent-dark: #e96250;
      --input-bg: rgba(255, 255, 255, 0.86);
      --input-border: rgba(33, 43, 83, 0.18);
      --error-bg: rgba(255, 124, 107, 0.18);
      --error-text: #b53b2d;
      --success-bg: rgba(63, 219, 173, 0.16);
      --success-text: #136956;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: clamp(2rem, 6vw, 4rem);
      background: var(--bg-gradient);
      font-family: "Space Grotesk", "Segoe UI", sans-serif;
      color: var(--text-primary);
    }

    main {
      width: min(480px, 100%);
      background: var(--panel-bg);
      border: 1px solid var(--panel-border);
      border-radius: 22px;
      padding: clamp(2rem, 5vw, 3rem);
      box-shadow: 0 28px 60px rgba(20, 30, 80, 0.35);
      display: grid;
      gap: 1.5rem;
    }

    header h1 {
      margin: 0;
      font-size: clamp(2rem, 5vw, 2.35rem);
      letter-spacing: -0.02em;
    }

    header p {
      margin: 0.5rem 0 0;
      color: var(--text-muted);
      font-size: 1rem;
    }

    form {
      display: grid;
      gap: 1.2rem;
    }

    label {
      display: grid;
      gap: 0.5rem;
      font-size: 0.95rem;
      color: var(--text-muted);
    }

    input[type="text"],
    input[type="password"] {
      width: 100%;
      padding: 0.75rem 1rem;
      border-radius: 14px;
      border: 1px solid var(--input-border);
      background: var(--input-bg);
      color: var(--text-primary);
      font-size: 1rem;
      outline: none;
      transition: border-color 140ms ease, box-shadow 140ms ease;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      border-color: rgba(255, 124, 107, 0.55);
      box-shadow: 0 0 0 3px rgba(255, 124, 107, 0.18);
    }

    button {
      width: 100%;
      padding: 0.85rem 1.4rem;
      border-radius: 12px;
      border: none;
      font-size: 1rem;
      font-weight: 600;
      letter-spacing: 0.035em;
      background: var(--accent);
      color: #fff;
      cursor: pointer;
      transition: transform 160ms ease, background 160ms ease, box-shadow 160ms ease;
      box-shadow: 0 16px 30px rgba(255, 124, 107, 0.32);
    }

    button:hover,
    button:focus-visible {
      transform: translateY(-2px);
      background: var(--accent-dark);
      box-shadow: 0 20px 36px rgba(255, 124, 107, 0.34);
      outline: none;
    }

    .alert {
      padding: 0.9rem 1rem;
      border-radius: 14px;
      font-size: 0.95rem;
      line-height: 1.4;
    }

    .alert.error {
      background: var(--error-bg);
      color: var(--error-text);
      border: 1px solid rgba(255, 124, 107, 0.35);
    }

    .alert.success {
      background: var(--success-bg);
      color: var(--success-text);
      border: 1px solid rgba(19, 105, 86, 0.2);
    }

    textarea {
      width: 100%;
      min-height: 120px;
      border-radius: 16px;
      border: 1px solid var(--input-border);
      background: rgba(33, 43, 83, 0.04);
      color: var(--text-primary);
      padding: 1rem 1.1rem;
      font-family: "Space Grotesk", monospace;
      letter-spacing: 0.04em;
      font-size: 0.95rem;
      resize: none;
    }

    @media (max-width: 520px) {
      body {
        padding: clamp(1.5rem, 10vw, 2.5rem);
      }
      main {
        padding: clamp(1.75rem, 8vw, 2.25rem);
      }
    }

    @media (prefers-reduced-motion: reduce) {
      * {
        animation-duration: 1ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 1ms !important;
        scroll-behavior: auto !important;
      }
    }
  </style>
</head>
<body>
  <main>
    <header>
      <h1>Authenticate &amp; Issue JWT</h1>
      <p>Enter credentials to receive a signed JSON Web Token. Demo credentials: `test` / `test`.</p>
    </header>

    <?php if ($error !== null) { ?>
      <div class="alert error"><?php echo htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></div>
    <?php } elseif ($token !== null) { ?>
      <div class="alert success">Authenticated successfully. Copy your JWT below.</div>
    <?php } ?>

    <form method="post" novalidate>
      <label>
        Username
        <input type="text" name="username" placeholder="Enter username" value="<?php echo isset($username) ? htmlspecialchars($username, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : ''; ?>" required>
      </label>
      <label>
        Password
        <input type="password" name="password" placeholder="Enter password" required>
      </label>
      <button type="submit">Log In</button>
    </form>

    <section>
      <textarea readonly placeholder="JWT will appear here."><?php echo $token !== null ? htmlspecialchars($token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : ''; ?></textarea>
    </section>
    <section>
      <a href="access.php" style="display: inline-block; margin-top: 1rem; text-decoration: none; color: var(--accent); font-weight: 500;">Go to API Explorer</a>
    </section>
  </main>
</body>
</html>
