# Blog Page Catalog

## Hero / Header Section

- **Navigation bar**: Teal/cyan background (`#0ea5e9`-ish). Links: "Werkwijze", "Projecten", "Over ons", **Blog** (active/highlighted), "Contact". Home icon on far left.
- **Hero banner**: Full-width image of construction workers in orange vests and yellow hardhats gathered around blueprints (aerial/top-down view). Background texture is a grey concrete/asphalt surface.
- **Heading**: "BLOG" in large white uppercase letters, left-aligned, overlaid on the hero image.

---

## Intro Text (Dutch, verbatim)

> **We geloven sterk in het delen van kennis – want goede samenwerking begint met begrijpen wat werkt.**
>
> Dat doen we door het schrijven van blogs over thema's die we in onze dagelijkse praktijk tegen komen. Daarnaast is Eveline Hinfelaar actief met onderzoek naar effectieve samenwerking in de infrastructuur, binnen haar PhD aan de Universiteit van Twente (afdeling Infrastructuur & Management….)
>
> We delen hier inzichten, ervaringen en onderzoeksresultaten over oa. teamontwikkeling en effectieve samenwerking in projecten en programma's; seriematig - en programmatisch samenwerken, raamcontracten & andere langdurige overeenkomsten; en het onderzoek naar de resultaten die we daarin samen kunnen bereiken.

---

## Blog Cards Grid

**Layout**: 2-column grid (2×2 = **4 cards** total). Cards have equal width with a gap between columns.

### Card Structure (each card)

- **Image**: Top portion — photo of a team meeting (woman pointing at sticky notes on a wall, colleagues seated at a table with laptops). Image has a **blue border** (`~2-3px solid blue`).
- **Gold/tan horizontal rule**: Thin decorative line below the image, spanning ~80% of card width, centered.
- **Date**: "10-05-2025" in small grey text, centered.
- **Title**: "Titel blog" in bold, centered.
- **Subtitle**: "Ondertitel Blog" in regular weight, centered.
- **No visible excerpt text** or "READ MORE" button on the overview page cards (the "READ MORE" button exists as a component — see below — likely used on individual blog post pages).

### Card 1 (top-left)
- Image: team meeting / sticky notes
- Date: 10-05-2025
- Title: **Titel blog**
- Subtitle: Ondertitel Blog

### Card 2 (top-right)
- Image: team meeting / sticky notes (same placeholder)
- Date: 10-05-2025
- Title: **Titel blog**
- Subtitle: Ondertitel Blog

### Card 3 (bottom-left)
- Image: team meeting / sticky notes (same placeholder)
- Date: 10-05-2025
- Title: **Titel blog**
- Subtitle: Ondertitel Blog

### Card 4 (bottom-right)
- Image: team meeting / sticky notes (same placeholder)
- Date: 10-05-2025
- Title: **Titel blog**
- Subtitle: Ondertitel Blog

> All 4 cards use identical placeholder content — these are wireframe/mockup cards, not real blog posts.

---

## "READ MORE" Button Component

- Pill/rounded-rectangle shape, full width of its container.
- Background: teal/cyan (`#0ea5e9`-ish).
- Text: "READ MORE" in white uppercase.
- Border: thin gold/tan outline.
- Shown at 3× zoom in `zoom-btn-blog.png` — reveals the rounded corners and gold border detail.

---

## Footer

- Solid teal/cyan bar at the bottom of the page (same color as nav bar).
- No visible text/content in the footer area within the viewport.

---

## Mobile Differences (`phone-blog.png`, `phone-blog-01.png`, `phone-blog-02.png`)

- **Navigation**: Collapses to a hamburger menu (three horizontal lines) on the right. Home icon remains on the left. Teal bar retained.
- **Hero**: "BLOG" heading moves below the nav bar, left-aligned with a small gold vertical accent bar to the left of "BLOG". Hero background image is removed/hidden.
- **Intro text**: Not visible in the mobile screenshots (likely scrolls above the fold or is hidden).
- **Card grid**: Changes from 2-column to **single column** (1 card per row, stacked vertically).
- **Cards**: Same structure (blue-bordered image, gold rule, date, title, subtitle) but full-width.
- **Footer**: Same teal bar at bottom.

---

## Assets

Related webp assets found in `assets/`:

| File | Likely Use |
|------|-----------|
| `res-14068bde.webp` | — |
| `res-3d8c2aeb.webp` | — |
| `res-d6c4e942.webp` | — |
| `res-2104ab6e.webp` | — |
| `res-0f7a6f60.webp` | — |
| `res-7939c42a.webp` | — |
| `res-b5ce20a2.webp` | — |
| `res-9c128ef7.webp` | — |
| `res-a4f7313d.webp` | — |

> Note: These assets were not individually inspected for content mapping. Cross-reference with the design to assign each to hero image, card images, or other elements.
