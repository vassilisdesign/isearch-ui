# VASSILISDESIGN: studio website

A bilingual (English / Greek) website for VASSILISDESIGN, an independent design studio in Greece.
It's built with Vite, vanilla JS, GSAP and Lenis, and the build outputs plain static files.

Clients, services, contact details and portfolio images come from vassilisdesign.gr and the studio brief.
The project summaries in `src/data.js` are short factual drafts: review and expand them before launch.

## Run

```bash
npm install
npm run dev       # http://localhost:5173
npm run build     # static site in dist/
npm run preview   # serve dist/
```

## Structure

| File | What it does |
| --- | --- |
| `src/data.js` | All copy in EN and EL, studio details, featured projects, the logo wall and the client index |
| `src/render.js` | HTML templates, shared by the build (pre-render) and the browser (language switch) |
| `src/motion.js` | Intro sequences (red band, image reveal, wordmark), image parallax, work tiles rising, red page wipe |
| `src/i18n.js` | Language toggle; remembers the choice; `?lang=el` also works |
| `src/contact.js` | Contact form validation and sending |
| `public/images/` | Portfolio images (WebP) |
| `index.html` | Page shell; `scripts/gen-pages.mjs` copies it to `work/<slug>.html` per project |

Every page is pre-rendered to complete English HTML at build time. That covers SEO, link previews and no-JS visitors. Greek is applied in place when chosen.

## Homepage order

Projects come first: hero, selected work, identities (logo wall), services, studio, clients, contact.

## Adding or editing a project

Add an entry to `projects` in `src/data.js`: `slug`, `client`, `sector` (`wine`, `hosp`, `food` or `corp`), a `cover` image, the `images` for its page, and `en` / `el` copy (`location`, `services`, `summary`, `scope`). Its case-study page is generated automatically.

Images use `img('name', width, height, fit)`:
- `fit: 'contain'` for packshots and logos on a white background. They blend into the page colour.
- `fit: 'cover'` (the default) for screenshots and photography.

On the homepage, images appear in black and white with the red block on hover. Case-study pages show them in full colour.

## Images

1. Export at about 1600–2000px on the long edge, as WebP (quality 75–80).
2. Put them in `public/images/` and reference them in `src/data.js`.

The current website screenshots are only 800px wide. Replacing them with larger exports will make the case-study pages sharper.

## Contact form

Create a form at formspree.io, then copy `.env.example` to `.env` and set:

```
VITE_FORMSPREE_ID=yourFormId
```

Without an ID, the form still works: it opens the visitor's email app with the message filled in.

## Deploy

`dist/` is static and uses relative paths, so it works on Netlify, Vercel, Cloudflare Pages, GitHub Pages or any web server, including in a subfolder.

## Motion and performance

- One orchestrated intro per page. Afterwards motion is gentle: images drift slightly on scroll, work tiles rise once, red blocks answer hover, and a red wipe plays between pages.
- Animation uses transforms, opacity and clip-path only. Smooth scrolling (Lenis) runs for mouse and trackpad users only; touch devices keep native scrolling.
- `prefers-reduced-motion` turns off every animation and the page transitions.
- Content is never left hidden. Pre-paint state is set by a tiny inline script with a 4-second failsafe, and visitors without JavaScript get the full page.
- About 65 KB of gzipped JS and 1.4 MB of images in total, lazy-loaded below the fold. The font is the variable Commissioner, self-hosted and split by script.
