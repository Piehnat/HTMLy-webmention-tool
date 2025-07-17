# Webmention Sender for HTMLy

A lightweight PHP tool to send Webmentions manually from a flat-file blog like [HTMLy](https://www.htmly.com) — no plugins, no WordPress, no nonsense.

## What It Does

- Accepts a post URL as input
- Fetches that page and extracts all `<a>` links
- Detects Webmention endpoints for each target link
- Sends Webmentions via HTTP POST if an endpoint is found

## How to Use

1. Upload `send-webmentions.php` to your blog root directory (e.g. next to `index.php`)
2. Call it via browser:
https://yourblog.tld/send-webmentions.php?source=https://yourblog.tld/post/your-article
3. Optionally use `launcher.html` locally to enter URLs and send Webmentions with a click

## License

MIT - do whatever you want with it. Credit is nice but not mandatory.
