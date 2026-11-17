import { defineConfig } from 'vite';
import { readdirSync, existsSync } from 'node:fs';
import { resolve } from 'node:path';
import {
  renderHeader, renderFooter, renderHome, renderProject, pageTitle, pageDescription, esc
} from './src/render.js';

const workDir = resolve(import.meta.dirname, 'work');
const workPages = existsSync(workDir)
  ? Object.fromEntries(readdirSync(workDir).filter((f) => f.endsWith('.html')).map((f) => [`work/${f.slice(0, -5)}`, resolve(workDir, f)]))
  : {};

// Pre-renders every page in English so content, SEO and no-JS visitors get complete HTML.
function prerender() {
  return {
    name: 'ostria-prerender',
    transformIndexHtml: {
      order: 'pre',
      handler(html) {
        const slug = (html.match(/data-slug="([^"]*)"/) || [])[1] || '';
        const root = slug ? '../' : '';
        return html
          .replace('<!--render:header-->', renderHeader(root))
          .replace('<!--render:main-->', slug ? renderProject(slug, 'en', root) : renderHome('en'))
          .replace('<!--render:footer-->', renderFooter(root))
          .replaceAll('%TITLE%', esc(pageTitle(slug)))
          .replaceAll('%DESC%', esc(pageDescription(slug)));
      }
    }
  };
}

export default defineConfig({
  base: './',
  plugins: [prerender()],
  build: {
    target: 'es2019',
    rollupOptions: { input: { main: resolve(import.meta.dirname, 'index.html'), ...workPages } }
  }
});
