import { cp, mkdir, rm } from 'node:fs/promises';
import { resolve } from 'node:path';

const projectRoot = resolve(import.meta.dirname, '..');
const publicRoot = resolve(projectRoot, 'public');
const outputRoot = resolve(projectRoot, '.vercel-static');

await rm(outputRoot, { recursive: true, force: true });
await mkdir(outputRoot, { recursive: true });

for (const path of ['build', 'css', 'images', 'js', 'favicon.ico', 'robots.txt']) {
    await cp(resolve(publicRoot, path), resolve(outputRoot, path), { recursive: true });
}
