# API & Embed Features Implementation Summary

## What Was Added

Complete REST API and embeddable widget system for the Flip Menu WordPress plugin, allowing external websites to access and display flip menus.

## New Features

### 1. REST API

**Full REST API with 5 endpoints:**
- `GET /shops` - List all shops
- `GET /shops/{id}` - Get single shop details
- `GET /shops/{id}/menu` - Get menu items for a shop
- `GET /shops/{id}/complete` - Get shop with all menu items (optimized)
- `GET /verify` - Verify API key validity

**Features:**
- ✅ RESTful architecture
- ✅ JSON responses
- ✅ API key authentication
- ✅ CORS support
- ✅ Error handling
- ✅ Header or query parameter auth
- ✅ Optional public access

### 2. Embeddable Widget

**Standalone JavaScript widget:**
- ✅ No dependencies required (jQuery optional)
- ✅ Simple HTML embed code
- ✅ Automatic initialization
- ✅ Multiple widgets per page
- ✅ Responsive design
- ✅ Turn.js integration (optional)
- ✅ Fallback simple slider
- ✅ Inline CSS styling
- ✅ XSS protection

### 3. Admin Interface

**New admin page: "API & Embed"**
- ✅ API enable/disable toggle
- ✅ API key generator
- ✅ CORS configuration
- ✅ Allowed origins settings
- ✅ Live API endpoint tester
- ✅ Embed code generator
- ✅ Live preview
- ✅ Copy to clipboard buttons

### 4. Security

- ✅ API key authentication
- ✅ Nonce verification
- ✅ User capability checks
- ✅ CORS headers
- ✅ Origin whitelisting
- ✅ XSS prevention
- ✅ SQL injection protection (prepared statements)

### 5. Documentation

**Three comprehensive documentation files:**
- `API_DOCUMENTATION.md` - Complete API reference
- `API_EMBED_SETUP.md` - Quick start guide
- `API_FEATURES_SUMMARY.md` - This file

## Files Created

### PHP Files

1. **[includes/class-flip-menu-api.php](includes/class-flip-menu-api.php)** (343 lines)
   - REST API endpoint definitions
   - Permission checking
   - Data retrieval methods
   - CORS header management
   - API key validation

2. **[admin/partials/flip-menu-admin-api-settings.php](admin/partials/flip-menu-admin-api-settings.php)** (331 lines)
   - API settings form
   - API key management
   - CORS configuration
   - Endpoint documentation
   - Live API tester
   - Embed code generator
   - Preview functionality

### JavaScript Files

3. **[public/js/flip-menu-widget.js](public/js/flip-menu-widget.js)** (357 lines)
   - Widget initialization
   - API data fetching
   - Dynamic HTML rendering
   - Turn.js integration
   - Simple slider fallback
   - Navigation controls
   - Multiple instance support
   - Inline styling

### Documentation Files

4. **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** (689 lines)
   - Complete API reference
   - All endpoints documented
   - Authentication guide
   - CORS setup
   - Code examples (JavaScript, PHP, jQuery, React)
   - Error handling
   - Security best practices
   - Performance tips
   - Troubleshooting

5. **[API_EMBED_SETUP.md](API_EMBED_SETUP.md)** (466 lines)
   - Quick start guide
   - Use case examples
   - Security considerations
   - Troubleshooting guide
   - Testing checklist
   - Performance optimization
   - Advanced customization

6. **[API_FEATURES_SUMMARY.md](API_FEATURES_SUMMARY.md)** (This file)

## Files Modified

### 1. [includes/class-flip-menu.php](includes/class-flip-menu.php)
**Changes:**
- Added API class loading
- Added `define_api_hooks()` method
- Registered REST API hooks

### 2. [admin/class-flip-menu-admin.php](admin/class-flip-menu-admin.php)
**Changes:**
- Added API settings submenu
- Added API settings registration
- Added form handler for API settings
- Added success notice handler
- Added `display_api_settings_page()` method

### 3. [README.md](README.md)
**Changes:**
- Added API & Embed features to features list
- Added REST API section
- Added embeddable widget section
- Added configuration instructions
- Linked to API documentation

## How It Works

### API Flow

```
External Website/App
        ↓
    HTTP GET Request
    (with API key in header)
        ↓
WordPress REST API
    (/wp-json/flip-menu/v1/shops/...)
        ↓
Flip_Menu_API class
    (check_api_permission)
        ↓
    Validate API key
    Check CORS settings
        ↓
    Query database
        ↓
    Return JSON response
        ↓
    External Website/App
    (receives menu data)
```

### Widget Flow

```
External Website
    ↓
Loads flip-menu-widget.js
    ↓
Widget finds all elements with
data-flip-menu-widget attribute
    ↓
For each widget:
    - Read configuration attributes
    - Fetch data from API
    - Render HTML structure
    - Initialize Turn.js (if available)
    - Or use simple slider fallback
    - Setup navigation controls
    ↓
User interacts with flip menu
```

## Usage Examples

### Basic API Call

```bash
curl -X GET \
  "https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete" \
  -H "X-API-Key: your-api-key-here"
```

### Basic Widget Embed

```html
<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-api-key="your-key"
     data-width="800"
     data-height="600">
</div>
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

### JavaScript API Usage

```javascript
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops', {
  headers: { 'X-API-Key': 'your-key' }
})
.then(res => res.json())
.then(data => console.log(data.data));
```

## Configuration Steps

### For Site Owners

1. **Enable API**
   - Go to Flip Menu > API & Embed
   - Check "Enable API"
   - Save settings

2. **Generate API Key** (optional but recommended)
   - Click "Generate New Key"
   - Copy and save the key
   - Save settings

3. **Configure CORS**
   - Check "Enable CORS"
   - Set allowed origins (e.g., `*` or `https://partner.com`)
   - Save settings

4. **Test API**
   - Click "Test" buttons on endpoints
   - Verify JSON responses

5. **Generate Embed Code**
   - Select a shop
   - Adjust dimensions
   - Copy embed code
   - Share with partners

### For External Developers

1. **Get API Access**
   - Obtain base URL from site owner
   - Get API key (if required)
   - Check CORS is enabled

2. **Test Endpoints**
   ```bash
   curl "https://site.com/wp-json/flip-menu/v1/shops"
   ```

3. **Implement**
   - Use REST API for custom implementations
   - Use widget for quick embed
   - Follow security best practices

## API Endpoints Details

### GET /shops

**Purpose:** List all available shops

**Response:**
```json
{
  "success": true,
  "data": [...],
  "count": 5
}
```

**Use Case:** Directory listings, shop selectors

---

### GET /shops/{id}

**Purpose:** Get details for one shop

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Restaurant Name",
    "description": "Description"
  }
}
```

**Use Case:** Shop profile pages

---

### GET /shops/{id}/menu

**Purpose:** Get menu items for a shop

**Response:**
```json
{
  "success": true,
  "data": [...],
  "count": 12
}
```

**Use Case:** Custom menu viewers

---

### GET /shops/{id}/complete

**Purpose:** Get shop + menu in one request (optimized)

**Response:**
```json
{
  "success": true,
  "data": {
    "shop": {...},
    "items": [...],
    "count": 12
  }
}
```

**Use Case:** Widget, full page displays (RECOMMENDED)

---

### GET /verify

**Purpose:** Test API key validity

**Response:**
```json
{
  "success": true,
  "message": "API key is valid"
}
```

**Use Case:** Connection testing, debugging

## Widget Attributes

| Attribute | Required | Description |
|-----------|----------|-------------|
| `data-flip-menu-widget` | Yes | Identifies the widget container |
| `data-shop-id` | Yes | Shop ID to display |
| `data-api-url` | Yes | WordPress REST API base URL |
| `data-api-key` | No | API key for authentication |
| `data-width` | No | Width in pixels (default: 800) |
| `data-height` | No | Height in pixels (default: 600) |
| `data-theme` | No | Theme name (reserved for future) |

## Security Features

### API Key Authentication

- Optional but recommended
- 32-character random string
- Can be regenerated anytime
- Sent via header or query parameter
- Validated on every request

### CORS Protection

- Enable/disable toggle
- Whitelist specific origins
- Prevent unauthorized domains
- Configurable per installation

### WordPress Security

- Nonce verification
- Capability checks
- Prepared SQL statements
- Data sanitization
- Output escaping

## Performance Considerations

### Server Side

- Efficient database queries
- Single query for complete endpoint
- Proper indexing on tables
- No unnecessary joins
- Cached responses (WordPress transients possible)

### Client Side

- Widget caches inline CSS
- Single script load for multiple widgets
- Lazy initialization possible
- Minimal DOM manipulation
- No external dependencies required

### Optimization Tips

1. Use `/complete` endpoint instead of multiple calls
2. Cache API responses on frontend
3. Optimize menu images (resize, compress)
4. Use CDN for widget script
5. Implement lazy loading for widgets
6. Enable browser caching

## Browser Support

### Widget

- ✅ Chrome (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers
- ✅ IE11 (with polyfills)

### Turn.js Enhancement

- ✅ All modern browsers with jQuery
- ✅ Touch devices (iOS, Android)
- ✅ Desktop (Windows, Mac, Linux)

## Use Cases

### 1. Restaurant Directory

Multiple restaurants, each with their own menu embeds.

### 2. Chain Restaurant

Same brand, different locations, different menus.

### 3. Food Delivery App

Mobile app fetches menus via API.

### 4. Partner Websites

Hotels, tourism sites showing restaurant menus.

### 5. White Label Solutions

Agencies creating custom menu viewers for clients.

### 6. Digital Signage

Displays showing rotating menus.

## Testing

### API Testing

```bash
# Test all endpoints
curl https://site.com/wp-json/flip-menu/v1/shops
curl https://site.com/wp-json/flip-menu/v1/shops/1
curl https://site.com/wp-json/flip-menu/v1/shops/1/menu
curl https://site.com/wp-json/flip-menu/v1/shops/1/complete
curl https://site.com/wp-json/flip-menu/v1/verify
```

### Widget Testing

Create test HTML file:
```html
<!DOCTYPE html>
<html>
<head>
    <title>Widget Test</title>
</head>
<body>
    <h1>Test Flip Menu Widget</h1>

    <div data-flip-menu-widget
         data-shop-id="1"
         data-api-url="https://yoursite.com/wp-json"
         data-api-key="your-key">
    </div>

    <script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
</body>
</html>
```

### Browser Console Testing

```javascript
// Test API
fetch('https://site.com/wp-json/flip-menu/v1/shops/1/complete?api_key=your-key')
  .then(r => r.json())
  .then(d => console.log(d));

// Reinit widget
FlipMenuWidget.init();
```

## Troubleshooting

See [API_EMBED_SETUP.md](API_EMBED_SETUP.md) for detailed troubleshooting guide.

## Future Enhancements

Possible additions for future versions:

- [ ] Rate limiting
- [ ] Usage analytics
- [ ] Webhook notifications
- [ ] API versioning
- [ ] GraphQL endpoint
- [ ] WebSocket support
- [ ] Widget themes
- [ ] Widget customization UI
- [ ] API key per partner
- [ ] Usage quotas
- [ ] Caching layer
- [ ] CDN integration

## Conclusion

The Flip Menu plugin now provides:

1. ✅ **Complete REST API** for external access
2. ✅ **Embeddable widget** for any website
3. ✅ **Admin interface** for configuration
4. ✅ **Security features** (API keys, CORS)
5. ✅ **Comprehensive documentation**
6. ✅ **Code examples** in multiple languages
7. ✅ **Testing tools** built-in
8. ✅ **Performance optimized**
9. ✅ **Production ready**

External websites can now easily integrate flip menus with minimal code!

---

**Version:** 1.0.0
**Created:** 2025-10-13
**Author:** Your Name
