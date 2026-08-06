#!/bin/bash
set -euo pipefail

BASE="https://cdn-sharing.adobecc.com/content/storage/id/urn:aaid:sc:US:70ce7a80-49e1-4054-8580-b7b4a28d5fcf"
TOK="api_key=CometServer1&access_token=1785976932_urn%3Aaaid%3Asc%3AUS%3A70ce7a80-49e1-4054-8580-b7b4a28d5fcf%3Bpublic_9cf2f8443bb18ba2b3c28c0b3931199d9d90e5db"
ROOT="/Users/mauro/Dev/vivera-site"

dl() { # dl <revision> <component_id> <outfile>
  local rev="$1" cid="$2" out="$3"
  local url="${BASE};revision=${rev}?component_id=${cid}&${TOK}"
  curl -sSf -o "$out" "$url" || curl -sSf -o "$out" "${BASE}?component_id=${cid}&${TOK}"
  echo "OK  $out ($(stat -f%z "$out") bytes)"
}

# Desktop artboard renders (revision 1)
dl 1 6c88dd5a-f898-4133-a98b-374c2780aab2 "$ROOT/reference/reviews.png"
dl 1 c0fa10f9-c1c8-4836-acd1-22f33aad5dc9 "$ROOT/reference/over-ons.png"
dl 1 2a94ac44-1557-4746-b272-a3f9fb6737d0 "$ROOT/reference/blog-pagina.png"
dl 1 7590cb91-c36a-465b-b1af-d09b9c63c9d9 "$ROOT/reference/blogs.png"
dl 1 1b435102-0f4c-4e82-a1d6-7cb64d61693c "$ROOT/reference/contact.png"
dl 1 1d0871c9-fe05-4f7f-bf5f-bbed3be195b8 "$ROOT/reference/onze-werkwijze.png"

# Phone artboard renders (revision 0)
dl 0 56a085ca-3256-48c1-8621-2be0fb2e58ea "$ROOT/reference/phone-home.png"
dl 0 fb281446-d5f1-44d4-9b41-c68a19c1ea86 "$ROOT/reference/phone-projecten.png"
dl 0 e98f1a53-1af5-42b1-bc1b-3dafc48c115c "$ROOT/reference/phone-over-ons.png"
dl 0 53c6ec1f-fd0f-445d-a188-aababd2a15dd "$ROOT/reference/phone-blog.png"
dl 0 b3b6ffb7-9300-45e5-9b00-33fe2e99c6b0 "$ROOT/reference/phone-blog-pagina.png"
dl 0 945ee280-4ae0-4afb-b414-a27278596344 "$ROOT/reference/phone-contact.png"
dl 0 65f526b4-273b-4f33-905f-17b1a90a8722 "$ROOT/reference/phone-onze-werkwijze.png"

# Embedded image resources (revision 0)
dl 0 ca52f652-493c-4d31-82d6-dbe8b7dd089d "$ROOT/assets/res-7939c42a"
dl 0 e6fdada5-8e5f-4516-b32e-851c90837a8b "$ROOT/assets/res-a4f7313d"
dl 0 828cb5e1-6e20-4c00-86f7-551a8d859749 "$ROOT/assets/res-b5ce20a2"
dl 0 c118f140-03bc-412e-b8d5-4a759d990036 "$ROOT/assets/res-14068bde"
dl 0 9fbb3687-9538-428c-b70a-a82e18a5ab58 "$ROOT/assets/res-0f7a6f60"
dl 0 6b8012d4-ee81-4283-82cc-d380e15b9767 "$ROOT/assets/res-3d8c2aeb"
dl 0 a656eaca-8a07-4776-8e50-27702a4c0cb3 "$ROOT/assets/res-2104ab6e"
dl 0 ff7d8f8d-10d5-4500-8077-ee2942a1bb41 "$ROOT/assets/res-d6c4e942"
dl 0 c77e3c84-e7cf-40e4-ae65-bf22ae1fc1a8 "$ROOT/assets/res-9c128ef7"

# JSON data (revision 1)
dl 1 cd1637ef-f3ab-4756-ba68-f0cf1e6b480f "$ROOT/reference/interactions.json"
dl 1 f79037bb-8409-43f5-80d5-cd9c8a0a9d1f "$ROOT/reference/globalResources.json"

echo "--- file types ---"
file "$ROOT"/assets/res-* "$ROOT"/reference/*.png | sed "s|$ROOT/||"
