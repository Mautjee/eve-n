#!/usr/bin/env python3
"""Slice PNG renders into overlapping horizontal strips for detailed reading.
Usage: slice.py <image> [slice_height] [overlap] [--scale N]
"""
import sys
from pathlib import Path
from PIL import Image

def main():
    img_path = Path(sys.argv[1])
    sh = int(sys.argv[2]) if len(sys.argv) > 2 else 650
    ov = int(sys.argv[3]) if len(sys.argv) > 3 else 50
    scale = 1
    if "--scale" in sys.argv:
        scale = float(sys.argv[sys.argv.index("--scale") + 1])

    im = Image.open(img_path)
    w, h = im.size
    out_dir = img_path.parent / "crops"
    out_dir.mkdir(exist_ok=True)
    for old in out_dir.glob(f"{img_path.stem}-*.png"):
        old.unlink()

    step = sh - ov
    ys = list(range(0, max(h - sh, 0) + 1, step))
    if not ys or ys[-1] != h - sh:
        ys.append(max(h - sh, 0))

    for i, y in enumerate(ys, 1):
        crop = im.crop((0, y, w, min(y + sh, h)))
        if scale != 1:
            crop = crop.resize((int(crop.width * scale), int(crop.height * scale)), Image.LANCZOS)
        crop.save(out_dir / f"{img_path.stem}-{i:02d}.png")
    print(f"{img_path.stem}: {w}x{h} -> {len(ys)} slices (scale {scale})")

if __name__ == "__main__":
    main()
