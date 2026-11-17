// Athens local time in the header, refreshed each minute.
import { currentLang } from './i18n.js';

export function initClock() {
  const els = document.querySelectorAll('[data-clock]');
  if (!els.length) return;
  const tick = () => {
    const now = new Date();
    const text = new Intl.DateTimeFormat(currentLang === 'el' ? 'el-GR' : 'en-GB', {
      timeZone: 'Europe/Athens', hour: '2-digit', minute: '2-digit', hour12: false
    }).format(now);
    els.forEach((el) => { el.textContent = text; el.dateTime = now.toISOString(); });
  };
  tick();
  setTimeout(() => { tick(); setInterval(tick, 60000); }, (60 - new Date().getSeconds()) * 1000);
  document.addEventListener('langchange', tick);
}
