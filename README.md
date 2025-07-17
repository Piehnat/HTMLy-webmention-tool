# Webmention Sender for HTMLy

A lightweight PHP tool to manually send Webmentions from a flat-file blog like HTMLy — no plugins, no WordPress, no nonsense.

## 🔧 What It Does

- Accepts a post URL as input (`source`) and a secret code (`auth`)
- Fetches the post content using **cURL**
- Extracts all `<a href="...">` links from the HTML
- For each link, checks for a Webmention endpoint (via HTTP header or HTML)
- Sends a Webmention via HTTP POST if an endpoint is found
- Shows a clear HTML output: success, failure, or missing endpoint

## ✅ Why This Version?

- Uses **cURL** instead of `file_get_contents()` — works on most shared hosts like Strato
- Includes a **basic password protection** via `auth=your-secret-code`
- Prevents abuse — nobody can spam your Webmention script

## 🚀 How to Use

1. Upload `send-webmentions.php` to your blog root directory (e.g. next to `index.php`)
2. Call it like this in your browser:
https://yourblog.tld/send-webmentions.php?source=https://yourblog.tld/post/your-article&auth=YOUR_SECRET
3. Optionally: Use `launcher.html` locally in your browser — enter your post URL and auth code, then click to send

## 🖥 Screenshot

<img src="https://piehnat.de/content/images/20250717104716-Bildschirmfoto%20vom%202025-07-17%2010-46-13.png" alt="Webmention output screenshot" width="500" />

## 🛡 Security

This script won't do anything unless you pass the correct `auth` code. Without it, it returns:
Unauthorized access


## 📄 License

MIT – do whatever you want. Attribution appreciated but not required.



