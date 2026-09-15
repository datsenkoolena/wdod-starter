# WDOD Starter

A lean **classic WordPress starter theme** for agency work. It ships with a modern build pipeline, `theme.json` design tokens, ACF blocks, an ACF options page, WooCommerce templates, WPML helpers and Elementor Theme Builder support - and every one of those integrations is optional. Activate the theme on a bare WordPress install and it just works; add ACF PRO, WooCommerce, WPML or Elementor and the matching features switch on.

> Built as a portfolio piece: the code is written the way a client project would be, with WordPress Coding Standards, escaping everywhere, translation-ready strings and a documented hook surface.

## Features

- **Classic template hierarchy** - header, footer, index, page, single, archive, search, 404, comments, plus a *Full Width* page template for landing pages.
- **theme.json tokens** - palette, fluid type scale, spacing scale, content/wide widths and custom radius/shadow values, aliased to `--wdod-*` custom properties for the Sass layer.
- **Vite 5 + Sass** - one command builds `assets/dist/main.js`, `main.css` and `editor.css` (IIFE bundle, stable file names, manifest for cache busting). The compiled output is committed so the theme installs from a zip with no build step.
- **Graceful asset loading** - `wdod_asset()` reads the Vite manifest; without it the loader falls back to the stable file names, and if nothing is built administrators see a one-line notice.
- **ACF blocks** - `WDOD Hero` and `WDOD Testimonials`, registered from `block.json` with `register_block_type()`, editor placeholders, scoped CSS and vanilla JS (IntersectionObserver reveal, scroll-snap slider). Field groups sync through `acf-json/`.
- **Theme Settings page** - footer text, phone, email and social links via ACF options; `wdod_option()` keeps working when ACF is removed.
- **WooCommerce** - product gallery features, theme wrappers, 3 columns / 12 products per page, header mini-cart with AJAX fragments, shop sidebar, hook-preserving `content-product.php` override.
- **WPML** - `wdod_current_lang()`, `wdod_translated_id()`, `wdod_language_switcher()` and a `wpml-config.xml` covering block fields and options.
- **Elementor** - registers all Theme Builder locations; header/footer/single/archive templates hand off to Elementor Pro when a template matches; the theme palette is pushed to the Elementor kit on activation.
- **Accessibility** - skip link, ARIA-driven mobile menu with Escape and outside-click handling, screen-reader labels on icon links, reduced-motion support.
- **Tooling** - `phpcs.xml.dist` (WordPress + PHPCompatibilityWP, PHP 7.4+), Composer scripts, `.editorconfig`, `.nvmrc`, POT generation.

## Screenshots

| Home / blog index | WDOD Hero block in the editor |
| --- | --- |
| _screenshot placeholder_ | _screenshot placeholder_ |

## Requirements

| | Minimum |
| --- | --- |
| WordPress | 6.4 (tested up to 7.1) |
| PHP | 7.4 |
| Node.js (development only) | 20 (see `.nvmrc`) |
| Composer (development only) | 2.x |

Optional plugins: Advanced Custom Fields **PRO** (blocks + options page), WooCommerce, WPML, Elementor + Elementor Pro (Theme Builder).

## Installation

1. Download or clone this repository into `wp-content/themes/wdod-starter`.
2. In WordPress go to **Appearance > Themes** and activate **WDOD Starter**.
3. Assign menus under **Appearance > Menus** (`Primary Menu`, `Footer Menu`) and optionally add widgets to the **Footer** area.
4. Install ACF PRO to get the WDOD blocks and the **Theme Settings** page; the field groups are picked up automatically from `acf-json/`.

Installing from a zip works too: `assets/dist/` is committed, so no build is required on the server.

## Development

```bash
cd wp-content/themes/wdod-starter

nvm use              # Node 20
npm install
npm run build        # one-off production build to assets/dist
npm run dev          # rebuild on change (vite build --watch)
npm run pot          # regenerate languages/wdod-starter.pot

composer install     # PHPCS + WordPress Coding Standards
composer lint        # php -l on every PHP file
composer phpcs       # coding standards report
composer phpcbf      # auto-fix what can be fixed
```

`vite.config.js` outputs an IIFE bundle with stable names (`main.js`, `main.css`, `editor.css`) plus `assets/dist/.vite/manifest.json`. `inc/enqueue.php` prefers the manifest and falls back to the stable names.

## Structure

```
wdod-starter/
├── style.css                 Theme header only (no CSS)
├── theme.json                Design tokens and editor settings
├── functions.php             Constants + loads inc/*
├── inc/
│   ├── setup.php             Theme supports, menus, image sizes, widgets
│   ├── enqueue.php           wdod_asset(), manifest loader, missing-build notice
│   ├── template-tags.php     wdod_posted_on(), wdod_post_thumbnail(), wdod_breadcrumbs(), wdod_pagination()
│   ├── acf-blocks.php        Block registration, "WDOD Blocks" category, acf-json paths, wdod_get_field()
│   ├── theme-options.php     ACF options page, wdod_option()
│   ├── woocommerce.php       Returns early without WooCommerce
│   ├── wpml.php              WPML helpers (no-ops without WPML)
│   └── elementor.php         Theme Builder locations
├── template-parts/           content.php, content-none.php, header/, footer/
├── page-templates/           full-width.php
├── blocks/acf/               hero/, testimonials/ (block.json, render.php, style.css, script.js)
├── acf-json/                 Field group exports (Hero, Testimonials, Theme Settings)
├── woocommerce/              content-product.php override
├── assets/src/               scss/ and js/ sources
├── assets/dist/              Built output (committed)
├── languages/                wdod-starter.pot
├── wpml-config.xml
└── package.json, vite.config.js, composer.json, phpcs.xml.dist
```

## ACF blocks: adding a new one

1. Create `blocks/acf/<name>/` with `block.json`, `render.php`, and optionally `style.css` / `script.js`. Use `wdod/<name>` as the block name and `"category": "wdod"`.
2. In `block.json` set `"acf": { "mode": "preview", "renderTemplate": "render.php" }` and reference assets with `"style": "file:./style.css"`, `"script": "file:./script.js"`.
3. In `render.php` read values with `wdod_get_field()` and build the wrapper with `wdod_block_wrapper_attributes( $block, 'wdod-<name>' )`. Show placeholder content when `$is_preview` is true and the fields are empty.
4. Create the field group in ACF with location **Block = WDOD <Name>**; it is saved to `acf-json/` automatically.
5. If the block has translatable fields add them to `wpml-config.xml`.

`inc/acf-blocks.php` picks up every folder with a `block.json` on `init`, so there is nothing to register by hand.

## WooCommerce notes

- `inc/woocommerce.php` is skipped entirely when the `WooCommerce` class does not exist.
- Default content wrappers are replaced with `<main id="primary" class="wdod-main wdod-main--woocommerce">` + `.wdod-container` so shop pages share the theme layout.
- The header cart link is rendered by `wdod_woocommerce_cart_link()` and refreshed through `woocommerce_add_to_cart_fragments`.
- `woocommerce/content-product.php` keeps all standard hooks; only the `<li>` gets an extra `wdod-product-card` class. Check the `@version` header against the WooCommerce template version after Woo updates.
- A **Shop Sidebar** widget area is rendered after the archive loop.

## WPML notes

- `wpml-config.xml` marks the Hero/Testimonials block fields and the Theme Settings options as translatable via String Translation / the Translation Editor.
- `wdod_translated_id( $id, 'page' )` returns the translated object ID, or the original when WPML is inactive.
- `wdod_language_switcher()` prints the WPML selector only when the `wpml_add_language_selector` action exists.

## Elementor notes

- `elementor/theme/register_locations` registers header, footer, single and archive.
- `header.php` / `footer.php` call `elementor_theme_do_location()` and fall back to the theme's template parts. `single.php` / `archive.php` do the same for their locations.
- The *Full Width* page template has no inner container, which suits Elementor pages and full-width block layouts.
- On theme activation the `theme.json` palette is added to the Elementor kit as custom colours (only if the kit has none yet).

## Hooks

| Hook | Type | Description |
| --- | --- | --- |
| `wdod_breadcrumb_items` | filter | Array of `['label' => ..., 'url' => ...]` items before the theme's own breadcrumbs render. |
| `wdod_option` | filter | Value returned by `wdod_option( $key )`; receives `$value, $key`. |
| `loop_shop_columns`, `loop_shop_per_page` | filter (core Woo) | Theme sets 3 and 12; override with a higher priority. |
| `woocommerce_show_page_title` | filter (core Woo) | Theme returns `false` so the shop page title is not duplicated. |
| `block_categories_all` | filter (core) | Theme adds the `wdod` category. |
| `acf/settings/save_json`, `acf/settings/load_json` | filter (ACF) | Point at `acf-json/`. |

Public helper functions: `wdod_asset()`, `wdod_asset_path_relative()`, `wdod_get_field()`, `wdod_option()`, `wdod_social_links()`, `wdod_current_lang()`, `wdod_translated_id()`, `wdod_language_switcher()`, `wdod_breadcrumbs()`, `wdod_pagination()`, `wdod_posted_on()`, `wdod_post_thumbnail()`, `wdod_entry_footer()`, `wdod_block_wrapper_attributes()`.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

GPL-2.0-or-later. See `style.css` for the header.
