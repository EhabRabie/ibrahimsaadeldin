# ibrahimsaadeldin.com

WordPress site files for Saad Eldin Law Firm (Astra + Elementor), without secrets.

- `wp-content/plugins/saadeldin-library/` – custom plugin: articles (native posts) and the YouTube `videos` library.
- `tools/migrate-old-content.php` – one-off local script used to convert the old content types.

## Not in this repository
- `wp-config.php` (database credentials and salts) – create it on the server from `wp-config-sample.php`.
- The database (export it from the old host with phpMyAdmin).
- `elementor-pro` – install it with your own licence.
- Cache and backup folders.
