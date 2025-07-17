# 🔗 Webmention Sender for HTMLy

A lightweight, secure PHP tool to send [Webmentions](https://www.w3.org/TR/webmention/) manually from a flat-file blog like [HTMLy](https://www.htmly.com) — no plugins, no WordPress, no nonsense.

---

## 🔧 What It Does

- Accepts a post URL as input via the `source` parameter  
- Requires a secret code (`auth`) for protection  
- Loads the source page via **cURL** (not `file_get_contents`)  
- Extracts all `<a href="...">` links from the HTML  
- For each link:
  - Detects Webmention endpoints via HTTP `Link` header or `<link rel="webmention">`
  - Sends a Webmention via HTTP POST if an endpoint is found  
- Adds a 2-second delay to slow down brute-force attempts  
- Logs every access (successful or failed) with IP and timestamp to `webmention-log.txt`  
- Displays all results in a simple HTML page (success, failure, or "no endpoint found")

---

## 🚀 How To Use

1. **Upload** `send-webmentions.php` to your blog root (same place as `index.php`)
2. **Call it** in your browser like so:
https://yourblog.tld/send-webmentions.php?source=https://yourblog.tld/post/your-article&auth=your-secret-code
3. **Result:** A clean HTML output showing what was sent, what failed, and what had no endpoint

---

## 🖱 Optional: Webmention Launcher

Use `launcher.html` for convenience.  
It’s a local HTML form with fields for article URL and auth code — hit the button, done.

---

## 🔐 Security

- Password-protected via `auth` GET parameter  
- Access attempts (valid and failed) are logged to `webmention-log.txt`  
- Includes `sleep(2)` delay to slow down brute-force attacks  
- No sessions, no database — just plain PHP

---

## 📁 Files

- `send-webmentions.php` — the actual sender script  
- `launcher.html` — (optional) visual launcher to simplify usage  
- `webmention-log.txt` — log file created automatically on first use

---

## 📝 License

MIT — Do whatever you want. Credit is nice, but not required.




