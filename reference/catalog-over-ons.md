# OVER ONS (over-ons.html)

> Desktop render: `over-ons.png` 960 x 1466 => **1920 x 2932 CSS px** (0.5x scale).
> Phone render: `phone-over-ons.png` 197 x 929 => **394 x 1858 CSS px** (0.5x scale).
> All measurements below are given in **CSS px** (desktop values doubled, phone values doubled).

---

## Header / nav (shared, identical on both artboards)
- Full-width solid cyan bar across the very top of the page.
  - Desktop height: ~**30 CSS px** (15 px on the 0.5x render, y = 0-15).
  - Fill: cyan `#009ACA` (token). Renders avg `(0, 154, 202)` -- pure.
- Logo: home icon at the top-left of the cyan bar.
- Nav links rendered on the cyan bar in **Roboto Regular ~24 CSS px**, white `#FFFFFF`:

  | Link        | Desktop x (render / CSS)      | Notes                                    |
  |-------------|-------------------------------|------------------------------------------|
  | Werkwijze   | 148 / 296                     | First nav item                           |
  | Projecten   | 315 / 630                     |                                          |
  | Over ons    | 477 / 954                     | Current page -- likely shown as active   |
  | Blog        | 636 / 1272                    |                                          |
  | Contact     | 764 / 1528                    | Right-most nav item                      |

  Vertical position: y ~10-14 desktop => 20-28 CSS px (centred in the 30-px bar).

---

## Sections (in vertical order)

### 1. Hero image with overlaid page heading "OVER ONS"
- **Asset:** `res-2104ab6e.webp` (3358 x 2239). Dark chalkboard photo with orange chalk
  arrows drawn on it -- a hand drawing a large upward arrow, with many smaller arrows
  converging toward it. Avg RGB ~(82, 64, 61).
- **Additional hero element:** A photo of construction workers in orange high-vis vests
  and yellow hard hats, viewed from above, appears in the upper-right portion of the
  hero band. This appears to be a separate composited element (not part of the
  chalkboard asset), partially cut off at the top edge of the hero.
- **Layout:** full-bleed, spans the entire 1920 CSS px width.
  - Top edge: y = 0 (butts against / overlaps the bottom of the cyan header).
  - Bottom edge: y ~195 desktop => **~390 CSS px** from the top.
  - Hero band height on render: ~180 px => **~360 CSS px** tall.
- **Exact copy** (overlaid in the hero): **`OVER ONS`** (single line, two words).
- **Font / size / weight / case / color:**
  - Font: **Menlo**, **Regular** style (monospace).
  - Size: **~50 CSS px** (render cap height ~26 px => 52 CSS px).
  - Case: rendered uppercase (the string is typed uppercase).
  - Color: **white** `#FFFFFF` on the dark hero image.
  - Letter-spacing: ~`-60` tenths of an em (negative tracking). Line-spacing: 82 CSS px.
- **Layout:**
  - Left-aligned. Render x = 33 => **66 CSS px** left margin.
  - `OVER`: x = 33-115 render => **66-230 CSS px**, width 82 px render => **164 CSS px**.
  - `ONS`: x = 138-197 render => **276-394 CSS px**, width 59 px render => **118 CSS px**.
  - Gap between the two words ~23 render px => **46 CSS px**.
  - Vertical position: y ~100 desktop => **~200 CSS px** from the top of the page.
  - Text height: 26 desktop px => **52 CSS px** tall.
- **No buttons or sub-headings** in the hero -- just the title on the photo.

### 2. Intro / about-the-practice paragraph block
- A single column of body text centred on the page, sitting in white space below the
  hero.
- **Exact copy** (verbatim Dutch):

  > **Eve-n is opgericht in 2013 door Eveline Hinfelaar MBA.**
  >
  > Inmiddels bestaat ons team uit twee ervaren experts in teamontwikkeling en
  > samenwerking binnen de infrastructuursector.
  >
  > Met jarenlange ervaring bij uiteenlopende opdrachtgevers brengen we in kaart wat
  > nodig is om teams sterker te maken en resultaten te behalen. Wij geloven dat
  > effectieve verandering maatwerk vereist -- op elk niveau van de organisatie, van
  > de werkvloer tot het management. Onze aanpak is dan ook altijd zorgvuldig
  > afgestemd op de mensen, de context en de doelen van het project. Waar nodig, en
  > als het de resultaten kan versterken, zoekt Eve-n samenwerking binnen een kring
  > van coaches, adviseurs en experts.

- **Per text element:**
  - **First sentence (lead):** "Eve-n is opgericht in 2013 door Eveline Hinfelaar MBA."
    - Font: **Roboto**, **Medium**.
    - Size: ~**26-28 CSS px** (render cap height 11 px => 22 CSS px).
    - Weight: medium (heavier than the following body).
    - Color: dark `#0D0D0D`.
    - Position: render y ~272, x ~298-662 => **596-1324 CSS px** wide, centred.
  - **Following paragraphs:** Roboto Regular, ~**22 CSS px** body size, dark `#0D0D0D`,
    justified/centred block.
    - Column width: render x ~174-790 => **348-1580 CSS px** => **~1232 CSS px** wide
      (centred, equal left/right margins of ~348 CSS px).
    - Line pitch: ~19 desktop px => ~**38 CSS px** line-height.
- **Layout:** centred single column ~1232 CSS px wide, paragraphs separated by
  blank lines.
- **Asset images:** none -- pure white background (`#FFFFFF`) throughout the column.
- **Spacing:**
  - Top of block at y ~272 desktop => **544 CSS px** (~154 CSS px below the hero).
  - Block height: y ~272-460 desktop => ~**376 CSS px** tall.
  - Bottom gap: ~110 CSS px before the team section.

### 3. Team section -- two side-by-side columns (Eveline left, Thomas right)
- White background. Two equal-width columns, each holding a name, a portrait, and a
  short biography stacked vertically.
- **Card styling:** Each team member is enclosed in a card with **rounded corners** and
  a **cyan/teal border** (~2-3 CSS px stroke, color ~`#009ACA` or similar teal). Cards
  have white background and generous internal padding.
- Column extents (CSS px):
  - **Left column (Eveline):** x ~120-462 (= 240-924 render). Content ~342 CSS px
    wide; portrait fills x ~240-780 render => **480-1560 CSS px** left/right edges,
    inner width ~**540 CSS px**.
  - **Right column (Thomas):** x ~538-880 render => **1076-1760 CSS px**; portrait at
    x ~540-810 render => **1080-1620 CSS px**, inner width ~**540 CSS px**.
  - Gap between the two columns: x ~390-540 render => **780-1080 CSS px** =>
    ~**300 CSS px** inter-column gap.
- Vertical structure of each column (top-to-bottom):
  1. **Name** (e.g. "Eveline Hinfelaar")
  2. **Portrait photo** (square-ish crop, ~540 x 420 CSS px)
  3. **Biography paragraphs** (Roboto Regular body)

#### 3a. Eveline Hinfelaar column (left)
- **Name text:**
  - **Exact copy:** `Eveline Hinfelaar`
  - Font: **Roboto Medium**.
  - Size: ~**26-28 CSS px**.
  - Weight: medium/bold.
  - Color: dark `#0D0D0D`.
  - Position: render (142, 538) => **(284, 1076) CSS px** -- left-aligned within the
    column.
- **Portrait image:**
  - **CONFIRMED: `res-3d8c2aeb.webp`** (1003 x 1504).
  - Visual match: Woman with long dark hair, wearing a dark blue/navy blazer over a
    white top, silver necklace with circular pendant, silver hoop earring, arms crossed.
    White/light background. This is clearly Eveline Hinfelaar.
  - Layout:
    - Render bounds: x ~120-390, y ~580-795 => **240-780 CSS px** wide x **1160-
      1590 CSS px** tall.
    - Width: **~540 CSS px**, Height: **~430 CSS px** (aspect ~1.26:1, slightly
      landscape crop from a portrait asset).
  - White margins surround the portrait: ~60 CSS px white either side.
- **Biography text:**
  - **Exact copy (verbatim Dutch):**

    > Mijn naam is Eveline Hinfelaar. Al jarenlang ben ik gespecialiseerd in
    > samenwerking binnen de infrastructuur. Als coach en adviseur geniet ik ervan om
    > samen met organisaties en de teams daarbinnen aan de slag te gaan in het vormen
    > van een effectief, verbonden en professioneel samenwerkend geheel.
    >
    > Naast mijn werk als adviseur en coach werk ik momenteel aan mijn
    > promotieonderzoek (PhD) aan de Universiteit Twente. Met mijn onderzoek hoop ik
    > organisaties binnen de infrastructuur te inspireren om op een bewustere,
    > slimmere en duurzamere manier samen te werken binnen seriematige programma's.
    >
    > Ik woon in Rotterdam en ben moeder van twee volwassen kinderen. In alles wat
    > ik doe staan verbinding, ontwikkeling en plezier in samenwerken centraal.

  - Font: **Roboto Regular**.
  - Size: ~**22 CSS px** (render cap height ~11 px => 22 CSS px).
  - Weight: regular.
  - Color: dark `#0D0D0D` on white.
  - Position: render x ~141-366 => **282-732 CSS px** wide => ~**450 CSS px** column
    width (slightly narrower than the portrait -- bio left edge sits ~42 CSS px inset
    from the portrait's left edge).
  - Top of bio: render y ~828 => **1656 CSS px** from page top. Bottom of bio: render
    y ~1256 => **2512 CSS px**.
  - Bio height: ~**856 CSS px** (~14-16 lines of body text -- Eveline's bio is
    noticeably longer than Thomas's).
  - Line pitch ~19 desktop px => **38 CSS px** line-height.

#### 3b. Thomas Vilain column (right)
- **Name text:**
  - **Exact copy:** `Thomas Vilain`
  - Font, size, weight, color: identical to Eveline's name (**Roboto Medium ~26-28 CSS
    px**, dark `#0D0D0D`).
  - Position: render (565, 538) => **(1130, 1076) CSS px** -- left-aligned within the
    right column, vertically aligned with Eveline's name.
- **Portrait image:**
  - **CONFIRMED: `res-a4f7313d.webp`** (1003 x 1420).
  - Visual match: Man with short dark hair and light stubble, wearing a royal blue suit
    jacket over a white dress shirt, arms crossed. Outdoor background with blurred
    buildings/waterfront. This is clearly Thomas Vilain.
  - Layout:
    - Render bounds: x ~540-810, y ~580-795 => **1080-1620 CSS px** wide x **1160-
      1590 CSS px** tall -- identical size/aspect to Eveline's portrait.
    - Width: **~540 CSS px**, Height: **~430 CSS px**.
  - White margins surround the portrait identically.
- **Biography text:**
  - **Exact copy (verbatim Dutch):**

    > Mijn naam is Thomas Vilain. Ik werk inmiddels drie jaar bij Eve-n en volg
    > daarnaast een masteropleiding Culture, Organization and Management aan de
    > Vrije Universiteit Amsterdam. Naast het mooie werk dat ik bij Eve-n mag doen,
    > houd ik me graag bezig met sporten(voornamelijk boksen) en reizen. Wat ik zo
    > leuk vind aan mijn werk is de afwisseling: geen dag is hetzelfde en ik leer
    > continu nieuwe dingen.

  - Font, size, weight, color: **Roboto Regular ~22 CSS px**, dark `#0D0D0D` on white.
  - Position: render x ~569-815 => **1138-1630 CSS px** wide => ~**492 CSS px** column
    width.
  - Top of bio: render y ~834 => **1668 CSS px** -- vertically aligned with Eveline's
    bio top (within 12 CSS px).
  - Bottom of bio: render y ~1068 => **2136 CSS px**.
  - Bio height: ~**468 CSS px** (~11 lines of body -- about half the length of
    Eveline's bio).
  - Line pitch ~19 desktop px => **38 CSS px** line-height (matches Eveline's).
- **Visual asymmetry note:** Eveline's bio is roughly **1.8x** the height of Thomas's
  bio; her column is therefore much taller. The right column ends ~376 CSS px higher
  than the left, leaving a tall empty rectangle in the right column beneath Thomas's
  bio (y ~1076-1256 render => **2152-2512 CSS px**) -- pure white.

### 4. Empty white area at the bottom
- After Eveline's bio ends (render y ~1256 => 2512 CSS px), there is an empty white
  strip running down to the footer.
- Height: ~105 desktop px => **~210 CSS px** of white negative space.
- No text, no images, no buttons.

---

## Footer
- Full-width solid cyan bar at the very bottom of the page.
- Height: 71 desktop px => **~142 CSS px** (render y = 1395-1466).
- Fill: solid `#009ACA` (avg samples read `(0, 154, 202)`, std = 0 -- pure cyan).
- **No text, no links, no logo** rendered in the bar.
- Decorative colour block footer with no embedded content.

---

## Mobile (394 CSS px wide -- `phone-over-ons.png`, 197 x 929 at 0.5x)

### Header
- Cyan bar: render y = 0-15 => **30 CSS px** tall (matches desktop).
- Home icon at top-left, hamburger menu icon at top-right (three horizontal lines).
- No nav links shown -- collapsed into hamburger menu.

### Hero / heading
- Hero image band: render y = 20-45 => ~**50 CSS px** tall (much shorter than the
  360-px desktop hero -- the mobile hero crops the dark image to a thin strip).
- Page heading **`OVER ONS`** rendered inside that strip:
  - Font: **Menlo Regular**, uppercase.
  - Size: ~**24 CSS px** (cap-height 12 px on render).
  - Color: white `#FFFFFF` on the dark image.
  - Position: render (11, 31) => **(22, 62) CSS px** -- left-aligned at the standard
    mobile left margin ~22 CSS px.

### Intro block
- Position: render y ~62-260 => **~124-520 CSS px** => ~**400 CSS px** tall.
- Width: full mobile content area -- render x ~16-181 => **~32-362 CSS px** =>
  ~**330 CSS px** wide (with ~32 CSS px left/right margins).
- Same copy as desktop (4 paragraphs, verbatim Dutch as listed above).
- Font: **Roboto Medium** (lead) + **Roboto Regular** (body), ~20 CSS px.
- Color: dark `#0D0D0D` on white.

### Eveline Hinfelaar block (mobile -- single column, card with rounded cyan border)
- **Name:** `Eveline Hinfelaar`
  - Render position: y ~289 => **578 CSS px**.
  - Font: Roboto Medium ~20 CSS px, dark `#0D0D0D`.
- **Portrait:**
  - Render y ~310-395 => **620-790 CSS px** => ~**170 CSS px** tall.
  - Width: ~render full content area minus margins => ~50-185 render px =>
    **~340 CSS px** wide.
  - Aspect ~2:1 (wider than the desktop crop).
  - Same asset as desktop: **`res-3d8c2aeb.webp`** (CONFIRMED).
- **Biography:** same verbatim Dutch copy as desktop (single column, full width):
  - Render y ~400-575 => **800-1150 CSS px** => ~**350 CSS px** tall (~16 lines).
  - Font: Roboto Regular ~20 CSS px, dark `#0D0D0D`.
  - Column width: render x ~30-170 => **60-340 CSS px** => ~**280 CSS px**.

### Thomas Vilain block (mobile -- single column, card with rounded cyan border, directly below Eveline)
- **Name:** `Thomas Vilain`
  - Render position: y ~622 => **1244 CSS px** (~94 CSS px below Eveline's last
    bio line).
  - Same font / size / color as Eveline's name.
- **Portrait:**
  - Render y ~625-700 => **1250-1400 CSS px** => ~**150 CSS px** tall.
  - Width: ~**340 CSS px** -- same as Eveline's portrait on mobile.
  - Same asset as desktop: **`res-a4f7313d.webp`** (CONFIRMED).
- **Biography:** same verbatim Dutch copy as desktop:
  - Render y ~733-865 => **1466-1730 CSS px** => ~**264 CSS px** tall (~12 lines).
  - Font: Roboto Regular ~20 CSS px, dark `#0D0D0D`.
  - Column width: ~**280 CSS px** (matches Eveline's).

### Mobile footer
- Cyan bar: render y = 905-929 => **~48 CSS px** tall (vs 142 on desktop -- much
  shorter on mobile).
- Fill: solid `#009ACA`.
- No footer content rendered.

### Mobile-only differences vs desktop
1. Header bar height matches (30 CSS px) but no nav links shown -- collapsed into a
   hamburger menu icon at the top-right.
2. Hero image band is much shorter (~50 vs 360 CSS px) -- the dark image is reduced to
   a thin strip behind the title.
3. Page heading `OVER ONS` is smaller (~24 vs 52 CSS px) but kept in the same family
   (Menlo Regular uppercase, white on dark).
4. Intro block fills the full mobile content width (~330 vs 1232 CSS px desktop).
5. Two-column team layout **collapses to a single stacked column** -- Eveline block,
   then Thomas block, vertically stacked. Each in its own rounded-card with cyan border.
6. Portraits become wider/shorter (~340 x 170 CSS px, ~2:1 aspect vs the desktop's
   540 x 430 ~1.26:1 aspect).
7. Bios fill the full mobile content width (~280 CSS px vs ~450 CSS px desktop).
8. Bio/body text shrinks from ~22 to ~20 CSS px.
9. Footer bar shorter (~48 vs 142 CSS px).

---

## Interactions
- **Header / nav hover states:** the nav links are part of the shared header symbol
  with `Default State` and `Hover State`. Auto-animate ease-out 300 ms.
  - "Over ons" link is the current page -- the cyan-30 token `0,154,202,0.30` is the
    likely active-state indicator (a translucent cyan underline).
- **Hamburger menu (mobile):** the collapsed-nav glyph at the top-right of the phone
  artboard opens a full-screen / slide-in menu.
- **No accordion, no buttons, no in-page interactions** other than nav. The Over Ons
  page is a static informational page -- no cards, no toggles, no form.
- Portrait images are presentational only (no hover state visible on the render).

---

## Asset inventory (relevant to this page)
| Asset                          | Dimensions     | Role on Over Ons page |
|--------------------------------|----------------|------------------------|
| `res-2104ab6e.webp`            | 3358 x 2239    | **Hero background image** -- dark chalkboard photo with orange chalk arrows (hand drawing upward arrow, many smaller arrows converging). Full-bleed behind the `OVER ONS` title at the top of the page. |
| `res-3d8c2aeb.webp`            | 1003 x 1504    | **Eveline Hinfelaar portrait** -- CONFIRMED. Woman with long dark hair, dark blue blazer, white top, silver necklace, arms crossed, white background. Used in left column (desktop) and first card (mobile). |
| `res-a4f7313d.webp`            | 1003 x 1420    | **Thomas Vilain portrait** -- CONFIRMED. Man with short dark hair and stubble, royal blue suit, white shirt, arms crossed, outdoor waterfront background. Used in right column (desktop) and second card (mobile). |

> **Portrait assignment:** Both assignments CONFIRMED by visual inspection of the
> renders side-by-side with the asset files. `res-3d8c2aeb.webp` = Eveline (left),
> `res-a4f7313d.webp` = Thomas (right).
