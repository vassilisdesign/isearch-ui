# Ostria: studio website

A bilingual (English / Greek) website for a brand and digital design studio in Athens.
It's built with Vite, vanilla JS, GSAP and Lenis, and the build outputs plain static files.

"Ostria" and all copy, clients and figures are **placeholders**. Every piece of text is in `src/data.js`.

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
| `src/data.js` | All copy in EN and EL: UI strings, studio details, projects |
| `src/render.js` | HTML templates, shared by the build (pre-render) and the browser (language switch) |
| `src/posters.js` | Black-and-white image stand-ins (generated SVG) and the `art()` helper that swaps in real photos |
| `src/motion.js` | Intro sequences (red band, image reveal, wordmark), image parallax, work tiles rising, red page wipe |
| `src/i18n.js` | Language toggle; remembers the choice; `?lang=el` also works |
| `src/contact.js` | Contact form validation and sending |
| `index.html` | Page shell; `scripts/gen-pages.mjs` copies it to `work/<slug>.html` per project |

Every page is pre-rendered to complete English HTML at build time. That covers SEO, link previews and no-JS visitors. Greek is applied in place when chosen.

## Adding a project

Add an entry to `projects` in `src/data.js` with `en` and `el` copy and a `poster` spec
(`waves`, `circle`, `grid`, `stripes`, `columns` or `arcs`). Its case-study page is generated automatically.

## Photography

The design is built around black-and-white photography crossed by solid red blocks.
Until real photos are added, every image slot shows a generated black-and-white stand-in with print grain.

1. Put photos in `public/images/` (any colour photo works: CSS renders it in black and white and adds the red block).
2. Reference them in `src/data.js`:
   - `studio.heroPhoto` for the hero (a portrait-format photo, roughly 4:5, works best) and `studio.studioPhoto` for the studio section.
   - `photo: 'images/kyma.jpg'` on a project for every slot, or `photo: ['images/kyma-cover.jpg', 'images/kyma-2.jpg', 'images/kyma-3.jpg']` for the cover and the two detail images.
3. Export at about 2000px on the long edge, as WebP or a quality-75 JPEG, so pages stay fast on phones.

## Contact form

Create a form at formspree.io, then copy `.env.example` to `.env` and set:

```
VITE_FORMSPREE_ID=yourFormId
```

Without an ID, the form still works: it opens the visitor's email app with the message filled in.

## Deploy

`dist/` is static and uses relative paths, so it works on Netlify, Vercel, Cloudflare Pages, GitHub Pages or any web server, including in a subfolder.

## Motion and performance

- One orchestrated intro per page. Afterwards motion only responds to the visitor: scroll speed shears the wordmark, hovering shows a poster, and clicking a link wipes to the next page.
- Animation uses transforms, opacity and clip-path only. Smooth scrolling (Lenis) runs for mouse and trackpad users only; touch devices keep native scrolling.
- `prefers-reduced-motion` turns off every animation and the page transitions.
- Content is never left hidden. Pre-paint state is set by a tiny inline script with a 4-second failsafe, and visitors without JavaScript get the full page.
- About 65 KB of gzipped JS. The font is the variable Commissioner, self-hosted and split by script, so only the Latin or Greek subset a page uses is downloaded.
