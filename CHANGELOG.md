# Changelog

All notable changes to WDOD Starter are documented here.
The format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/) and the project uses [Semantic Versioning](https://semver.org/).

## [1.0.0] - 2026-09-14

### Added
- Classic theme scaffold: header, footer, index, page, single, archive, search, 404, comments and a Full Width page template.
- `theme.json` design tokens (palette, type scale, spacing, layout sizes, custom radius/shadow) mapped to `--wdod-*` CSS custom properties.
- Vite 5 + Sass build pipeline producing `assets/dist/main.js`, `main.css`, `editor.css` and a manifest; PHP loader with manifest-less fallback and an admin notice when the build is missing.
- Template tags: `wdod_posted_on()`, `wdod_post_thumbnail()`, `wdod_breadcrumbs()` (WooCommerce / Yoast aware), `wdod_pagination()`, `wdod_entry_footer()`.
- ACF blocks **WDOD Hero** and **WDOD Testimonials** registered from `block.json`, with editor placeholders, scoped CSS and dependency-free scripts; "WDOD Blocks" inserter category; `acf-json` sync.
- ACF **Theme Settings** options page (footer text, phone, email, social links) with `wdod_option()` fallback to plain options when ACF is inactive.
- WooCommerce support: gallery features, theme wrappers, 3 columns / 12 per page, header mini-cart with AJAX fragments, shop sidebar, `content-product.php` override.
- WPML helpers (`wdod_current_lang()`, `wdod_translated_id()`, `wdod_language_switcher()`) and `wpml-config.xml`.
- Elementor Theme Builder locations (header, footer, single, archive) honoured by the templates; theme palette pushed to the Elementor kit on activation.
- Accessible mobile navigation (ARIA state, Escape/outside click to close, focus return).
- Coding standards tooling: `phpcs.xml.dist` (WordPress + PHPCompatibilityWP), Composer scripts, `.editorconfig`, `.nvmrc`.
- Translation template `languages/wdod-starter.pot`.
