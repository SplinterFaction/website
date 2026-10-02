# Bastion

An RTS / strategy game theme for **Simple Website Framework**.

Dark, angular, high-contrast. Built for a game site: key art, faction
dossiers, unit databases, screenshot galleries and a dev-log.

No web fonts. No imports. No build step. Nothing to remove before you can
start.

---

## Install

1. Copy the `bastion` folder into `themes/`.
2. In `config/config.php`, set:

   ```php
   $theme = "bastion";
   ```

3. Leave `theme: ''` in `themes/bastion/navigation-options.php`. StellarNav's
   `'light'` and `'dark'` presets reassign the same variables the theme
   controls, so setting one would stop the nav bar following the palette.

That's it. To also install the demo site, copy the contents of
`demo-pages/` into `pages/` (it includes `navigation.html` and four footer
columns).

---

## Layouts

Set one per page with a `pagelayout` line in the page's metadata block.

| `pagelayout` | What it does |
|---|---|
| `page-landing` | Front page. Full-height hero built from the page's own metadata, then free-form content bands. Suppresses the interior banner. |
| `page-html` | Standard page, HTML, capped at the reading measure. |
| `page-md` | Same, run through the Markdown parser. |
| `page-html-notitle` / `page-md-notitle` | Same again, no in-page title block. |
| `page-wide` | Title keeps its measure, body runs the full container. For unit tables, faction line-ups, galleries. |
| `page-blank` | No container, no measure, no chrome at all. |
| `postarchives-featured` | Newest post as a wide lead card, the rest as a grid. Lead appears on page 1 only. |
| `postarchives-styled` | Card grid, nine per page. |
| `postarchives` | Quiet list view, thumbnail beside text, eight per page. |
| `postarchives-notitle` | The list view without the in-page title. |

### The landing page

`page-landing` reads three fields from the page's own metadata block:

```html
<!-- pagetitle:Hold the line, or become the line -->
<!-- pageexcerpt:One or two sentences under the headline. -->
<!-- pageimage:themes/bastion/images/ph-hero.svg -->
```

- `pagetitle` becomes the `<h1 class="display">`
- `pageexcerpt` becomes the sub-line **and** the meta description
- `pageimage` becomes the hero background **and** the OpenGraph image

The buttons, the eyebrow and the facts strip under the hero are markup in
`page-landing.php` — edit them there. Everything below the hero comes from
the page file, full-bleed, so the page decides its own section order.

### The page banner

Every layout except `page-landing` and `page-blank` gets a short banner from
`header.php`, using that page's `pageimage` as the background. Because the
banner already shows the title, the layouts suppress their own `<h1>` — the
title appears exactly once. Delete the banner block from `header.php` and
the inline titles come back automatically.

---

## Placeholder images

`themes/bastion/images/` holds fourteen SVG placeholders. They exist to be
deleted. Each is a dark plate with a grid, corner brackets and a label
naming its intended size — all under 3.5 KB, no dependencies.

| File | Size | For |
|---|---|---|
| `ph-hero.svg` | 1920×900 | Landing hero |
| `ph-keyart.svg` | 1920×1080 | Key art, trailer still |
| `ph-banner.svg` | 1920×480 | Interior page banner |
| `ph-wide.svg` | 1600×600 | Wide plate |
| `ph-16x9.svg`, `ph-16x9-alt.svg` | 1280×720 | Screenshots |
| `ph-card.svg` | 800×500 | Card image |
| `ph-thumb.svg` | 640×360 | Post thumbnail, archive fallback |
| `ph-square.svg` | 800×800 | Square art |
| `ph-portrait.svg` | 800×1200 | Faction key art (2:3) |
| `ph-unit.svg` | 480×480 | Unit art |
| `ph-emblem.svg` | 320×320 | Faction emblem, site mark |
| `ph-map.svg` | 1200×1200 | Campaign map |
| `ph-avatar.svg` | 240×240 | Author avatar |

Swap the `src` and keep the wrapper — `.mediaframe` holds the aspect ratio,
so the layout does not move when a real screenshot replaces a placeholder.

---

## Tokens

Section 1 of `custom.css` is the only part most projects need to touch.
Change `--color-accent` and the whole site re-skins.

```css
--color-accent:  #ff9b21;   /* ember: buttons, brackets, rules   */
--color-signal:  #5fd0e8;   /* cold highlight: links, stats      */
--color-bg:      #0b0e12;
--color-surface: #131920;
--cut:           12px;      /* notched corner size; 0 = square   */
--heading-transform: uppercase;  /* set to none to turn it off  */
```

Spacing steps derive from base.css's `--space`, so changing that one value
rescales the whole theme proportionally.

The palette is deliberately dark at every scheme setting — a strategy game
site that flips to white next to its own key art looks broken. If you want a
light variant, reassign these same variables inside a
`@media (prefers-color-scheme: light)` block; no rule below depends on the
values.

---

## Components

Layout primitives follow the same names as the starter theme, so anything
you already know carries over.

**Structure** — `.band` (+ `-tight`, `-loose`, `-sunk`, `-raised`, `-line`,
`-line-b`, `-art`, `-grid`), `.wrap` (+ `-wide`, `-narrow`), `.stack`,
`.cluster`, `.bandhead`, `.row-flip`, `.row-stretch`, `.row-tight`

**Surfaces** — `.panel` (+ `-raised`, `-cut`, `-edge`), `.hud` (corner
brackets), `.lift` (hover raise), `.callout`, `.quotecard`

**Media** — `.mediaframe` with `.ratio-21x9` / `16x9` / `4x3` / `1x1` /
`3x4` / `2x3`, plus `.mediacaption`

**Game-specific** — `.statbar` / `.stat`, `.faction` / `.factionart` /
`.traits`, `.unit` / `.unitrole`, `.datatable`, `.roadmap` /
`.roadmapitem` (+ `.is-done`, `.is-active`), `.featurerow`, `.ctaband`

**Type** — `.display`, `.eyebrow`, `.rule`, `.mono`, `.tag`, `.accent`

**Archive** — `.dispatch`, `.dispatch-lead`, `.dispatchtitle`,
`.dispatchmeta`, `.dispatchexcerpt`, `.dispatchmore`, `.pagination`

**Categories** — `.dispatchtag` (chip on artwork), `.dispatchcats`
(`Filed in …` line), `.pagecategories` (chips on a post), `.catbar`
(filtered-archive strip)

Columns come from `css/flexgridsystem.css` (`row` / `column` /
`flex-basis-*`). The theme does not duplicate that. Note that `.column`
centres its text; every card component resets that itself.

The band is **not** called `.section` — the `page-*.php` templates already
emit `<div class="section group">`, so styling `.section` would pad the
inside of every page.

---

## Latest posts on any page

`partial-latest-dispatches.php` renders the newest posts as cards. Drop it
anywhere with the framework's php shortcode:

```html
[php] $limit = 3; include 'themes/bastion/partial-latest-dispatches.php'; [/php]
```

The page decides where the block sits; the theme decides what it looks like.
It takes an optional `$category` too — see Categories below.

---

## Archive behaviour

A post is picked up if it has `pagetitle`, `pagedate`, `pageimage` and
`pageexcerpt` — the framework's own requirement, kept so posts behave
identically under other themes. `pagecategory` is deliberately **not** part
of that check; requiring it would make every untagged post vanish from every
archive.

Two additions on top of the framework behaviour:

- A `pageimage` pointing at a file that isn't there falls back to
  `ph-thumb.svg` instead of printing an error into the page.
- Empty states and the category bar are worded for a filtered view.

---

## Categories

The theme supports the framework's `pagecategory` / `postcategory` system in
all four archive layouts, the landing-page partial, and on the posts
themselves.

### Tagging a post

```html
<!-- pagecategory: design, systems -->
```

Comma-separated, matched lowercase and trimmed. Optional — an untagged post
still appears in every unfiltered archive and belongs to the built-in
`uncategorized` category.

### Making a category page

A normal page using any archive layout, plus one tag:

```html
<!-- pagetitle:Engineering -->
<!-- pagelayout:postarchives-styled -->
<!-- postcategory: engineering -->
```

Save as `pages/engineering.html` and `/engineering` is that category's
archive. All four archive layouts honour `postcategory`, so you can give a
category the card grid, the lead-story grid or the quiet list.

### How the theme renders categories

| Where | What shows |
|---|---|
| Card artwork | The **first** category as a clickable chip. Untagged posts get no chip — a badge reading UNCATEGORIZED on every legacy post is noise, not information. |
| Card body | `Filed in X, Y` — only when a post has **more than one** category, since the chip already names the first. The lead card on `postarchives-featured` always spells the list out, because it has the room. |
| Plain list (`postarchives`) | Categories inline in the meta line, since that layout has no artwork chip. |
| A post's own page | Category chips under the byline, in `.pagecategories`. |
| A filtered archive | A `.catbar` strip naming the filter, linking back to the full archive and to that category's RSS feed. |

Category links are built from the framework's convention: the category
`engineering` links to `/engineering`.

> **Slug collisions.** Category slugs share a namespace with page names, so a
> category cannot reuse the slug of an existing content page. The demo site
> hits this: there is already a `pages/factions.html` faction dossier, so the
> lore post is tagged `lore` rather than `factions`. If a category chip lands
> on the wrong page, this is why.

### RSS

The framework exposes per-category feeds as `?rss=<category>`, and filtered
archive pages link their own feed automatically. `?rss` and `?rss=all` still
return everything. Because `index.php` handles RSS before `top-cache.php`
runs, a query string is safe on that URL specifically — which is not true
elsewhere on a cached site.

### Latest posts, narrowed

The landing-page partial takes an optional `$category`:

```html
[php] $limit = 3; $category = 'engineering';
      include 'themes/bastion/partial-latest-dispatches.php'; [/php]
```

---

## Notes

- **Forms need their own endpoint.** `top-cache.php` writes the page to disk
  and exits before `plugins.php` loads, so posting back to the same URL will
  not work. The signup band on the demo landing page is buttons only for
  this reason.
- **Focus rings.** Notched corners on primary buttons are drawn with a
  gradient rather than `clip-path`, because `clip-path` also trims the
  keyboard focus ring. `.btn-ghost` does use `clip-path` and therefore
  carries its own inset focus indicator.
- **Overflow.** `html` and `body` use `overflow-x: clip`, not `hidden` —
  `hidden` would turn the body into a scroll container and silently break
  the sticky nav.
- **A trailing row of two cards will stretch** to fill the row. That is
  `flex-grow: 1` in the framework grid, not the theme.
