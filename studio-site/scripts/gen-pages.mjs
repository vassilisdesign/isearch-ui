// Writes one HTML shell per project into /work, based on index.html.
// The Vite plugin in vite.config.js fills each shell with pre-rendered content.
import { readFileSync, writeFileSync, mkdirSync, rmSync } from 'node:fs';
import { dirname, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';
import { projects } from '../src/data.js';

const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const shell = readFileSync(resolve(root, 'index.html'), 'utf8');
const outDir = resolve(root, 'work');
rmSync(outDir, { recursive: true, force: true });
mkdirSync(outDir, { recursive: true });

for (const p of projects) {
  const html = shell
    .replace('data-slug=""', `data-slug="${p.slug}"`)
    .replaceAll('href="./src/', 'href="../src/')
    .replaceAll('src="./src/', 'src="../src/');
  writeFileSync(resolve(outDir, `${p.slug}.html`), html);
}
console.log(`Generated ${projects.length} case-study pages in /work`);
