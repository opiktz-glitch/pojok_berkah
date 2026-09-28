# Pojok Berkah

Landing site and PHP CMS for the Pojok Berkah service directory.

## Requirements

- PHP 8.0 or newer
- A PHP-capable Apache host (for example, InfinityFree)
- Writable `data/` directory for CMS settings and uploaded images
- HTTPS enabled for the production domain

## Local setup

1. Copy `data/storage.example.php` to `data/storage.php`.
2. Serve this directory through PHP; do not open the HTML files with `file://` when testing the CMS.
3. Open `admin.php` from localhost and create the admin password.
4. Set the WhatsApp number and review the service FAQ and images.

The CMS setup screen is intentionally limited to localhost while no password hash exists. After login, the CMS can manage the WhatsApp number, service names, URLs, descriptions and tags, service images, homepage statistics, and FAQ.

## Production deployment (InfinityFree)

Free hosting runs PHP 8.3, which satisfies the PHP 8.0 requirement, and the CMS needs no database. Uploaded files are limited to 1 MB for HTML and PHP files and 10 MB for everything else, and that limit cannot be raised.

1. Add `pojokberkah.online` to the hosting account and point it at InfinityFree. Either set the nameservers to `ns1.infinityfree.com` and `ns2.infinityfree.com`, or keep the current nameservers and verify the domain with the CNAME record shown in the panel. Until DNS points here the domain answers with the registrar parking page.
2. Request the free SSL certificate for the domain in the control panel and wait until the HTTPS URL loads. Finish this step before uploading files, because `.htaccess` redirects HTTP to HTTPS.
3. Upload the tracked project files into `htdocs/`. Use the hosting file manager, an FTP/FTPS client (`ftpupload.net`, port 21, explicit FTPS), or the GitHub Actions workflow described below.
4. Create the production `data/storage.php`: run the CMS on localhost, set the production WhatsApp number and admin password, then upload the generated file privately. The setup screen only works from localhost, and the deploy workflow and `.gitignore` both exclude this file because it holds the admin password hash and live settings. Until it exists the homepage uses its built-in defaults and `/admin.php` shows the setup notice instead of the login form.
5. Keep `data/` writable by PHP so settings and images can be saved. If saving settings or uploading images fails, set `data/` to 755 and `data/storage.php` to 644, falling back to 777 and 666 only if the host requires it.
6. Verify `/admin.php` loads over HTTPS, direct requests to `data/storage.php` and other `data/` paths return 404, and the homepage shows the WhatsApp number stored in the CMS.

The `.htaccess` file in this repository redirects HTTP to HTTPS, serves `404.html` for missing pages, and blocks direct requests to `data/`. The HTTPS redirect skips `localhost` and `127.0.0.1` so local testing over HTTP keeps working, and it must not go live before the SSL certificate is active, otherwise the site will not load. The CMS logo and service images are limited to JPEG, PNG, or WebP and 5 MB each. A transparent PNG is recommended for the logo.

## Automatic deployment from GitHub

The workflow in `.github/workflows/deploy.yml` deploys pushes to `main` through explicit FTPS. Push deployment is disabled until the repository variable is enabled; manual dry-runs can be run first.

1. In the GitHub repository, open **Settings > Secrets and variables > Actions > Secrets** and add:
	- `INFINITYFREE_FTP_SERVER`: the FTP host shown in InfinityFree, commonly `ftpupload.net` (do not use the website domain).
	- `INFINITYFREE_FTP_USERNAME`: the FTP username from the hosting panel.
	- `INFINITYFREE_FTP_PASSWORD`: the FTP password.
	- `INFINITYFREE_FTP_SERVER_DIR`: the site's document root, usually `htdocs/`, with a trailing slash.
2. Run **Actions > Deploy to InfinityFree > Run workflow** with `dry_run` enabled. Review the log to confirm files target the correct `htdocs/` directory and that the settings file and uploaded images are excluded.
3. In **Settings > Secrets and variables > Actions > Variables**, add `POJOK_BERKAH_DEPLOY_ENABLED` with value `true` only after the dry-run log is correct.
4. Run the workflow manually with `dry_run` disabled for the first actual deploy. Later pushes to `main` deploy automatically.

The workflow excludes `data/storage.php`, its example template, and uploaded image files from publishing and deletion. Keep production settings and images backed up on the hosting account; do not add them to Git or send FTP credentials through chat.

The action keeps a `.ftp-deploy-sync-state.json` file in `htdocs/` to remember what it already uploaded, and `.htaccess` hides that file from visitors. If a deploy claims nothing changed after you edited a file directly on the server, delete that state file and run the workflow again.
