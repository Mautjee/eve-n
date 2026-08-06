#!/usr/bin/env python3
"""crop.py <image> <x> <y> <w> <h> <out> [scale]"""
import sys
from PIL import Image

img, x, y, w, h, out = sys.argv[1], int(sys.argv[2]), int(sys.argv[3]), int(sys.argv[4]), int(sys.argv[5]), sys.argv[6]
scale = float(sys.argv[7]) if len(sys.argv) > 7 else 3.0
im = Image.open(img)
crop = im.crop((x, y, x + w, y + h))
crop = crop.resize((int(w * scale), int(h * scale)), Image.LANCZOS)
crop.save(out)
print("saved", out, crop.size)
