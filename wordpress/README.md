# VASSILISDESIGN for WordPress (Hello Elementor + Elementor Pro)

Two packages, each with its own job:

**Plugin: VASSILISDESIGN Elements** (`vassilisdesign-elements.zip`): features and content structure, kept even if the theme changes.
- **Works** custom post type for the portfolio, with Sectors and simple fields (no ACF needed).
- 14 lightweight **Elementor widgets** in a "VASSILISDESIGN" category: hero, hero image (the hero's image, red band and arrow on their own, 100% of their container), section heading, Works grid, logo wall, numbered list, studio, client index, contact intro, Works archive and four widgets for single Work pages.
- The design system: colours, the Commissioner font (self-hosted), widget styles and animations.
- **Single Work and Works archive** layouts. An Elementor Pro Theme Builder template replaces them automatically.
- **Studio details** and **Legal details** (Appearance → Customize), the **EU legal pages** (Privacy Policy, Terms of Use, Legal Notice, English and Greek) and the **one-click setup**.

**Theme: VASSILISDESIGN** (`vassilisdesign.zip`), a child theme of Hello Elementor: appearance only.
- Header, footer, text-page typography, Complianz banner styling, Hello Elementor tweaks and speed settings.

## Plugins

| Plugin | Status | Why |
| --- | --- | --- |
| Hello Elementor (theme) | Required | Parent theme |
| VASSILISDESIGN Elements (this plugin) | Required | Works, widgets, design system, setup |
| Elementor | Required | Page builder |
| Elementor Pro | Required | Contact form widget; optional Theme Builder |
| Complianz – GDPR/CCPA Cookie Consent | Required | Cookie banner, consent records, Cookie Policy |
| Polylang | Optional | Only if the site is bilingual (English and Greek) |

**Not used:** Essential Addons, Premium Addons, Ultimate Addons (UAE), Unlimited Elements, Sticky Header Effects. The VD widgets and Elementor Pro already cover everything those would add. Each add-on pack loads extra CSS and JavaScript, so leaving them out keeps the site fast. If any are installed now, deactivate and delete them.

## Install

1. **Back up** the current site (files and database). Do this on a staging copy first if your host offers one.
2. Make sure **Hello Elementor**, **Elementor**, **Elementor Pro** and **Complianz** are installed and active.
3. Upload the plugin: **Plugins → Add New → Upload Plugin**, choose `vassilisdesign-elements.zip`, then **Activate**.
   Upload the theme: **Appearance → Themes → Add New → Upload Theme**, choose `vassilisdesign.zip`, then **Activate**.
4. Fill in your details: **Appearance → Customize → Studio details** (email, phone, footer text, wordmark).
5. Run the setup: **Appearance → VASSILISDESIGN setup → Run setup**. It skips anything that already exists, so it is safe to run again.
6. Open **Pages → Home → Edit with Elementor** and add:
   - the hero image (VD Hero → Image),
   - the logos (VD Logo wall → Add images),
   - the studio image (VD Studio → Image).
7. Add your Works (see below).
8. Set up Complianz (see below).
9. Fill in the `[BRACKETED]` details on **Privacy Policy**, **Terms of Use** and **Legal Notice**.
10. Check the contact form: send a test message and confirm it arrives.

### Updating from theme 1.x (one theme with everything)

Do it in this order and the site never breaks:
1. **Plugins → Add New → Upload Plugin** → `vassilisdesign-elements.zip` → **Activate**. A notice says it is waiting for the theme update. Your site keeps working.
2. **Appearance → Themes → Add New → Upload Theme** → `vassilisdesign.zip` → **Replace current with uploaded**.
3. Done: the plugin starts on its own. Your Works, Sectors, homepage widgets, Studio details and Legal details are all kept.

### Existing content

The setup never overwrites anything. If the site already has a page with the slug `home`, `privacy-policy`, `terms-of-use` or `legal-notice`, that page is kept and not replaced. Rename or delete old pages first if you want the new ones.

## Works

**Works → Add Work**:

| Field | Used for |
| --- | --- |
| Title | Client or project name |
| Excerpt | Short summary on the Work page (turn on "Excerpt" in the editor's panel if hidden) |
| Cover image (featured image) | Homepage tile and the 16:9 image on the Work page (opens full size on click) |
| Sectors | Filters on the Works archive and the "Sector" label |
| Services | Tick 2–4 in the Services box (managed under Works → Services) |
| Order (Page attributes) | Display order: lowest number first |
| Work details → Location | Shown under the tile and on the Work page |
| Work details → Scope | 1–2 sentences on the research and idea behind the design, shown in the red box |
| Work details → Live website | Optional; adds "Visit website ↗" in the red box (new tab) |
| Work details → Cover display | "Fill" for photos and screenshots, "Fit" for packshots and logos on white |
| Work details → Gallery | Images on the Work page, in order: 3 per row, each opens full size on click |
| Content (editor) | Optional longer description |

On the homepage, the **VD Works grid** shows 5 Works per row, edge to edge, with staggered heights. On hover, a red block (multiplied over the image) covers the whole tile and the Work's Excerpt appears in white, or the start of its content if there is no Excerpt. Titles use Elementor's global **Accent** font (Site Settings → Global Fonts).

Which Works appear: in the widget, **Show** → *All Works (by Order)*, *Works in one Sector*, or *Works I choose* (pick them in the order you want). Columns, full width, spacing, hover text, captions and staggered heights are in the **Layout** settings. On phones it shows 4 Works, 2 per row, with equal heights; change this under **Layout → Phones**.

Images: about 1600–2000px on the long edge, JPEG quality 75–80 or WebP. On the homepage, images show in black and white with the red block on hover. On Work pages they show in full colour. Packshots on a white background blend into the page colour automatically.

### Writing a Work (house style)

Every field says something different. Do not repeat the same words in two fields.

| Field | What goes in | Example (Hotel Atlas) |
| --- | --- | --- |
| Sector | One per Work: Wine & Spirits, Hospitality, Food & Restaurants, Food Products, Professional Services, Culture & Lifestyle | Hospitality |
| Services | 2–4 from: Brand Identity, Logo Design, Packaging, Label Design, Web Design, Print & Editorial, Stationery, Signage & Wayfinding, Art Direction, Social Media | Brand Identity, Web Design, Print & Editorial |
| Scope | The thinking before the design: what we looked at and the idea it led to. 1–2 short sentences (about 25–35 words), no year, no results. The client often gives the idea | One wave, two meanings: the logo's blue sea and green leaf are the same shape, turned and recoloured. |
| Live website | The client's live site, if there is one | https://… |
| Excerpt | 2–3 sentences: what was made and why it matters | A new identity for Hotel Atlas … |

On the Work page, Sector, Services and Location sit in 3 columns; each Sector and Service is on its own line. Updating to 2.11 moves the old Services text of each Work into the Services checklist automatically (for example "websites" becomes Web Design). Check **Works → Services** afterwards and merge anything left over.

In Elementor, every VD widget has **Style → Fonts and colours** for each of its texts. **VD Work gallery** also has Images per row, Space, Image shape and the full-image switch; **VD Work cover and summary** has the cover Shape and the same switch.

Each Work can also be built with Elementor if you want a custom layout for a special project. Its Elementor content then appears between the gallery and "Next project".

## Complianz setup

Run **Complianz → Wizard** and answer as follows. The site sets no tracking cookies, so the banner stays simple.

**General**
- Region: **Europe (GDPR)**. Add **United Kingdom** if you want UK visitors covered, and **US** only if you also need CCPA.
- Website owner: your legal name, address and country (Greece). Use the same details as the Legal Notice.
- Do you want to create a Privacy Statement? **No, I have my own** → choose the **Privacy Policy** page created by the setup.
- Imprint / Legal notice: choose the **Legal Notice** page if asked.

**Consent**
- Cookie scan: run it, then review the results. Expect only WordPress and Complianz cookies.
- Statistics: **No, I don't use statistics** (unless you add analytics later; see below).
- Do you use Google Fonts / reCAPTCHA / maps / video embeds? **No.** Fonts are self-hosted, and the form uses a hidden honeypot field instead of reCAPTCHA.
- Social media, advertising, other third-party services: **None.**

**Documents**
- Cookie Policy: let Complianz **generate** it. It creates a page called "Cookie Policy (EU)".
- Add that page to the footer legal menu: **Appearance → Menus → Legal**.

**Banner** (Complianz → Consent → Cookie Banner)
- Banner type: **Opt-in**. Consent type is set by region; for the EU it is opt-in.
- Position: **Bottom left**, Layout: **Box** or **Bar**.
- Colours, matching the site:
  - Background `#F0F0EE`, text `#000000`, border `#000000`
  - Accept button: background `#D41A14`, text `#FFFFFF`
  - Deny and Settings buttons: background `#F0F0EE`, text `#000000`, border `#000000`
  - Border radius: **0** everywhere
- Text: keep the defaults, or use "We use only the cookies needed for the site to work. You can change your choice at any time."
- **Make "Deny" as visible as "Accept".** EU regulators require refusing to be as easy as accepting.
- Soft cookie wall: **off**.

**Integrations**
- Turn off integrations the site does not use, so Complianz loads less.

The footer has a **Cookie settings** link that reopens the banner (it uses Complianz's `cmplz-show-banner` class).

### If you add analytics later

Use Complianz's statistics settings so that the analytics script runs only after consent. Then update the Privacy Policy (section 2.5) and re-run the cookie scan.

## Bilingual site (English and Greek)

WordPress needs a translation plugin for two languages. **Polylang** (free) is the lightest option:

1. Install Polylang, add **English** and **Ελληνικά**, and allow it to translate **Works** and **Sectors** (Languages → Settings → Custom post types and Taxonomies).
2. Homepage in Greek: create a Greek translation of Home, open it with Elementor, then import `setup/home-el.json` from the plugin folder (Templates → Saved Templates → Import, then insert it).
3. Legal pages in Greek: create translations of the three pages and paste the content from `setup/legal/*-el.html`, after replacing `{{...}}` with your details.
4. The header shows an **EN / ΕΛ** switch automatically when Polylang is active.
5. Texts such as "Next project" and "Scope" are translated by the plugin's and the theme's `languages/` files.

## SEO (Rank Math)

`dist/rank-math-settings.json` holds the recommended Rank Math (free) settings: only the needed modules, titles with "|", Works and Sectors indexed with their own sitemaps, legal-friendly defaults, Organization details, and visibility for AI assistants (an llms.txt file and a robots.txt that welcomes AI search crawlers).

1. Install and activate **Rank Math SEO**. Finish its setup wizard (connect a free account or skip): Rank Math stays inactive until then.
2. **Rank Math → Status & Tools → Import & Export → Import Settings**: choose `rank-math-settings.json` and import. This replaces Rank Math's settings (a backup is kept).
3. **Status & Tools → Database Tools → Re-create Missing Database Tables** (needed for Redirections and the 404 Monitor).
4. **Settings → Permalinks → Save Changes** (activates the sitemap and llms.txt addresses), then purge the cache.
5. Edit the **Home** page → Rank Math: set the SEO title and description (the front page uses its own).
6. Set the legal pages (Privacy, Terms, Legal Notice, Cookie Policy) to **noindex** in their Rank Math tab.
7. Add the street address and logo in **Titles & Meta → Local SEO**, and submit `https://vassilisdesign.gr/sitemap_index.xml` in Google Search Console.

Check: `/robots.txt`, `/llms.txt` and `/sitemap_index.xml` should open.

## Speed

The theme removes WordPress output the site does not use: emoji scripts, oEmbed, jQuery Migrate, block styles on Elementor pages, Dashicons for visitors and front-end Heartbeat. The plugin self-hosts the font and loads the animation scripts deferred.

The setup applies these Elementor settings (you can change them back under Elementor → Settings):

- Google Fonts only when hosted on your server (never loaded from Google), default colours and fonts off, font display swap
- External CSS files, leaner markup, CSS loaded per page, inline SVG icons
- Lightbox off, optimised image loading, lazy-loaded background images

Recommended on the server, without adding plugins:
- **Page caching** from your host (for example LiteSpeed Cache on LiteSpeed hosts, or the host's built-in cache).
- **PHP 8.2 or newer** and **HTTP/2 or HTTP/3**.
- **Brotli or Gzip** compression.
- Optional: **Elementor → Tools → Element Manager** to deactivate the widgets you never use.

## Email delivery

The contact form sends email through your hosting server. If test messages do not arrive, ask your host to enable SMTP or proper mail authentication (SPF, DKIM) for your domain. That is better than adding a plugin.

## Files

```
vassilisdesign-elements/        PLUGIN
  vassilisdesign-elements.php   plugin file
  inc/            Works, settings, legal details, markup helpers, assets, page layouts, Elementor, widgets, setup
  templates/      single-works.php, archive-works.php (a theme can override them)
  assets/         css/elements.css, js/elements.js, vendor/ (GSAP), fonts/ (Commissioner: Latin, Latin Extended, Greek)
  setup/          home-en.json, home-el.json (Elementor templates), legal/ (EN and EL legal pages)
  languages/      Greek translation
vassilisdesign/                 THEME (child of Hello Elementor)
  style.css, functions.php
  inc/            helpers, setup, assets, performance
  template-parts/ header and footer
  assets/css/theme.css
  languages/      Greek translation
tools/            build_templates.py (Elementor templates), build_translations.py (Greek translations)
dist/             vassilisdesign-elements.zip and vassilisdesign.zip, ready to upload
```

## Legal note

The legal pages are a solid starting point written for a small Greek design studio that uses no tracking. They are not legal advice. Fill in the bracketed details and have them reviewed, especially if you add analytics, newsletters or online payments.
