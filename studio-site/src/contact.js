// Contact form: validates, posts to Formspree when VITE_FORMSPREE_ID is set,
// otherwise opens the visitor's email app with the message filled in.
import { strings, studio } from './data.js';
import { currentLang } from './i18n.js';

const FORMSPREE_ID = import.meta.env.VITE_FORMSPREE_ID;
const EMAIL_RE = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

const t = (k) => strings[currentLang][k];

function validate(form) {
  const data = Object.fromEntries(new FormData(form));
  const errors = {};
  if (!data.name?.trim()) errors.name = 'form.err.name';
  if (!EMAIL_RE.test(data.email?.trim() || '')) errors.email = 'form.err.email';
  if (!data.message?.trim()) errors.message = 'form.err.message';
  return { data, errors };
}

function showErrors(form, errors) {
  form.querySelectorAll('[data-error-for]').forEach((el) => {
    const name = el.dataset.errorFor;
    const field = el.closest('.field');
    const input = form.elements[name];
    if (errors[name]) {
      el.textContent = t(errors[name]);
      el.dataset.key = errors[name];
      field.setAttribute('data-invalid', '');
      input.setAttribute('aria-invalid', 'true');
    } else {
      el.textContent = '';
      delete el.dataset.key;
      field.removeAttribute('data-invalid');
      input.removeAttribute('aria-invalid');
    }
  });
}

export function initContact() {
  const form = document.querySelector('[data-contact]');
  if (!form) return;
  const status = form.querySelector('[data-form-status]');
  const button = form.querySelector('button[type="submit"]');
  const label = button.querySelector('span');
  let statusKey = '';
  let touched = false;

  const setStatus = (key) => { statusKey = key; status.textContent = key ? t(key) : ''; };

  // Re-validate live once the visitor has tried to submit
  form.addEventListener('input', () => { if (touched) showErrors(form, validate(form).errors); });

  // Keep messages in the chosen language
  document.addEventListener('langchange', () => {
    if (statusKey) status.textContent = t(statusKey);
    form.querySelectorAll('[data-error-for][data-key]').forEach((el) => { el.textContent = t(el.dataset.key); });
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    touched = true;
    const { data, errors } = validate(form);
    showErrors(form, errors);
    const firstInvalid = Object.keys(errors)[0];
    if (firstInvalid) { form.elements[firstInvalid].focus(); return; }
    if (data._gotcha) return; // bot

    if (!FORMSPREE_ID) {
      const body = `${data.message}\n\n${data.name}${data.company ? `, ${data.company}` : ''}\n${data.email}\n${t('form.budget')}: ${data.budget}`;
      setStatus('form.mailto');
      location.href = `mailto:${studio.email}?subject=${encodeURIComponent(`New project: ${data.company || data.name}`)}&body=${encodeURIComponent(body)}`;
      return;
    }

    button.disabled = true;
    label.textContent = t('form.sending');
    setStatus('');
    try {
      const res = await fetch(`https://formspree.io/f/${FORMSPREE_ID}`, {
        method: 'POST',
        headers: { Accept: 'application/json' },
        body: new FormData(form)
      });
      if (!res.ok) throw new Error(String(res.status));
      form.reset();
      touched = false;
      setStatus('form.sent');
    } catch (err) {
      setStatus('form.failed');
    } finally {
      button.disabled = false;
      label.textContent = t('form.submit');
    }
  });
}
