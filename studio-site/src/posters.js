// Generates image stand-ins: Swiss-style SVG compositions, rendered in black and white (mono)
// so they take the same red-block treatment as real photography. Real photos replace them via `photo` in data.js.
// Deterministic (seeded by slug + variant) so build output and browser output match.

function seeded(str) {
  let h = 1779033703 ^ str.length;
  for (let i = 0; i < str.length; i++) {
    h = Math.imul(h ^ str.charCodeAt(i), 3432918353);
    h = (h << 13) | (h >>> 19);
  }
  let a = h >>> 0;
  return () => {
    a |= 0; a = (a + 0x6d2b79f5) | 0;
    let t = Math.imul(a ^ (a >>> 15), 1 | a);
    t = (t + Math.imul(t ^ (t >>> 7), 61 | t)) ^ t;
    return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
  };
}

const r1 = (n) => Math.round(n * 10) / 10;

const compositions = {
  // Whitewashed Cycladic steps in hard sun: the studio stand-in.
  steps({ w, h, id }) {
    const n = 7;
    const sw = w / (n + 1.5);
    const sh = h / (n + 3);
    let out = `<rect width="${w}" height="${h}" fill="url(#${id}-stone)"/>`;
    for (let i = 0; i < n; i++) {
      const x = i * sw;
      const y = h - (i + 1) * sh;
      out += `<rect x="${r1(x)}" y="${r1(y)}" width="${r1(w - x)}" height="${r1(sh)}" fill="url(#${id}-riser)"/>`;
      out += `<rect x="${r1(x)}" y="${r1(y)}" width="${r1(w - x)}" height="${r1(sh * 0.22)}" fill="#f4f4f1"/>`;
    }
    out += `<polygon points="${r1(w * 0.55)},0 ${w},0 ${w},${r1(h * 0.62)}" fill="#000" opacity="0.32"/>`;
    return out;
  },

  // Sunlit Doric colonnade: the hero stand-in.
  colonnade({ w, h, rand, variant, id }) {
    const n = 5 + variant;
    const top = h * 0.2;
    const base = h * 0.82;
    const span = (w * 1.12) / n;
    const cw = span * 0.58;
    const off = -w * 0.06 + rand() * span * 0.25;
    let out = `<rect x="0" y="${r1(top)}" width="${w}" height="${r1(base - top)}" fill="url(#${id}-in)"/>`;
    for (let i = 0; i < n; i++) {
      const x = off + i * span;
      out += `<rect x="${r1(x)}" y="${r1(top)}" width="${r1(cw)}" height="${r1(base - top)}" fill="url(#${id}-col)"/>`;
      for (let f = 1; f < 9; f++) {
        const fx = x + (cw / 9) * f;
        out += `<line x1="${r1(fx)}" y1="${r1(top)}" x2="${r1(fx)}" y2="${r1(base)}" stroke="#000" stroke-opacity="${(0.05 + f * 0.012).toFixed(3)}" stroke-width="${r1(cw / 70)}"/>`;
      }
      out += `<rect x="${r1(x - cw * 0.1)}" y="${r1(top - h * 0.03)}" width="${r1(cw * 1.2)}" height="${r1(h * 0.04)}" fill="url(#${id}-col)"/>`;
    }
    out += `<rect x="0" y="${r1(top - h * 0.17)}" width="${w}" height="${r1(h * 0.14)}" fill="url(#${id}-stone)"/>`;
    out += `<rect x="0" y="${r1(top - h * 0.03)}" width="${w}" height="${r1(h * 0.012)}" fill="#1a1a1a" opacity="0.55"/>`;
    for (let s = 0; s < 3; s++) {
      const y = base + s * h * 0.06;
      out += `<rect x="0" y="${r1(y)}" width="${w}" height="${r1(h * 0.02)}" fill="#efefec"/>`;
      out += `<rect x="0" y="${r1(y + h * 0.02)}" width="${w}" height="${r1(h * 0.04)}" fill="url(#${id}-riser)"/>`;
    }
    return out;
  },

  waves({ w, h, fg, rand, variant }) {
    const rows = 9 + variant * 2;
    const amp = h / rows / 2.4;
    const len = w / (1.4 + rand() * 0.8);
    const phase = rand() * Math.PI * 2;
    let out = '';
    for (let i = 0; i < rows; i++) {
      const y0 = (h / rows) * (i + 0.5);
      let d = '';
      for (let x = -w * 0.05; x <= w * 1.05; x += w / 40) {
        d += d ? ' L' : 'M';
        d += `${r1(x)} ${r1(y0 + Math.sin((x / len) * Math.PI * 2 + phase + i * 0.55) * amp)}`;
      }
      out += `<path d="${d}" fill="none" stroke="${fg}" stroke-width="${r1(h / rows / 7)}"/>`;
    }
    return out;
  },

  circle({ w, h, fg, rand, variant }) {
    const r = Math.min(w, h) * (0.34 + rand() * 0.08);
    const cx = variant === 1 ? w * 0.3 : variant === 2 ? w * 0.7 : w * 0.58;
    const cy = h * (0.42 + rand() * 0.12);
    const sr = r * 0.16;
    return `<circle cx="${r1(cx)}" cy="${r1(cy)}" r="${r1(r)}" fill="${fg}"/>` +
      `<circle cx="${r1(cx - r * 1.05)}" cy="${r1(cy + r * 1.1)}" r="${r1(sr)}" fill="${fg}"/>` +
      `<line x1="0" y1="${r1(h * 0.88)}" x2="${w}" y2="${r1(h * 0.88)}" stroke="${fg}" stroke-width="2"/>`;
  },

  grid({ w, h, fg, accent, rand }) {
    const cols = 6;
    const size = w / cols;
    const rows = Math.ceil(h / size);
    const accentIndex = Math.floor(rand() * cols * rows);
    let out = '';
    for (let y = 0; y < rows; y++) {
      for (let x = 0; x < cols; x++) {
        const i = y * cols + x;
        const v = rand();
        const pad = size * 0.12;
        if (i === accentIndex) {
          out += `<rect x="${r1(x * size + pad)}" y="${r1(y * size + pad)}" width="${r1(size - pad * 2)}" height="${r1(size - pad * 2)}" fill="${accent}"/>`;
        } else if (v < 0.42) {
          out += `<rect x="${r1(x * size + pad)}" y="${r1(y * size + pad)}" width="${r1(size - pad * 2)}" height="${r1(size - pad * 2)}" fill="${fg}"/>`;
        } else if (v < 0.62) {
          out += `<circle cx="${r1(x * size + size / 2)}" cy="${r1(y * size + size / 2)}" r="${r1(size / 2 - pad)}" fill="none" stroke="${fg}" stroke-width="2"/>`;
        }
      }
    }
    return out;
  },

  stripes({ w, h, fg, variant }) {
    const band = Math.max(w, h) / (7 + variant * 3);
    let out = '';
    for (let i = -Math.ceil(h / band) - 2; i < Math.ceil(w / band) + 2; i += 2) {
      const x = i * band;
      out += `<polygon points="${r1(x)},0 ${r1(x + band)},0 ${r1(x + band + h)},${h} ${r1(x + h)},${h}" fill="${fg}"/>`;
    }
    return out;
  },

  columns({ w, h, fg, rand }) {
    let out = '';
    let x = w * 0.06;
    while (x < w * 0.94) {
      const cw = w * (0.012 + rand() * 0.07);
      const top = h * (0.08 + rand() * 0.3);
      out += `<rect x="${r1(x)}" y="${r1(top)}" width="${r1(cw)}" height="${r1(h * 0.92 - top)}" fill="${fg}"/>`;
      x += cw + w * (0.02 + rand() * 0.05);
    }
    return out;
  },

  arcs({ w, h, fg, variant }) {
    const cx = variant === 1 ? w : 0;
    const cy = h;
    const max = Math.hypot(w, h);
    const step = max / (11 + variant * 2);
    let out = '';
    for (let r = step; r < max; r += step * 2) {
      out += `<circle cx="${cx}" cy="${cy}" r="${r1(r)}" fill="none" stroke="${fg}" stroke-width="${r1(step * 0.9)}"/>`;
    }
    return out;
  }
};

/**
 * @param {object} project  project entry from data.js
 * @param {object} opts     { w, h, variant, label }
 * @returns {string} inline SVG markup
 */
export function poster(project, { w = 400, h = 500, variant = 0, label = '', mono = true } = {}) {
  const spec = project.poster;
  const id = `a${project.slug.replace(/[^a-z0-9]/gi, '')}${variant}${w}`;
  const rand = seeded(`${project.slug}:${variant}`);
  const draw = compositions[spec.type] || compositions.circle;
  const fg = mono ? '#161616' : spec.fg;
  const accent = mono ? '#77776f' : spec.accent || spec.fg;
  const body = draw({ w, h, fg, accent, rand, variant, id });
  const a11y = label
    ? `role="img" aria-label="${label.replace(/"/g, '&quot;')}"`
    : 'aria-hidden="true" focusable="false"';
  const defs = mono
    ? `<defs>
<linearGradient id="${id}-bg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#ecece9"/><stop offset="1" stop-color="#b4b4ae"/></linearGradient>
<linearGradient id="${id}-col" x1="0" x2="1"><stop offset="0" stop-color="#f6f6f3"/><stop offset=".35" stop-color="#dcdcd7"/><stop offset=".75" stop-color="#8d8d87"/><stop offset="1" stop-color="#4b4b47"/></linearGradient>
<linearGradient id="${id}-in" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#0c0c0c"/><stop offset="1" stop-color="#3a3a37"/></linearGradient>
<linearGradient id="${id}-stone" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#e2e2de"/><stop offset="1" stop-color="#a9a9a3"/></linearGradient>
<linearGradient id="${id}-riser" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#7b7b75"/><stop offset="1" stop-color="#3d3d3a"/></linearGradient>
<radialGradient id="${id}-v" cx=".45" cy=".4" r=".8"><stop offset=".55" stop-color="#000" stop-opacity="0"/><stop offset="1" stop-color="#000" stop-opacity=".35"/></radialGradient>
</defs>`
    : '';
  const bg = mono ? `url(#${id}-bg)` : spec.bg;
  const vignette = mono ? `<rect width="${w}" height="${h}" fill="url(#${id}-v)"/>` : '';
  return `<svg class="poster" viewBox="0 0 ${w} ${h}" preserveAspectRatio="xMidYMid slice" ${a11y}>${defs}<rect width="${w}" height="${h}" fill="${bg}"/>${body}${vignette}</svg>`;
}

/**
 * A project image: the real photo when `project.photo` is set, otherwise the generated stand-in.
 * Photos are shown in black and white by CSS, so any colour photo works.
 */
export function art(project, { w = 400, h = 500, variant = 0, label = '', root = '', eager = false } = {}) {
  const ph = project.photo && (Array.isArray(project.photo) ? project.photo[variant] || project.photo[0] : project.photo);
  if (ph) {
    const src = typeof ph === 'string' ? ph : ph.src;
    return `<img class="photo-img" src="${root}${src}" alt="${(label || '').replace(/"/g, '&quot;')}" width="${w}" height="${h}" ${eager ? 'fetchpriority="high"' : 'loading="lazy"'} decoding="async">`;
  }
  return poster(project, { w, h, variant, label });
}
