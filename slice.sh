#!/bin/bash
# slice.sh <image> <sliceHeight> [overlap] — horizontal strips into crops/<name>-NN.png
set -euo pipefail
IMG="$1"; SH="${2:-650}"; OV="${3:-50}"
DIR=$(dirname "$IMG"); BASE=$(basename "$IMG" .png)
OUT="$DIR/crops"; mkdir -p "$OUT"
W=$(sips -g pixelWidth "$IMG" | tail -1 | awk '{print $2}')
H=$(sips -g pixelHeight "$IMG" | tail -1 | awk '{print $2}')
STEP=$((SH - OV)); N=0; Y=0
while [ "$Y" -lt "$H" ]; do
  N=$((N+1))
  sips -c "$SH" "$W" --cropOffset "$Y" 0 "$IMG" --out "$OUT/${BASE}-$(printf %02d $N).png" >/dev/null 2>&1
  Y=$((Y + STEP))
  if [ $((Y + SH)) -ge "$H" ] && [ $((H - Y)) -lt "$SH" ]; then
    N=$((N+1))
    sips -c "$SH" "$W" --cropOffset $((H - SH)) 0 "$IMG" --out "$OUT/${BASE}-$(printf %02d $N).png" >/dev/null 2>&1
    break
  fi
done
echo "$BASE: ${W}x${H} -> $N slices"
