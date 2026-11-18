# VASSILISDESIGN for WordPress (Hello Elementor + Elementor Pro)

A child theme of **Hello Elementor** that turns the VASSILISDESIGN design into a fast, editable WordPress site:

- **Works** custom post type for the portfolio, with Sectors and simple fields (no ACF needed).
- 12 lightweight **Elementor widgets** in a "VASSILISDESIGN" category: hero, section heading, Works grid, logo wall, numbered list, studio, client index, contact intro, and four widgets for single Work pages.
- Fast PHP **header, footer, single Work and Works archive** templates. An Elementor Pro Theme Builder template, if you make one, replaces any of them automatically.
- **EU legal pages** (Privacy Policy, Terms of Use, Legal Notice) in English and Greek, plus a **Complianz** setup sheet.
- **One-click setup** that creates the pages, homepage, menus and Sectors and applies Elementor's speed settings.

## Plugins

| Plugin | Status | Why |
| --- | --- | --- |
| Hello Elementor (theme) | Required | Parent theme |
| Elementor | Required | Page builder |
| Elementor Pro | Required | Contact form widget; optional Theme Builder |
| Complianz – GDPR/CCPA Cookie Consent | Required | Cookie banner, consent records, Cookie Policy |
| Polylang | Optional | Only if the site is bilingual (English and Greek) |

**Not used:** Essential Addons, Premium Addons, Ultimate Addons (UAE), Unlimited Elements, Sticky Header Effects. The theme widgets and Elementor Pro already cover everything those would add. Each add-on pack loads extra CSS and JavaScript, so leaving them out keeps the site fast. If any are installed now, deactivate and delete them.

## Install

1. **Back up** the current site (files and database). Do this on a staging copy first if your host offers one.
2. Make sure **Hello Elementor**, **Elementor**, **Elementor Pro** and **Complianz** are installed and active.
3. Upload the theme: **Appearance → Themes → Add New → Upload Theme**, choose `vassilisdesign.zip`, then **Activate**.
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

### Existing content

The setup never overwrites anything. If the site already has a page with the slug `home`, `privacy-policy`, `terms-of-use` or `legal-notice`, that page is kept and not replaced. Rename or delete old pages first if you want the new ones.

## Works

**Works → Add Work**:

| Field | Used for |
| --- | --- |
| Title | Client or project name |
| Excerpt | Short summary on the Work page (turn on "Excerpt" in the editor's panel if hidden) |
| Cover image (featured image) | Homepage tile and the large image on the Work page |
| Sectors | Filters on the Works archive and the "Sector" label |
| Order (Page attributes) | Display order: lowest number first |
| Work details → Location, Services | Shown under the tile and on the Work page |
| Work details → Scope | One item per line, shown in the red box |
| Work details → Cover display | "Fill" for photos and screenshots, "Fit" for packshots and logos on white |
| Work details → Gallery | Images on the Work page, in order |
| Content (editor) | Optional longer description |

On the homepage, the **VD Works grid** shows 5 Works per row, edge to edge, with staggered heights. On hover, a red block (multiplied over the image) covers the whole tile and the Work's Excerpt appears in white, or the start of its content if there is no Excerpt. Titles use Elementor's global **Accent** font (Site Settings → Global Fonts).

Which Works appear: in the widget, **Show** → *All Works (by Order)*, *Works in one Sector*, or *Works I choose* (pick them in the order you want). Columns, full width, spacing, hover text, captions and staggered heights are in the **Layout** settings.

Images: about 1600–2000px on the long edge, JPEG quality 75–80 or WebP. On the homepage, images show in black and white with the red block on hover. On Work pages they show in full colour. Packshots on a white background blend into the page colour automatically.

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
2. Homepage in Greek: create a Greek translation of Home, open it with Elementor, then import `setup/home-el.json` from the theme folder (Templates → Saved Templates → Import, then insert it).
3. Legal pages in Greek: create translations of the three pages and paste the content from `setup/legal/*-el.html`, after replacing `{{...}}` with your details.
4. The header shows an **EN / ΕΛ** switch automatically when Polylang is active.
5. Theme texts ("Next project", "Scope" and so on) are translated by `languages/el.po` / `el.mo`.

## Speed

The theme already removes WordPress output the site does not use: emoji scripts, oEmbed, jQuery Migrate, block styles on Elementor pages, Dashicons for visitors and front-end Heartbeat. It also self-hosts the font and loads the animation scripts deferred.

The setup applies these Elementor settings (you can change them back under Elementor → Settings):

- Google Fonts off, default colours and fonts off, font display swap
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
vassilisdesign/
  style.css, functions.php
  inc/            setup, Works, Customizer, templates, assets, performance, Elementor, widgets, one-click setup
  template-parts/ header and footer
  single-works.php, archive-works.php, taxonomy-work_sector.php
  assets/         css/vd.css, js/vd.js, vendor/ (GSAP), fonts/ (Commissioner: Latin, Latin Extended, Greek)
  languages/      Greek translation of the theme texts
  setup/          home-en.json, home-el.json (Elementor templates), legal/ (EN and EL legal pages)
tools/build_templates.py   regenerates the Elementor templates
```

## Legal note

The legal pages are a solid starting point written for a small Greek design studio that uses no tracking. They are not legal advice. Fill in the bracketed details and have them reviewed, especially if you add analytics, newsletters or online payments.
