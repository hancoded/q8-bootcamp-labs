// =============================================================================
// Vercel Edge Middleware — Custom branded login page (cookie session)
// =============================================================================
// Replaces the browser's native Basic Auth popup with an in-page login form.
// Once a student enters the right creds, a 30-day cookie keeps them in.
//
// REQUIRED Vercel environment variables:
//   AUTH_USER  — username (e.g. "bootcamp")
//   AUTH_PASS  — password
// =============================================================================

export const config = {
  matcher: '/((?!favicon\\.ico|_next|_vercel).*)',
};

const COOKIE_NAME = 'q8_session';
const COOKIE_MAX_AGE = 60 * 60 * 24 * 30; // 30 days

export default async function middleware(request: Request): Promise<Response | undefined> {
  const user = process.env.AUTH_USER;
  const pass = process.env.AUTH_PASS;

  if (!user || !pass) {
    return new Response(
      'Authentication is not configured. Set AUTH_USER and AUTH_PASS in Vercel project settings.',
      { status: 503, headers: { 'Content-Type': 'text/plain; charset=utf-8' } }
    );
  }

  const expectedCookie = btoa(`${user}:${pass}`);
  const cookieHeader = request.headers.get('cookie') || '';
  const hasValidCookie = cookieHeader
    .split(';')
    .some((c) => c.trim() === `${COOKIE_NAME}=${expectedCookie}`);

  if (hasValidCookie) {
    return; // pass through to static asset
  }

  // Login form POST
  if (request.method === 'POST') {
    const contentType = request.headers.get('content-type') || '';
    if (
      contentType.includes('application/x-www-form-urlencoded') ||
      contentType.includes('multipart/form-data')
    ) {
      try {
        const form = await request.formData();
        const submittedUser = form.get('username');
        const submittedPass = form.get('password');

        if (submittedUser === user && submittedPass === pass) {
          const url = new URL(request.url);
          const target = url.pathname && url.pathname !== '/' ? url.pathname : '/';
          return new Response(null, {
            status: 303,
            headers: {
              'Set-Cookie': `${COOKIE_NAME}=${expectedCookie}; Path=/; Max-Age=${COOKIE_MAX_AGE}; Secure; HttpOnly; SameSite=Lax`,
              Location: target,
            },
          });
        }
        return loginPage('Invalid username or password.');
      } catch {
        return loginPage('Login failed. Try again.');
      }
    }
  }

  return loginPage(null);
}

function loginPage(errorMsg: string | null): Response {
  const safeError = errorMsg ? escapeHtml(errorMsg) : '';
  const errorBlock = errorMsg ? `<div class="error" role="alert">${safeError}</div>` : '';

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <meta name="robots" content="noindex,nofollow" />
  <title>Bootcamp Lab Access</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html,body{height:100%}
    body{
      font-family:'Manrope',system-ui,-apple-system,sans-serif;
      background:#0a1628;color:#e8eef5;min-height:100vh;
      display:flex;align-items:center;justify-content:center;padding:24px;
      background-image:
        radial-gradient(ellipse at top left,rgba(245,158,11,0.08) 0%,transparent 50%),
        radial-gradient(ellipse at bottom right,rgba(13,41,73,0.6) 0%,transparent 50%),
        linear-gradient(180deg,#0a1628 0%,#050b14 100%);
      overflow:hidden;
    }
    body::before{
      content:'';position:fixed;inset:0;
      background-image:
        linear-gradient(rgba(245,158,11,0.04) 1px,transparent 1px),
        linear-gradient(90deg,rgba(245,158,11,0.04) 1px,transparent 1px);
      background-size:60px 60px;pointer-events:none;
    }
    .card{
      position:relative;width:100%;max-width:440px;
      background:linear-gradient(180deg,rgba(20,32,52,0.95) 0%,rgba(13,22,40,0.95) 100%);
      border:1px solid rgba(245,158,11,0.15);
      border-radius:16px;padding:40px 36px;
      box-shadow:0 20px 60px rgba(0,0,0,0.5),0 0 80px rgba(245,158,11,0.08),inset 0 1px 0 rgba(255,255,255,0.05);
      backdrop-filter:blur(20px);
    }
    .badge{
      display:inline-flex;align-items:center;gap:6px;
      font-family:'JetBrains Mono',monospace;font-size:11px;
      letter-spacing:0.12em;text-transform:uppercase;color:#f59e0b;
      background:rgba(245,158,11,0.1);padding:6px 10px;
      border-radius:6px;border:1px solid rgba(245,158,11,0.2);
      margin-bottom:20px;
    }
    .badge::before{
      content:'';width:6px;height:6px;border-radius:50%;
      background:#f59e0b;box-shadow:0 0 8px #f59e0b;
    }
    h1{font-size:26px;font-weight:700;letter-spacing:-0.02em;color:#f8fafc;margin-bottom:8px;line-height:1.2}
    h1 span{color:#f59e0b}
    .subtitle{font-size:14px;color:#94a3b8;line-height:1.5;margin-bottom:28px}
    form{display:flex;flex-direction:column;gap:16px}
    .field{display:flex;flex-direction:column;gap:6px}
    label{font-size:12px;font-weight:600;color:#cbd5e1;letter-spacing:0.04em;text-transform:uppercase}
    input[type='text'],input[type='password']{
      width:100%;background:rgba(10,22,40,0.7);
      border:1px solid rgba(148,163,184,0.2);border-radius:8px;
      padding:13px 14px;font-family:'JetBrains Mono',monospace;
      font-size:14px;color:#f8fafc;
      transition:border-color 0.15s,background 0.15s,box-shadow 0.15s;
    }
    input[type='text']:focus,input[type='password']:focus{
      outline:none;border-color:#f59e0b;background:rgba(10,22,40,0.95);
      box-shadow:0 0 0 3px rgba(245,158,11,0.15);
    }
    input::placeholder{color:#475569}
    button{
      margin-top:8px;width:100%;padding:14px;
      font-family:'Manrope',sans-serif;font-size:15px;font-weight:700;
      letter-spacing:0.02em;color:#0a1628;
      background:linear-gradient(180deg,#fbbf24 0%,#f59e0b 100%);
      border:1px solid rgba(0,0,0,0.2);border-radius:8px;cursor:pointer;
      transition:transform 0.1s,box-shadow 0.15s,filter 0.15s;
      box-shadow:0 4px 16px rgba(245,158,11,0.3);
    }
    button:hover{filter:brightness(1.08);box-shadow:0 6px 20px rgba(245,158,11,0.45)}
    button:active{transform:translateY(1px)}
    .error{
      background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.3);
      color:#fecaca;font-size:13px;padding:11px 14px;
      border-radius:8px;margin-bottom:16px;
      display:flex;align-items:center;gap:10px;
    }
    .error::before{
      content:'!';font-family:'JetBrains Mono',monospace;font-weight:700;
      width:20px;height:20px;border-radius:50%;
      background:rgba(239,68,68,0.4);color:#fff;
      display:flex;align-items:center;justify-content:center;
      font-size:12px;flex-shrink:0;
    }
    .footer{
      margin-top:28px;padding-top:20px;
      border-top:1px solid rgba(148,163,184,0.1);
      font-size:12px;color:#64748b;text-align:center;line-height:1.6;
    }
    .footer code{
      font-family:'JetBrains Mono',monospace;color:#94a3b8;
      background:rgba(148,163,184,0.08);padding:1px 6px;border-radius:4px;
    }
  </style>
</head>
<body>
  <main class="card">
    <div class="badge">restricted access</div>
    <h1>Bootcamp Lab <span>Access</span></h1>
    <p class="subtitle">
      This environment is restricted to enrolled students of the
      <strong>CODED Academy Ethical Hacking Bootcamp</strong>. Use the credentials
      provided by your instructor.
    </p>
    ${errorBlock}
    <form method="POST" action="" autocomplete="on">
      <div class="field">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" required autofocus
               autocomplete="username" autocapitalize="off" autocorrect="off"
               spellcheck="false" placeholder="bootcamp" />
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required
               autocomplete="current-password" placeholder="********" />
      </div>
      <button type="submit">Sign In</button>
    </form>
    <div class="footer">
      Lost your credentials? Contact your <code>CODED</code> instructor.
    </div>
  </main>
</body>
</html>`;

  return new Response(html, {
    status: errorMsg ? 401 : 200,
    headers: {
      'Content-Type': 'text/html; charset=utf-8',
      'Cache-Control': 'no-store',
    },
  });
}

function escapeHtml(s: string): string {
  return s
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');
}
