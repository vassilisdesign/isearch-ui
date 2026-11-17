import '@fontsource-variable/commissioner/wght.css';
import 'lenis/dist/lenis.css';
import './styles.css';
import { initI18n } from './i18n.js';
import { initClock } from './clock.js';
import { initContact } from './contact.js';
import { initMotion } from './motion.js';

window.__ostria = true; // tells the inline failsafe that scripts are running

initI18n();
initClock();
initContact();

// Start the intro once the typeface is ready (or after 1.2s), so letters never swap mid-animation.
const fontsReady = document.fonts ? document.fonts.ready : Promise.resolve();
Promise.race([fontsReady, new Promise((r) => setTimeout(r, 1200))]).then(() => {
  try {
    initMotion();
  } catch (err) {
    document.documentElement.classList.remove('intro', 'is-wiping');
    console.error(err);
  }
});
