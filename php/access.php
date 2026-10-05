<?php
declare(strict_types=1);

$hostname = $_SERVER["HTTP_HOST"];
$scheme = $_SERVER['REQUEST_SCHEME'] . '://';

$defaultUrl = $scheme . $hostname . '/json/users';

$methods = ['GET', 'POST', 'PUT', 'DELETE'];
$jwt = trim((string)($_COOKIE['jwt'] ?? ''));

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>JWT API Access</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      color-scheme: light;
      --bg-gradient: radial-gradient(120% 120% at 20% 10%, #101422 0%, #1e2950 35%, #364b9c 75%, #4e6be2 100%);
      --panel-bg: rgba(255, 255, 255, 0.96);
      --panel-border: rgba(34, 40, 86, 0.18);
      --primary-text: #242d49;
      --muted-text: rgba(36, 45, 73, 0.7);
      --accent: #5ef9d6;
      --accent-dark: #32c3a4;
      --input-bg: rgba(255, 255, 255, 0.84);
      --input-border: rgba(26, 33, 74, 0.18);
      --input-focus: rgba(94, 249, 214, 0.32);
      --error-bg: rgba(255, 118, 136, 0.16);
      --error-text: #b5445b;
      --code-bg: rgba(16, 20, 34, 0.78);
      --code-text: #f3f6ff;
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
      padding: clamp(2rem, 6vw, 4.5rem);
      background: var(--bg-gradient);
      font-family: "Space Grotesk", "Segoe UI", sans-serif;
      color: var(--primary-text);
    }

    main {
      width: min(960px, 100%);
      background: var(--panel-bg);
      border: 1px solid var(--panel-border);
      border-radius: 30px;
      padding: clamp(2.4rem, 6vw, 3.6rem);
      box-shadow: 0 36px 70px rgba(18, 24, 54, 0.32);
      display: grid;
      gap: 2.2rem;
    }

    header {
      display: grid;
      gap: 0.8rem;
    }

    header h1 {
      margin: 0;
      font-size: clamp(2.1rem, 5vw, 2.7rem);
      letter-spacing: -0.02em;
    }

    header p {
      margin: 0;
      color: var(--muted-text);
      font-size: 1.05rem;
    }

    form {
      display: grid;
      gap: 1.5rem;
    }

    .field {
      display: grid;
      gap: 0.6rem;
    }

    .field label {
      font-size: 0.95rem;
      color: var(--muted-text);
    }

    input[type="text"],
    select,
    textarea {
      width: 100%;
      border-radius: 16px;
      border: 1px solid var(--input-border);
      background: var(--input-bg);
      padding: 0.85rem 1rem;
      font: inherit;
      color: var(--primary-text);
      outline: none;
      transition: border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
    }

    input[type="text"]:focus,
    select:focus,
    textarea:focus {
      border-color: rgba(50, 195, 164, 0.65);
      box-shadow: 0 0 0 3px var(--input-focus);
      background: #fff;
    }

    textarea {
      min-height: 160px;
      resize: vertical;
      line-height: 1.4;
      letter-spacing: 0.02em;
    }

    button {
      justify-self: flex-start;
      padding: 0.95rem 2.6rem;
      border-radius: 999px;
      border: none;
      font-size: 1rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      background: var(--accent);
      color: #0d1733;
      cursor: pointer;
      transition: transform 160ms ease, box-shadow 160ms ease, background 160ms ease;
      box-shadow: 0 20px 34px rgba(94, 249, 214, 0.36);
    }

    button:hover,
    button:focus-visible {
      transform: translateY(-3px);
      background: var(--accent-dark);
      box-shadow: 0 26px 42px rgba(50, 195, 164, 0.42);
      outline: none;
    }

    .alert {
      padding: 1rem 1.2rem;
      border-radius: 16px;
      font-size: 0.95rem;
      line-height: 1.5;
    }

    .alert.error {
      background: var(--error-bg);
      color: var(--error-text);
      border: 1px solid rgba(181, 68, 91, 0.25);
    }

    .response {
      display: grid;
      gap: 1.1rem;
    }

    .response-section {
      display: grid;
      gap: 0.5rem;
    }

    .response-section h2 {
      margin: 0;
      font-size: 1rem;
      letter-spacing: 0.02em;
      text-transform: uppercase;
      color: rgba(36, 45, 73, 0.64);
    }

    pre {
      margin: 0;
      padding: 1rem 1.2rem;
      border-radius: 18px;
      background: var(--code-bg);
      color: var(--code-text);
      font: 0.92rem/1.5 "Space Grotesk", monospace;
      overflow-x: auto;
    }

    @media (max-width: 720px) {
      body {
        padding: clamp(1.5rem, 10vw, 2.5rem);
      }
      main {
        padding: clamp(1.8rem, 8vw, 2.6rem);
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
      <h1>JWT-Protected API Explorer</h1>
      <p>Send authenticated requests with your JSON Web Token. Adjust the endpoint, method, and payload as needed.</p>
    </header>

    <div id="alert" class="alert error" role="alert" hidden></div>

    <form id="api-form" novalidate>
      <div class="field">
        <label for="jwt">Bearer Token (JWT)</label>
        <input id="jwt" name="jwt" type="text" placeholder="eyJhbGciOi..." value="<?php echo e($jwt); ?>" required>
      </div>
      <div class="field">
        <label for="url">Request URL</label>
        <input id="url" name="url" type="text" value="<?php echo e($defaultUrl); ?>" required>
      </div>
      <div class="field">
        <label for="method">HTTP Method</label>
        <select id="method" name="method">
          <?php foreach ($methods as $option) { ?>
            <option value="<?php echo $option; ?>" <?php echo ($option === 'GET') ? 'selected' : ''; ?>><?php echo $option; ?></option>
          <?php } ?>
        </select>
      </div>
      <div class="field">
        <label for="payload">JSON Payload (optional)</label>
        <textarea id="payload" name="payload" placeholder="{
  &quot;name&quot;: &quot;Alice&quot;
}"></textarea>
      </div>
      <button type="submit">Send Request</button>
    </form>

    <section id="response" class="response" aria-live="polite" hidden>
      <div class="response-section">
        <h2>Status</h2>
        <pre id="response-status"></pre>
      </div>
      <div class="response-section">
        <h2>Headers</h2>
        <pre id="response-headers"></pre>
      </div>
      <div class="response-section">
        <h2>Body</h2>
        <pre id="response-body"></pre>
      </div>
    </section>
  </main>

  <script>
    (function () {
      const form = document.getElementById('api-form');
      if (!form) {
        return;
      }

      const alertBox = document.getElementById('alert');
      const responseWrap = document.getElementById('response');
      const statusField = document.getElementById('response-status');
      const headersField = document.getElementById('response-headers');
      const bodyField = document.getElementById('response-body');
      const jwtField = document.getElementById('jwt');
      const urlField = document.getElementById('url');
      const methodField = document.getElementById('method');
      const payloadField = document.getElementById('payload');
      const submitButton = form.querySelector('button[type="submit"]');

      function toggleAlert(message) {
        if (!message) {
          alertBox.hidden = true;
          alertBox.textContent = '';
          return;
        }

        alertBox.textContent = message;
        alertBox.hidden = false;
      }

      function showResponse(status, headers, body) {
        statusField.textContent = status || '(unknown)';
        headersField.textContent = headers ? headers : '(none)';
        bodyField.textContent = body ? body : '(empty)';
        responseWrap.hidden = false;
      }

      form.addEventListener('submit', async (event) => {
        event.preventDefault();

        toggleAlert('');
        responseWrap.hidden = true;

        const jwt = jwtField.value.trim();
        const url = urlField.value.trim();
        const method = methodField.value.toUpperCase();
        const payload = payloadField.value.trim();

        if (!jwt) {
          toggleAlert('JWT is required for authorization.');
          return;
        }

        let parsedUrl;
        try {
          parsedUrl = new URL(url);
        } catch (error) {
          toggleAlert('Enter a valid URL.');
          return;
        }

        const options = {
          method,
          headers: {
            'Authorization': `Bearer ${jwt}`,
            'Accept': 'application/json'
          }
        };

        if (payload) {
          let validatedPayload;
          try {
            JSON.parse(payload);
            validatedPayload = payload;
          } catch (error) {
            toggleAlert('Payload must be valid JSON.');
            return;
          }

          if (method === 'GET') {
            toggleAlert('GET requests cannot include a payload. Remove the JSON body or choose another method.');
            return;
          }

          options.body = validatedPayload;
          options.headers['Content-Type'] = 'application/json';
        }

        if (submitButton) {
          submitButton.disabled = true;
          submitButton.dataset.originalText = submitButton.textContent || '';
          submitButton.textContent = 'Sending...';
        }

        try {
          const response = await fetch(parsedUrl.toString(), options);
          const responseText = await response.text();
          const headerLines = [];
          response.headers.forEach((value, key) => {
            headerLines.push(`${key}: ${value}`);
          });

          let formattedBody = responseText;
          if (responseText) {
            try {
              const jsonData = JSON.parse(responseText);
              formattedBody = JSON.stringify(jsonData, null, 2);
            } catch (error) {
              // keep original text when not JSON
            }
          }

          const statusLine = `${response.status} ${response.statusText}`.trim();
          showResponse(statusLine, headerLines.join('\n'), formattedBody);
        } catch (error) {
          const message = error instanceof Error ? error.message : 'Unknown error';
          toggleAlert(`Request failed: ${message}.`);
        } finally {
          if (submitButton) {
            submitButton.textContent = submitButton.dataset.originalText || 'Send Request';
            submitButton.disabled = false;
            delete submitButton.dataset.originalText;
          }
        }
      });
    })();
  </script>
</body>
</html>

