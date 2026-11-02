"""Build a static HTML preview of index.php.

PHP is not installed in this environment, so this script inlines the
`<?php include_once __DIR__ . '/...'; ?>` partials and strips the remaining
PHP blocks, producing .preview/index.html that behaves exactly like the
rendered PHP page (the partials are pure HTML apart from PHP comments).

Run: python tools/build_preview.py
"""
import os
import re

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
OUT_DIR = os.path.join(ROOT, '.preview')

INCLUDE_RE = re.compile(r"<\?php\s*include_once\s+__DIR__\s*\.\s*'([^']+)'\s*;\s*\?>")
PHP_BLOCK_RE = re.compile(r"<\?php.*?\?>", re.S)
ASSET_RE = re.compile(r'(?:href|src)="((?:\./)?(?:css|js|data|assets|manifest\.json)[^"]*)"')


def load(path):
    with open(path, encoding='utf-8') as fh:
        return fh.read()


def build():
    html = load(os.path.join(ROOT, 'index.php'))

    # Inline each include (relative to the file that contains the reference).
    def inline(match):
        rel = match.group(1)
        target = os.path.normpath(os.path.join(ROOT, rel.lstrip('/')))
        content = load(target)
        # Nested includes (none today, but safe).
        base = os.path.dirname(target)
        content = INCLUDE_RE.sub(
            lambda m: load(os.path.normpath(os.path.join(base, m.group(1).lstrip('/')))),
            content
        )
        return content

    html = INCLUDE_RE.sub(inline, html)
    html = PHP_BLOCK_RE.sub('', html)

    # Preview file lives in .preview/, so root-relative assets need one level up.
    html = ASSET_RE.sub(lambda m: 'href="../%s"' % m.group(1).lstrip('./') if m.group(0).startswith('href')
                        else 'src="../%s"' % m.group(1).lstrip('./'), html)

    os.makedirs(OUT_DIR, exist_ok=True)
    out_path = os.path.join(OUT_DIR, 'index.html')
    with open(out_path, 'w', encoding='utf-8') as fh:
        fh.write(html)

    leftover = re.search(r'<\?', html)
    print('wrote', out_path, '| php markers left:', bool(leftover))


if __name__ == '__main__':
    build()
