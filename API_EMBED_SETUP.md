# Quick Start: API & Embed Widget Setup

## Overview

This guide helps you quickly set up the Flip Menu API and embed widget so external websites can display your menus.

## 5-Minute Setup

### Step 1: Enable API Access

1. Log in to WordPress admin
2. Go to **Flip Menu > API & Embed**
3. Check **"Enable API"**
4. Click **"Generate New Key"** (optional but recommended)
5. Check **"Enable CORS"**
6. Set **Allowed Origins** to `*` (allow all) or specific domains
7. Click **"Save API Settings"**

### Step 2: Test API

On the same page, scroll to **"API Endpoints"** and click any **"Test"** button.

You should see a JSON response with your shop data.

### Step 3: Generate Embed Code

1. Scroll to **"Embed Widget Generator"**
2. Select a shop from the dropdown
3. Adjust width/height if needed
4. Click **"Copy Embed Code"**

### Step 4: Use on External Site

Paste the embed code into any HTML page:

```html
<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-api-key="your-generated-key"
     data-width="800"
     data-height="600">
</div>
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

## Common Use Cases

### Use Case 1: Public Restaurant Directory

**Scenario:** You run a restaurant directory site and want to show menus from multiple restaurants.

**Setup:**
1. Keep API key empty for public access
2. Set CORS to `*`
3. Each restaurant gets their own embed code with their `shop_id`

**Example:**
```html
<!-- Restaurant A -->
<h2>Pizza Palace</h2>
<div data-flip-menu-widget data-shop-id="1" data-api-url="https://yoursite.com/wp-json"></div>

<!-- Restaurant B -->
<h2>Burger Bar</h2>
<div data-flip-menu-widget data-shop-id="2" data-api-url="https://yoursite.com/wp-json"></div>

<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

### Use Case 2: Partner Website Integration

**Scenario:** A specific partner website needs to display your menus.

**Setup:**
1. Generate API key for security
2. Set CORS to partner's domain: `https://partner-site.com`
3. Provide partner with embed code including API key

**Example:**
```html
<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-api-key="abc123xyz789"
     data-width="1000"
     data-height="700">
</div>
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

### Use Case 3: Mobile App Integration

**Scenario:** You have a mobile app that needs menu data.

**Setup:**
1. Generate API key
2. Enable CORS with `*` or your app's domain
3. Use REST API endpoints directly

**Example (JavaScript):**
```javascript
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete', {
  headers: {
    'X-API-Key': 'your-api-key'
  }
})
.then(res => res.json())
.then(data => {
  const shop = data.data.shop;
  const items = data.data.items;
  // Display in your app
});
```

### Use Case 4: Build Your Own Viewer

**Scenario:** You want to create a custom menu viewer with your own design.

**Setup:**
1. Use API to fetch menu data
2. Build custom HTML/CSS/JS viewer
3. Don't use the widget script

**Example:**
```html
<!DOCTYPE html>
<html>
<head>
    <title>Custom Menu Viewer</title>
    <style>
        .menu-viewer { display: flex; gap: 20px; }
        .menu-page { border: 1px solid #ddd; padding: 10px; }
        .menu-page img { max-width: 400px; }
    </style>
</head>
<body>
    <div id="menu-container"></div>

    <script>
        fetch('https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete?api_key=your-key')
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('menu-container');
                const shop = data.data.shop;
                const items = data.data.items;

                container.innerHTML = '<h1>' + shop.name + '</h1>';

                const viewer = document.createElement('div');
                viewer.className = 'menu-viewer';

                items.forEach(item => {
                    const page = document.createElement('div');
                    page.className = 'menu-page';
                    page.innerHTML = '<img src="' + item.source_url + '" alt="' + item.title + '">';
                    viewer.appendChild(page);
                });

                container.appendChild(viewer);
            });
    </script>
</body>
</html>
```

## Security Considerations

### When to Use API Keys

**Use API Key When:**
- Limiting access to specific partners
- Tracking/monitoring API usage
- Production environments
- Sensitive menu data

**Don't Need API Key When:**
- Public restaurant directory
- Testing/development
- Non-sensitive data
- Maximum accessibility needed

### CORS Settings

**Allow All (`*`):**
- ✅ Good for: Public directories, maximum reach
- ⚠️ Caution: Anyone can access your API

**Specific Domains:**
- ✅ Good for: Partner integrations, controlled access
- ⚠️ Note: Must list all allowed domains

**Example:**
```
https://partner1.com,https://partner2.com,https://partner3.com
```

## Troubleshooting

### Problem: Widget Shows "Error: Failed to connect to API"

**Solutions:**
1. Check API is enabled in **Flip Menu > API & Embed**
2. Verify the `data-api-url` is correct
3. Check CORS is enabled
4. If using API key, verify it's correct
5. Check browser console for specific errors

### Problem: API Returns 403 Forbidden

**Causes:**
- API is disabled
- Invalid API key
- CORS not allowed for requesting domain

**Fix:**
1. Enable API access
2. Verify API key matches
3. Add requesting domain to allowed origins

### Problem: Widget Shows but Images Don't Load

**Causes:**
- Images are on HTTPS but site is HTTP (mixed content)
- Image URLs are incorrect
- CORS blocking image loading

**Fix:**
1. Ensure both sites use HTTPS
2. Check image URLs in database
3. Configure server to allow image loading

### Problem: Turn.js Animation Not Working

**Cause:** Turn.js library not loaded or jQuery missing

**Fix:**
```html
<!-- Add before widget script -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="path/to/turn.min.js"></script>

<!-- Then load widget -->
<script src="https://yoursite.com/.../flip-menu-widget.js"></script>
```

### Problem: Multiple Widgets Conflicting

**Cause:** Widget script loaded multiple times

**Fix:**
Load the widget script only once at the end:
```html
<!-- Widget 1 -->
<div data-flip-menu-widget data-shop-id="1" ...></div>

<!-- Widget 2 -->
<div data-flip-menu-widget data-shop-id="2" ...></div>

<!-- Widget 3 -->
<div data-flip-menu-widget data-shop-id="3" ...></div>

<!-- Load script ONCE -->
<script src=".../flip-menu-widget.js"></script>
```

## Testing Checklist

Before going live, test these:

- [ ] API returns data correctly
- [ ] Widget displays on test page
- [ ] Images load properly
- [ ] Navigation buttons work
- [ ] Responsive on mobile
- [ ] Works on partner domains (if applicable)
- [ ] API key authentication works (if used)
- [ ] CORS headers present
- [ ] No console errors
- [ ] Performance is acceptable

## Performance Tips

### 1. Cache API Responses

Don't fetch on every page load:

```javascript
// Cache for 1 hour
const cacheKey = 'menu_shop_1';
const cacheTime = 3600000; // 1 hour in ms

let cached = localStorage.getItem(cacheKey);
let cacheTimestamp = localStorage.getItem(cacheKey + '_time');

if (cached && (Date.now() - cacheTimestamp < cacheTime)) {
    // Use cached data
    displayMenu(JSON.parse(cached));
} else {
    // Fetch fresh data
    fetch('...').then(data => {
        localStorage.setItem(cacheKey, JSON.stringify(data));
        localStorage.setItem(cacheKey + '_time', Date.now());
        displayMenu(data);
    });
}
```

### 2. Optimize Images

Before uploading menu images:
- Resize to maximum 1200px width
- Compress to 80-90% quality
- Use JPG for photos, PNG for graphics
- Consider WebP format

### 3. Lazy Load Widgets

Load widgets only when scrolled into view:

```javascript
// Intersection Observer for lazy loading
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            // Load widget when visible
            FlipMenuWidget.initWidget(entry.target);
            observer.unobserve(entry.target);
        }
    });
});

document.querySelectorAll('[data-flip-menu-widget]').forEach(el => {
    observer.observe(el);
});
```

### 4. Use CDN

Host widget script on CDN for faster loading:
```html
<script src="https://cdn.yoursite.com/flip-menu-widget.js"></script>
```

## Advanced Customization

### Custom Styling

Override widget styles:

```css
/* Custom container background */
.flip-menu-widget-container {
    background: #1a1a1a !important;
    border-radius: 15px !important;
}

/* Custom button colors */
.flip-menu-widget-btn {
    background: #ff6600 !important;
}

.flip-menu-widget-btn:hover {
    background: #ff8800 !important;
}

/* Custom title */
.flip-menu-widget-title {
    font-family: 'Georgia', serif !important;
    color: #ff6600 !important;
}
```

### Custom Events

Listen to widget events:

```javascript
// After widget loads
window.addEventListener('flipMenuLoaded', function(e) {
    console.log('Menu loaded:', e.detail);
});

// When page changes
window.addEventListener('flipMenuPageChange', function(e) {
    console.log('Page changed to:', e.detail.page);
});
```

## Next Steps

1. **Read full documentation:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
2. **Check Turn.js setup:** [TURN_JS_SETUP.md](TURN_JS_SETUP.md)
3. **Review main README:** [README.md](README.md)
4. **Test thoroughly** before production use
5. **Monitor API usage** and performance
6. **Keep plugin updated** for security

## Support

Need help?
- Check the troubleshooting section above
- Review API documentation
- Test endpoints in admin panel
- Check browser console for errors
- Verify WordPress error logs

---

**Remember:** The widget works standalone without Turn.js, but Turn.js adds the flip animation effect!
