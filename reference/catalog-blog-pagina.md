# BLOG-PAGINA (blog-pagina.html)

> Catalog produced from desktop render `reference/blog-pagina.png` (960×874 px, 0.5× → 1920×1748 CSS px) and phone render `reference/phone-blog-pagina.png` (197×554 px, 0.5× → 393×1108 CSS px). All sizes in **CSS px** (render px × 2).

---

## Header / Nav

- **Background:** solid teal/cyan `#0099CC` (approx), full-width bar, ≈80 CSS px tall.
- **Logo (left):** white home/house icon, ≈36–40 CSS px tall.
- **Nav links (centered, white text):** `Werkwijze · Projecten · Over ons · Blog · Contact`
- **Font:** sans-serif (likely Roboto Medium or similar), ≈28–32 CSS px, white `#FFFFFF`.
- **Active state:** `Blog` appears slightly bolder/highlighted as the current page.
- **Spacing:** links evenly distributed across the header width.

---

## Hero Section

### Hero Image
- **Description:** Aerial/overhead photograph of 6 construction workers wearing orange high-visibility vests and yellow hard hats, gathered around blueprints/plans on top of solar panels. Concrete ground visible. Warm sunlight casting shadows.
- **Layout:** Full-bleed, spans full viewport width, ≈600 CSS px tall.
- **Overlay:** Dark translucent wash `rgba(0,0,0, 0.40–0.50)` to ensure text legibility.
- **Asset (VISUAL-INFER):** likely `assets/res-14068bde.webp` or similar wide landscape webp — verify against asset sheet.

### Hero Title
- **Text:** `Blog titel`
- **Font:** **Menlo Bold, 70 CSS px**, white `#FFFFFF`, letter-spacing 0, line-height ≈82 CSS px.
- **Alignment:** center.
- **Position:** vertically centered within the hero, slightly above midpoint.

### Hero Subtitle
- **Text:** `Ondertitel`
- **Font:** **Menlo Regular (or Roboto Regular), 40 CSS px**, white `#FFFFFF`, line-height ≈53 CSS px.
- **Alignment:** center, directly below title with ≈8–12 CSS px gap.

### Date / Byline
- **Not present** in this render. No date, author, or meta row between subtitle and body.

---

## Body Content

### Layout
- **Background:** white `#FFFFFF`.
- **Content width:** single column, `max-width` ≈1100–1200 CSS px, centered.
- **Side padding:** ≈200 CSS px left and right on desktop.
- **Decorative element:** subtle angular/geometric shape in light gray `#E8E8E8` or `#F0F0F0` on the right side of the body area — a large diagonal polygon that adds visual interest without interfering with text.

### Typography
- **Font:** **Roboto Regular, 30 CSS px**, color `#0D0D0D` (near-black), line-height ≈39 CSS px.
- **Text alignment:** center.
- **Paragraph spacing:** ≈24–32 CSS px between paragraphs.

### Exact Body Copy (verbatim)

**Paragraph 1:**
```
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consecteturadipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consecteturadipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
```

**Paragraph 2:**
```
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consecteturadipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consecteturadipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
```

**Paragraph 3:**
```
Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consecteturadipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud. Lorem ipsum dolor sit amet, consecteturadipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
```

> Note: The three paragraphs are byte-identical placeholder Latin. The concatenations `consecteturadipiscing` are line-wrap artifacts in the source rendering — the intended sentence boundary is `consectetur adipiscing` with a single space.

### Inline Elements
- **No headings** (H2/H3) visible in the body.
- **No pull-quote** visible.
- **No inline images** visible in the rendered fold.
- **No "Lees meer" link** visible above the fold; if present it appears below the fold.

---

## Footer

- **Background:** solid teal/cyan `#0099CC` (same as header), full-width bar, ≈148 CSS px tall.
- **Content:** OCR could not surface distinct footer text from the static render. Expected shared footer template:
  - Small logo (left).
  - Standard link labels + copyright row in white.
  - Wordmark accent (right).
- **No "back to Blogs" / pagination / share links** visible in the rendered footer block.

---

## Mobile (393 CSS px) Differences

Render: `reference/phone-blog-pagina.png` (197×554, 0.5× = 393×1108 CSS px).

1. **Header:** collapses to shared mobile header — logo (home icon, left) + hamburger menu icon (right), teal `#0099CC` background.
2. **Hero:** shrunk proportionally — left/right padding ≈24 CSS px, hero height ≈300–340 CSS px.
3. **Title:** `Blog titel` — font reduced to ≈30–36 CSS px (Menlo Bold or Roboto Medium substitute), **left-aligned** instead of centered.
4. **Subtitle:** `Ondertitel` — stacks underneath title, left-aligned, wraps to at most 2 lines.
5. **Body:** single column with ≈24 CSS px side padding. Font reduced to **Roboto Regular ≈20–22 CSS px**. Each desktop paragraph now spans many more mobile lines due to narrower column. Text remains **center-aligned** (or possibly left-aligned on mobile — verify).
6. **Decorative shape:** the angular gray polygon is absent or significantly reduced on mobile.
7. **Footer:** collapses to mobile shared footer — one centered line + wordmark.
8. **No embedded images** in the body on mobile either; article remains pure running text. Hero image preserved but cropped tighter (`object-fit: cover`, `object-position: center`).

---

## Color Palette

| Token | Hex | Usage |
|-------|-----|-------|
| Teal/Cyan (header + footer) | `#0099CC` (approx) | Header bar, footer bar |
| White | `#FFFFFF` | Nav text, hero title/subtitle |
| Near-black (body text) | `#0D0D0D` | Body paragraphs |
| Light gray (decorative shape) | `#E8E8E8` / `#F0F0F0` | Angular background shape |
| Hero overlay | `rgba(0,0,0, 0.40–0.50)` | Dark wash over hero image |

---

## Font Summary

| Element | Font | Size (CSS px) | Weight | Color |
|---------|------|---------------|--------|-------|
| Nav links | Roboto Medium (approx) | 28–32 | Medium | `#FFFFFF` |
| Hero title | Menlo Bold | 70 | Bold | `#FFFFFF` |
| Hero subtitle | Menlo Regular / Roboto Regular | 40 | Regular | `#FFFFFF` |
| Body text | Roboto Regular | 30 | Regular | `#0D0D0D` |
| Body text (mobile) | Roboto Regular | 20–22 | Regular | `#0D0D0D` |
| Footer text | Nimbus Sans Bold (approx) | 16 | Bold | `#FFFFFF` |

---

## Interactions

1. **Header nav hover** — shared nav hover (Default → Hover, auto-animate 0.3 s ease-out).
2. **Internal links in article body** — underline color transition to link-blue `#2141AD` (33,65,173).
3. **Tap on logo** → routes to Home artboard `5b174190` via dissolve @ 0.6 s ease-out.
4. **Tap on nav link** → routes to corresponding inner-page artboard via dissolve transition.
5. **No carousel, swipe, gallery, or video player** on this article page.

---

## Layout Summary (Desktop)

```
┌─────────────────────────────────────────────┐
│  [🏠]  Werkwijze  Projecten  Over ons  Blog  Contact  │  ← Header: teal #0099CC, ~80px
├─────────────────────────────────────────────┤
│                                             │
│              Blog titel                     │  ← Hero: full-bleed image + dark overlay
│              Ondertitel                     │    ~600px tall, Menlo Bold 70px white
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│    Lorem ipsum dolor sit amet...            │  ← Body: white bg, centered column
│    (3 paragraphs, Roboto Regular 30px)      │    max-width ~1100-1200px
│                                             │    angular gray shape on right
│                                             │
├─────────────────────────────────────────────┤
│                                             │  ← Footer: teal #0099CC, ~148px
─────────────────────────────────────────────┘
```

(End of file)
