# Fix: Network Error with Embed Widget from Other Websites

## Problem

When embedding the Flip Menu widget on an external website, you see:
```
Error: Network error - Cannot connect to server. Check CORS and internet connection.
```

## Root Cause

This is a **CORS (Cross-Origin Resource Sharing)** issue. Browsers block requests from one domain to another for security reasons unless the server explicitly allows it.

## Quick Fix (5 Steps)

### Step 1: Enable API Access

1. Log into your WordPress admin
2. Go to **Flip Menu > API & Embed**
3. Check ✅ **"Enable API"**
4. Click **"Save API Settings"**

### Step 2: Enable CORS

1. On the same page, check ✅ **"Enable CORS"**
2. In **"Allowed Origins"**, enter `*` (allow all domains)
   - Or enter specific domain: `https://yourexternalsite.com`
3. Click **"Save API Settings"**

### Step 3: Test API

1. Scroll to **"API Endpoints"** section
2. Click any **"Test"** button
3. You should see JSON data (not an error)

### Step 4: Flush WordPress Permalinks

1. Go to **Settings > Permalinks**
2. Don't change anything, just click **"Save Changes"**
3. This ensures REST API routes are registered

### Step 5: Test Widget Again

1. Refresh your external website
2. The widget should now load properly

---

## Detailed Solutions

### Solution 1: CORS Configuration

The most common issue is CORS not being properly configured.

**Check these settings in WordPress Admin > Flip Menu > API & Embed:**

| Setting | Value | Why |
|---------|-------|-----|
| Enable API | ✅ Checked | Allows external access |
| Enable CORS | ✅ Checked | Required for cross-domain requests |
| Allowed Origins | `*` or your domain | Controls which websites can access |

**Allowed Origins Examples:**

```
# Allow all domains (easiest but less secure)
*

# Allow specific domain
https://example.com

# Allow multiple domains (comma-separated)
https://example.com,https://another.com

# Allow subdomains (wildcard)
*.example.com
```

---

### Solution 2: Server Configuration

Some hosting providers block REST API access. Check with your host.

**Add to `.htaccess` (Apache):**

```apache
# Enable CORS for REST API
<IfModule mod_headers.c>
    <FilesMatch "\.(php)$">
        Header set Access-Control-Allow-Origin "*"
        Header set Access-Control-Allow-Methods "GET, POST, OPTIONS"
        Header set Access-Control-Allow-Headers "X-API-Key, Content-Type, Authorization"
    </FilesMatch>
</IfModule>
```

**Add to `nginx.conf` (Nginx):**

```nginx
location /wp-json/ {
    add_header Access-Control-Allow-Origin *;
    add_header Access-Control-Allow-Methods "GET, POST, OPTIONS";
    add_header Access-Control-Allow-Headers "X-API-Key, Content-Type, Authorization";

    if ($request_method = 'OPTIONS') {
        return 204;
    }
}
```

---

### Solution 3: WordPress REST API Issues

**Check if REST API is working:**

Visit this URL in your browser:
```
https://yoursite.com/wp-json/
```

You should see JSON data. If you see an error, the REST API is disabled.

**Enable REST API:**

Add to `wp-config.php`:
```php
// Enable REST API
define('REST_REQUEST', true);
```

**Check for blocking plugins:**

Some security plugins block REST API. Temporarily disable:
- Wordfence
- iThemes Security
- All In One WP Security

---

### Solution 4: SSL/HTTPS Issues

**Mixed Content Error:**

If your WordPress site is HTTPS but external site is HTTP (or vice versa), browsers block the request.

**Fix:**
- Ensure BOTH sites use HTTPS
- Or both use HTTP (not recommended)

**Check in browser console:**
```
Mixed Content: The page at 'http://external-site.com' was loaded over HTTP,
but requested an insecure XMLHttpRequest endpoint 'https://wordpress-site.com/wp-json/...'.
This request has been blocked.
```

---

### Solution 5: Firewall / CDN Issues

**Cloudflare:**
1. Go to Cloudflare Dashboard
2. Security > WAF
3. Add rule to allow `/wp-json/flip-menu/*`

**Sucuri:**
1. Go to Sucuri Dashboard
2. Whitelist `/wp-json/flip-menu/*`

**ModSecurity:**
Add to `.htaccess`:
```apache
<IfModule mod_security.c>
    SecRuleRemoveById 981173
    SecRuleRemoveById 950109
</IfModule>
```

---

## Testing Guide

### Test 1: Direct API Access

Open browser console and run:

```javascript
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops')
  .then(r => r.json())
  .then(d => console.log('Success:', d))
  .catch(e => console.error('Error:', e));
```

**Expected:** JSON data with shops
**Error:** Check API is enabled

### Test 2: With API Key

```javascript
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops', {
  headers: { 'X-API-Key': 'your-api-key' }
})
  .then(r => r.json())
  .then(d => console.log('Success:', d))
  .catch(e => console.error('Error:', e));
```

**Expected:** JSON data
**Error:** Check API key is correct

### Test 3: CORS Headers

```javascript
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops', {
  method: 'OPTIONS'
})
  .then(r => {
    console.log('CORS Headers:', r.headers.get('Access-Control-Allow-Origin'));
  });
```

**Expected:** Should show allowed origin (`*` or your domain)
**Error:** CORS not enabled

### Test 4: Widget on External Site

Create `test.html` on external domain:

```html
<!DOCTYPE html>
<html>
<head>
    <title>Widget Test</title>
</head>
<body>
    <h1>Testing Flip Menu Widget</h1>

    <div data-flip-menu-widget
         data-shop-id="1"
         data-api-url="https://yourwordpresssite.com/wp-json"
         data-width="800"
         data-height="600">
    </div>

    <script src="https://yourwordpresssite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>

    <script>
        // Check for errors
        window.addEventListener('error', function(e) {
            console.error('Page Error:', e);
        });
    </script>
</body>
</html>
```

---

## Common Error Messages

### Error: "CORS error: Cannot connect to API"

**Cause:** CORS not enabled or origin not allowed

**Fix:**
1. Enable CORS in settings
2. Add domain to allowed origins
3. Use `*` to allow all

### Error: "Authentication failed"

**Cause:** Invalid API key or CORS blocking

**Fix:**
1. Check API key is correct
2. Try without API key (public access)
3. Verify CORS settings

### Error: "API endpoint not found"

**Cause:** Permalinks not configured or plugin not activated

**Fix:**
1. Flush permalinks (Settings > Permalinks > Save)
2. Check plugin is activated
3. Verify REST API is working

### Error: "Failed to parse JSON response"

**Cause:** Server returning HTML instead of JSON (often 404 or 500 error)

**Fix:**
1. Check URL is correct
2. Verify shop ID exists
3. Check server error logs

---

## Debugging Checklist

Use this checklist to debug the issue:

- [ ] API is enabled in WordPress admin
- [ ] CORS is enabled
- [ ] Allowed origins is set to `*` or includes your domain
- [ ] REST API is accessible at `/wp-json/`
- [ ] Plugin is activated
- [ ] Permalinks are flushed
- [ ] Both sites use HTTPS (or both HTTP)
- [ ] No security plugins blocking REST API
- [ ] No firewall rules blocking requests
- [ ] Browser console shows detailed error
- [ ] Test API directly in browser works

---

## Advanced Debugging

### Enable WordPress Debug Mode

Add to `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Check `/wp-content/debug.log` for errors.

### Check Server Logs

**Apache:** `/var/log/apache2/error.log`
**Nginx:** `/var/log/nginx/error.log`

### Browser Network Tab

1. Open browser DevTools (F12)
2. Go to Network tab
3. Try loading widget
4. Find the API request
5. Check:
   - Status code
   - Response headers
   - Response body
   - Request headers

### Use cURL to Test

```bash
# Test basic endpoint
curl -v https://yoursite.com/wp-json/flip-menu/v1/shops

# Test with API key
curl -v https://yoursite.com/wp-json/flip-menu/v1/shops \
  -H "X-API-Key: your-key"

# Test CORS preflight
curl -v https://yoursite.com/wp-json/flip-menu/v1/shops \
  -H "Origin: https://external-site.com" \
  -H "Access-Control-Request-Method: GET" \
  -X OPTIONS
```

---

## Still Not Working?

### 1. Contact Your Hosting Provider

Ask them to:
- Enable REST API access
- Allow CORS headers
- Check firewall rules
- Review server logs

### 2. Try Different Browser

Test in:
- Chrome (incognito mode)
- Firefox (private window)
- Safari

### 3. Test Locally

Test the widget on the same domain first:
- Add widget to a WordPress page
- If it works there, it's a CORS issue
- If it doesn't work, it's a plugin issue

### 4. Check WordPress Version

Minimum requirements:
- WordPress 5.0+
- PHP 7.0+
- REST API enabled

### 5. Use Browser Extensions

Temporarily disable:
- Ad blockers
- Privacy extensions
- CORS blockers

---

## Quick Reference: API Settings

Copy this configuration (WordPress Admin > Flip Menu > API & Embed):

```
✅ Enable API: CHECKED
✅ Enable CORS: CHECKED
Allowed Origins: *
API Key: (optional - leave empty for public access)
```

---

## Summary

**Most common fix:**
1. Go to Flip Menu > API & Embed
2. Check "Enable API" ✅
3. Check "Enable CORS" ✅
4. Set Allowed Origins to `*`
5. Save settings
6. Refresh external website

**If still not working:**
- Flush permalinks (Settings > Permalinks > Save)
- Check REST API works: visit `/wp-json/`
- Ensure both sites use HTTPS
- Disable security plugins temporarily

**Get more help:**
- Check browser console for detailed errors
- Review server error logs
- Test API directly with cURL
- Contact hosting provider

---

The network error is almost always a CORS configuration issue. Following the steps above should resolve it in 99% of cases!
