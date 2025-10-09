import { getGoogleIdToken } from "./firebase";

async function loginWithGoogle() {
  try {
    console.log('[GoogleLogin] Click');
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (!meta) { console.error('No CSRF meta'); return; }
    const csrf = meta.getAttribute('content');

    const idToken = await getGoogleIdToken();
    console.log('[GoogleLogin] Token OK');

    const res = await fetch('/firebase-login', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrf,
        'Accept': 'application/json',
      },
      credentials: 'include',
      body: JSON.stringify({ idToken }),
    });

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      console.error('[GoogleLogin] Backend error', err);
      alert(err.message || 'Error al iniciar sesión con Google');
      return;
    }

    const data = await res.json();
    console.log('[GoogleLogin] Backend OK', data);
    if (data.redirect) window.location.href = data.redirect;
    else window.location.reload();
  } catch (e) {
    console.error('[GoogleLogin] Exception', e);
    alert('No se pudo completar el inicio con Google.');
  }
}

window.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btn-google');
  if (btn) btn.addEventListener('click', loginWithGoogle);
});
