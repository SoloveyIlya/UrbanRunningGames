#!/usr/bin/env node
/**
 * Запуск dev-окружения: PHP server + queue + Vite.
 * Работает на Windows и Linux без concurrently.
 */
import { spawn } from 'child_process';
import { join } from 'path';
import { fileURLToPath } from 'url';

const __dirname = fileURLToPath(new URL('.', import.meta.url));
const root = join(__dirname, '..');
const viteBin = join(root, 'node_modules', 'vite', 'bin', 'vite.js');

const children = [];

function run(cmd, args, opts = {}) {
  const p = spawn(cmd, args, {
    stdio: 'inherit',
    cwd: root,
    ...opts,
  });
  children.push(p);
  p.on('exit', (code) => {
    const i = children.indexOf(p);
    if (i !== -1) children.splice(i, 1);
    if (code !== null && code !== 0 && children.length > 0) {
      children.forEach((c) => c.kill());
      process.exit(code);
    }
  });
  return p;
}

function killAll() {
  children.forEach((c) => {
    try {
      c.kill('SIGTERM');
    } catch (_) {}
  });
  process.exit(0);
}

process.on('SIGINT', killAll);
process.on('SIGTERM', killAll);

run('php', ['artisan', 'serve']);
run('php', ['artisan', 'queue:listen', '--tries=1', '--timeout=0']);
run(process.execPath, [viteBin]);
