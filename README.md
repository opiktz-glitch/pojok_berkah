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

## Production deployment

1. Upload the tracked project files to the hosting document root.
2. Create the production `data/storage.php` locally through the CMS setup, then upload it privately using the hosting file manager or FTP/SFTP. Never commit this file; it contains the admin password hash and live settings.
3. Keep `data/` writable by PHP so settings and images can be saved. Verify direct requests to `data/storage.php` return 404.
4. Enable HTTPS in the hosting panel and use the HTTPS URL for `/admin.php`.
5. Confirm the custom domain matches the canonical URLs in the HTML and `sitemap.xml`.

The site does not require `.htaccess`. Configure a custom 404 page in the hosting panel if supported. The CMS logo and service images are limited to JPEG, PNG, or WebP and 5 MB each. A transparent PNG is recommended for the logo.
