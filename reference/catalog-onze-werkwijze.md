# Catalog — Onze Werkwijze

> Source images: `reference/onze-werkwijze.png` (960×839 @ 0.5x → 1920×1678 CSS px),
> `reference/crops/zoom-btn-werkwijze.png`, `reference/phone-onze-werkwijze.png`

---

## 1. Global Layout

| Property | Value |
|---|---|
| Page width (desktop) | 1920 CSS px |
| Content max-width | ~1200 CSS px, centered |
| Background | White `#FFFFFF` |
| Bottom accent bar | Teal `#0097A7`, full-width, ~80 CSS px tall |

---

## 2. Navigation Bar

| Property | Value |
|---|---|
| Background | Teal `#0097A7` |
| Height | ~80 CSS px |
| Position | Fixed / sticky top |

**Left element:**
- Home icon (house outline), white, ~24 CSS px

**Menu items (centered, evenly distributed):**

| Item | Active state |
|---|---|
| Werkwijze | **Active** — white text |
| Projecten | White text |
| Over ons | White text |
| Blog | White text |
| Contact | White text |

| Property | Value |
|---|---|
| Font | Sans-serif (likely Inter / Open Sans) |
| Size | ~18 CSS px |
| Weight | 400 (regular) |
| Case | Sentence case (first letter capitalised) |
| Color | `#FFFFFF` |
| Spacing between items | ~80–100 CSS px |

---

## 3. Hero Section

| Property | Value |
|---|---|
| Height | ~460 CSS px |
| Background | Full-bleed photograph — aerial/top-down view of construction workers in orange high-vis vests and white hard hats on gray concrete/asphalt |
| Overlay | None (text sits directly on photo) |

**Title text:**

| Property | Value |
|---|---|
| Text | `ONZE WERKWIJZE` |
| Font | Monospace / slab-serif (appears to be a typewriter-style font, e.g. Courier New or similar) |
| Size | ~56 CSS px |
| Weight | 700 (bold) |
| Case | UPPERCASE |
| Color | `#FFFFFF` |
| Position | Left-aligned, ~80 CSS px from left edge, vertically centered in hero |

---

## 4. Body Content Section

| Property | Value |
|---|---|
| Background | White `#FFFFFF` |
| Decorative shape | Large light-gray (`#E8E8E8`) organic/blob shape on the right side, extending from hero boundary down through ~60% of content area |
| Content column width | ~900 CSS px, left-aligned with ~160 CSS px left margin |
| Paragraph spacing | ~24 CSS px between paragraphs |

### Paragraph 1

> De infrastructuur kent grote uitdagingen! Samenwerken binnen deze complexe opgaves is cruciaal om effectief en succesvol te zijn. En dat moet je samen goed organiseren! Succesvolle samenwerking vraagt om méér dan structuur alleen. Het draait ook om het ontwikkelen van samenwerkingsvaardigheden, het versterken van onderling vertrouwen en het bouwen aan een (h)echt team. Eve-n begeleidt dit proces met oog voor de mens én het project. Samen maken we van samenwerking een kracht – voor projecten die niet alleen efficiënt, maar ook met plezier worden gerealiseerd.

### Paragraph 2

> Al meer dan tien jaar ondersteunt Eve-n teams in de infrastructuursector bij het versterken van hun samenwerking. Wij geloven dat effectieve samenwerking begint bij goed georganiseerde (bouw)teams. Dat betekent heldere rollen en verantwoordelijkheden, duidelijke communicatieafspraken, een gedeeld doel en een gezamenlijk gedragen planning. Maar daar stopt het niet.

### Paragraph 3

> Of het nu gaat om de start van een aanbesteding of de afronding van een complex infraproject – Eve-n is er in elke fase. We bieden strategisch advies, training en coaching op álle niveaus. Onze rol varieert van procesbegeleider en facilitator tot teamcoach – altijd met een scherp oog voor wat de samenwerking op dat moment nodig heeft.

### Paragraph 4

> Met onze teamgerichte aanpak bouwen we aan professionele, samenwerkingsrelaties in het team én zorgen we voor duidelijke afspraken met ruimte voor open, eerlijke gesprekken. Zo versterken we de samenwerking, brengen we mensen in beweging en houden we het gezamenlijke doel én ieders belangen scherp in beeld.

**Body text styling (all paragraphs):**

| Property | Value |
|---|---|
| Font | Sans-serif (likely Inter / Open Sans) |
| Size | ~20 CSS px |
| Weight | 400 (regular) |
| Case | Sentence case |
| Color | `#333333` (near-black) |
| Line height | ~1.6 |

---

## 5. "Read More" Button (zoom-btn-werkwijze.png)

| Property | Value |
|---|---|
| Text | `READ MORE` |
| Font | Sans-serif |
| Size | ~20 CSS px |
| Weight | 400 (regular) |
| Case | UPPERCASE |
| Color | `#FFFFFF` |
| Background | Teal `#0097A7` |
| Border | ~2 CSS px solid gold/amber `#C9A84C` |
| Border-radius | Pill shape (~40 CSS px) |
| Padding | ~16 CSS px vertical, ~48 CSS px horizontal |
| Position | Appears below body content, centered or left-aligned within content column |

---

## 6. Bottom Accent Bar

| Property | Value |
|---|---|
| Background | Teal `#0097A7` |
| Height | ~80 CSS px |
| Width | Full viewport |

---

## 7. Mobile Version (`phone-onze-werkwijze.png`)

| Property | Desktop | Mobile |
|---|---|---|
| Nav layout | Horizontal menu items | Hamburger menu (☰) right-aligned, home icon left |
| Nav height | ~80 CSS px | ~56 CSS px |
| Hero height | ~460 CSS px | ~200 CSS px |
| Title size | ~56 CSS px | ~28 CSS px |
| Title position | Left-aligned, ~80 px margin | Left-aligned, ~16 px margin |
| Content column width | ~900 CSS px | ~100% minus ~32 px padding |
| Body text size | ~20 CSS px | ~16 CSS px |
| Gray decorative shape | Large blob on right | Reduced / repositioned, less prominent |
| Paragraph spacing | ~24 CSS px | ~16 CSS px |
| Button | Pill with gold border | Same style, full-width or near-full-width |

**Mobile nav details:**

| Property | Value |
|---|---|
| Background | Teal `#0097A7` |
| Left element | Home icon (house outline), white, ~20 CSS px |
| Right element | Hamburger icon (3 horizontal lines), white, ~24 CSS px |
| Menu items | Hidden, revealed via hamburger toggle |

**Mobile hero:**
- Same aerial construction worker photo, cropped to fit narrower viewport
- Title `ONZE WERKWIJZE` still uppercase, monospace/slab-serif, white

**Mobile body:**
- Same 4 paragraphs, same Dutch text, narrower column
- Gray decorative shape still present but scaled down and repositioned

---

## 8. Color Palette Summary

| Name | Hex | Usage |
|---|---|---|
| Teal | `#0097A7` | Nav bar, button bg, bottom accent bar |
| Gold/Amber | `#C9A84C` | Button border |
| Near-black | `#333333` | Body text |
| White | `#FFFFFF` | Nav text, hero title, button text |
| Light gray | `#E8E8E8` | Decorative blob shape |

---

## 9. Typography Summary

| Element | Font | Size (CSS px) | Weight | Case | Color |
|---|---|---|---|---|---|
| Nav items | Sans-serif | 18 | 400 | Sentence | `#FFF` |
| Hero title | Monospace/slab | 56 (mob: 28) | 700 | UPPERCASE | `#FFF` |
| Body text | Sans-serif | 20 (mob: 16) | 400 | Sentence | `#333` |
| Button text | Sans-serif | 20 | 400 | UPPERCASE | `#FFF` |
