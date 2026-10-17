# Pojok Berkah — README Utama (Root)

Landing site and PHP CMS for the Pojok Berkah service directory.

> ⚠️ **INI ADALAH README ROOT** — untuk pengguna & admin (Bahasa Inggris).
> Full documentation (Indonesian) is available in [`docs/INDEX-DOKUMEN.md`](docs/INDEX-DOKUMEN.md):
> - [01 — System Guide & Architecture](docs/01-panduan-sistem.md)
> - [02 — CMS User Guide & API Documentation](docs/02-panduan-pengguna-cms.md)
> - [03 — Contribution & Coding Standards](docs/03-panduan-kontribusi-style.md)
> - [04 — Development Roadmap](docs/04-roadmap.md)
> - [CHANGELOG](docs/CHANGELOG.md)

## Requirements

- PHP 8.0 or newer (production InfinityFree uses PHP 8.3)
- Apache with `mod_rewrite` for `.htaccess`
- Writable `data/` directory for CMS settings, article images and uploaded images
- HTTPS enabled for the production domain

## Local setup

1. Copy `data/storage.example.php` to `data/storage.php`.
2. Serve this directory through PHP; do not open the HTML files with `file://` when testing the CMS.
3. Open `admin.php` from localhost (`http://localhost/pojok_berkah/admin.php`) and create the admin password.
4. Set the WhatsApp number, review the service FAQ and images, then create or publish articles from the **Artikel** tab.

The CMS setup screen is intentionally limited to localhost while no password hash exists. After login, the CMS can manage the WhatsApp number, service names, URLs, descriptions and tags (4 slots: tryout/gadai/kitchen/parkir), service images, homepage statistics, FAQ, and articles. Published articles are available at `/artikel/` and `/artikel/{slug}.html`; drafts remain private.

The site is built with a **Zero-JavaScript architecture**: public pages are rendered on the server (SSR) via `render.php` and `admin/lib/public_render.php`, while interactive components (mobile drawer navigation, header scroll shadow, reveal transitions, floating WhatsApp button) are handled purely by CSS. Complete SEO features are implemented across all pages, including robots directives with `max-image-preview:large`, Open Graph, Twitter Cards, rich Schema.org structured data (Organization, WebSite, AboutPage, WebPage, Article, and auto-synchronized FAQPage), BreadcrumbList, and dynamic XML sitemaps.

The public top navigation is consistent on the homepage, About page, and article pages. It contains Beranda, Tentang, Keunggulan, Layanan, Artikel, FAQ, and Kontak. WhatsApp remains available in page CTAs and the mobile floating button, not in the top navigation.

## Production deployment (InfinityFree)

Free hosting runs PHP 8.3, which satisfies the PHP 8.0 requirement, and the CMS needs no database. Uploaded files are limited to 1 MB for HTML and PHP files and 10 MB for everything else, and that limit cannot be raised.

1. Add `pojokberkah.online` to the hosting account and point it at InfinityFree. Either set the nameservers to `ns1.infinityfree.com` and `ns2.infinityfree.com`, or keep the current nameservers and verify the domain with the CNAME record shown in the panel. Until DNS points here the domain answers with the registrar parking page.
2. Request the free SSL certificate for the domain in the control panel and wait until the HTTPS URL loads. Finish this step before uploading files, because `.htaccess` redirects HTTP to HTTPS.
3. Upload the tracked project files into `htdocs/`. Use the hosting file manager, an FTP/FTPS client (`ftpupload.net`, port 21, explicit FTPS), or the GitHub Actions workflow described below.
4. Create the production `data/storage.php`: run the CMS on localhost, set the production WhatsApp number and admin password, then upload the generated file privately. The setup screen only works from localhost, and the deploy workflow and `.gitignore` both exclude this file because it holds the admin password hash and live settings. Until it exists the homepage uses its built-in defaults and `/admin.php` shows the setup notice instead of the login form.
5. Keep `data/` writable by PHP so settings and images can be saved. If saving settings or uploading images fails, set `data/` to 755 and `data/storage.php` to 644, falling back to 777 and 666 only if the host requires it.
6. Verify `/admin.php` loads over HTTPS, direct requests to `data/storage.php` and other `data/` paths return 404, and the homepage shows the WhatsApp number stored in the CMS.

The `.htaccess` file in this repository redirects HTTP to HTTPS, serves `404.html` for missing pages, maps `/artikel/` and `/artikel/{slug}.html` to `artikel.php`, maps `sitemap.xml` to `sitemap.php` (dynamic, includes published articles), and blocks direct requests to `data/` and `_arsip/`. The HTTPS redirect skips `localhost` and `127.0.0.1` so local testing over HTTP keeps working, and it must not go live before the SSL certificate is active, otherwise the site will not load. The CMS logo and service images are limited to JPEG, PNG, or WebP and 5 MB each. A transparent PNG is recommended for the logo.

## Automatic deployment from GitHub

The workflow in `.github/workflows/deploy.yml` deploys pushes to `main` through explicit FTPS. Push deployment is disabled until the repository variable is enabled; manual dry-runs can be run first.

1. In the GitHub repository, open **Settings > Secrets and variables > Actions > Secrets** and add the FTP host, username and password. Either naming scheme is accepted, and the shorter names win when both exist:
	- `FTP_HOST` (or `INFINITYFREE_FTP_SERVER`): the FTP host shown in InfinityFree, commonly `ftpupload.net` (do not use the website domain).
	- `FTP_USER` (or `INFINITYFREE_FTP_USERNAME`): the FTP username from the hosting panel.
	- `FTP_PASS` (or `INFINITYFREE_FTP_PASSWORD`): the FTP password.
	- Optional `INFINITYFREE_FTP_SERVER_DIR` overrides the document root, which defaults to `htdocs/` and must keep its trailing slash.
2. Run **Actions > Deploy to InfinityFree > Run workflow** with `dry_run` enabled. Review the log to confirm files target the correct `htdocs/` directory and that the settings file and uploaded images are excluded.
3. In **Settings > Secrets and variables > Actions > Variables**, add `POJOK_BERKAH_DEPLOY_ENABLED` with value `true` only after the dry-run log is correct.
4. Run the workflow manually with `dry_run` disabled for the first actual deploy. Later pushes to `main` deploy automatically.

The workflow excludes `data/storage.php`, its example template, and uploaded image files from publishing and deletion. Keep production settings and images backed up on the hosting account; do not add them to Git or send FTP credentials through chat.

The action keeps a `.ftp-deploy-sync-state.json` file in `htdocs/` to remember what it already uploaded, and `.htaccess` hides that file from visitors. If a deploy claims nothing changed after you edited a file directly on the server, delete that state file and run the workflow again.
