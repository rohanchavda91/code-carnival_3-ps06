"""Generate PWA icons for Synapse AI (stdlib only, no PIL required).

The manifest referenced assets/icon-192.png and assets/icon-512.png which did
not exist, so PWA installability was broken. Run: python tools/generate_icons.py
"""
import os
import struct
import zlib

BOLT = [
    (0.60, 0.07),
    (0.30, 0.54),
    (0.46, 0.54),
    (0.39, 0.93),
    (0.71, 0.45),
    (0.54, 0.45),
]

TOP = (79, 70, 229)      # indigo
BOTTOM = (8, 145, 178)   # cyan
BOLT_COLOR = (248, 250, 252)  # near-white lightning bolt


def lerp(a, b, t):
    return int(round(a + (b - a) * t))


def inside_polygon(x, y, poly):
    inside = False
    j = len(poly) - 1
    for i in range(len(poly)):
        xi, yi = poly[i]
        xj, yj = poly[j]
        if (yi > y) != (yj > y):
            x_cross = (xj - xi) * (y - yi) / (yj - yi) + xi
            if x < x_cross:
                inside = not inside
        j = i
    return inside


def build(size, path):
    radius = size * 0.18
    rows = []
    for y in range(size):
        row = bytearray()
        for x in range(size):
            # rounded-corner alpha
            cx = min(x, size - 1 - x)
            cy = min(y, size - 1 - y)
            if cx < radius and cy < radius:
                dx = radius - cx
                dy = radius - cy
                dist = (dx * dx + dy * dy) ** 0.5
                alpha = max(0.0, min(1.0, radius - dist + 1.0))
            else:
                alpha = 1.0

            t = (x + y) / (2.0 * (size - 1))
            r = lerp(TOP[0], BOTTOM[0], t)
            g = lerp(TOP[1], BOTTOM[1], t)
            b = lerp(TOP[2], BOTTOM[2], t)

            # 3x3 supersample the lightning bolt for smooth edges
            hits = 0
            for sy in (-0.33, 0.0, 0.33):
                for sx in (-0.33, 0.0, 0.33):
                    if inside_polygon((x + sx) / size, (y + sy) / size, BOLT):
                        hits += 1
            if hits:
                k = hits / 9.0
                r = lerp(r, BOLT_COLOR[0], k)
                g = lerp(g, BOLT_COLOR[1], k)
                b = lerp(b, BOLT_COLOR[2], k)

            row += bytes((r, g, b, int(alpha * 255)))
        rows.append(bytes(row))

    raw = b''.join(b'\x00' + row for row in rows)

    def chunk(tag, data):
        payload = tag + data
        return struct.pack('>I', len(data)) + payload + struct.pack('>I', zlib.crc32(payload) & 0xFFFFFFFF)

    png = b'\x89PNG\r\n\x1a\n'
    png += chunk(b'IHDR', struct.pack('>IIBBBBB', size, size, 8, 6, 0, 0, 0))
    png += chunk(b'IDAT', zlib.compress(raw, 9))
    png += chunk(b'IEND', b'')

    with open(path, 'wb') as fh:
        fh.write(png)
    print('wrote', path, len(png), 'bytes')


if __name__ == '__main__':
    out_dir = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), 'assets', 'icons')
    os.makedirs(out_dir, exist_ok=True)
    build(192, os.path.join(out_dir, 'icon-192.png'))
    build(512, os.path.join(out_dir, 'icon-512.png'))
