// =============================================================================
// Vercel Edge Middleware — Single shared password gate
// =============================================================================
// Protects the entire site behind HTTP Basic Auth. Browser shows a native
// login popup, students enter credentials once per session.
//
// REQUIRED Vercel environment variables:
//   AUTH_USER  — the username (e.g. "q8student")
//   AUTH_PASS  — the password (your choice — keep it strong)
//
// If either is missing, the site returns 503 (fails closed — never leaks open).
// Set them in: Vercel project → Settings → Environment Variables
// =============================================================================

export const config = {
  // Run on every request except favicon (so the browser tab icon doesn't loop
  // through the auth prompt) and any internal/build paths Vercel uses.
  matcher: '/((?!favicon\\.ico|_next|_vercel).*)',
};

export default function middleware(request: Request): Response | undefined {
  const user = process.env.AUTH_USER;
  const pass = process.env.AUTH_PASS;

  // Fail closed if env vars aren't configured yet.
  if (!user || !pass) {
    return new Response(
      'Authentication is not configured. Set AUTH_USER and AUTH_PASS in Vercel project settings.',
      {
        status: 503,
        headers: { 'Content-Type': 'text/plain; charset=utf-8' },
      }
    );
  }

  const expected = 'Basic ' + btoa(`${user}:${pass}`);
  const submitted = request.headers.get('authorization');

  if (submitted === expected) {
    // Auth passed — let the static asset through.
    return;
  }

  // Show the browser's native login popup.
  return new Response('Authentication required', {
    status: 401,
    headers: {
      'WWW-Authenticate': 'Basic realm="Q8 Logistics — Bootcamp Lab"',
      'Content-Type': 'text/plain; charset=utf-8',
    },
  });
}
