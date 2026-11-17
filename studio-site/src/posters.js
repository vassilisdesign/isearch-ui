// Generates Swiss-style SVG posters for each project from a small spec.
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
export function poster(project, { w = 400, h = 500, variant = 0, label = '' } = {}) {
  const spec = project.poster;
  const rand = seeded(`${project.slug}:${variant}`);
  const draw = compositions[spec.type] || compositions.circle;
  const body = draw({ w, h, fg: spec.fg, accent: spec.accent || spec.fg, rand, variant });
  const a11y = label
    ? `role="img" aria-label="${label.replace(/"/g, '&quot;')}"`
    : 'aria-hidden="true" focusable="false"';
  return `<svg class="poster" viewBox="0 0 ${w} ${h}" preserveAspectRatio="xMidYMid slice" ${a11y}><rect width="${w}" height="${h}" fill="${spec.bg}"/>${body}</svg>`;
}
