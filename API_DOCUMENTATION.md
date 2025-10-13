# Flip Menu API & Embed Documentation

## Overview

The Flip Menu plugin provides a RESTful API and embeddable widget system that allows external websites to access and display flip menus.

## Table of Contents

1. [REST API](#rest-api)
2. [Embeddable Widget](#embeddable-widget)
3. [Authentication](#authentication)
4. [CORS Configuration](#cors-configuration)
5. [Examples](#examples)

---

## REST API

### Base URL

```
https://yoursite.com/wp-json/flip-menu/v1
```

### Endpoints

#### 1. Get All Shops

**Endpoint:** `GET /shops`

**Description:** Retrieve a list of all shops.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Restaurant Name",
      "description": "Restaurant description",
      "created_at": "2025-01-01 12:00:00",
      "updated_at": "2025-01-01 12:00:00"
    }
  ],
  "count": 1
}
```

**Example:**
```bash
curl -X GET "https://yoursite.com/wp-json/flip-menu/v1/shops" \
     -H "X-API-Key: your-api-key-here"
```

---

#### 2. Get Single Shop

**Endpoint:** `GET /shops/{id}`

**Description:** Retrieve a specific shop by ID.

**Parameters:**
- `id` (required): The shop ID

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Restaurant Name",
    "description": "Restaurant description",
    "created_at": "2025-01-01 12:00:00",
    "updated_at": "2025-01-01 12:00:00"
  }
}
```

**Example:**
```bash
curl -X GET "https://yoursite.com/wp-json/flip-menu/v1/shops/1" \
     -H "X-API-Key: your-api-key-here"
```

---

#### 3. Get Shop Menu Items

**Endpoint:** `GET /shops/{id}/menu`

**Description:** Retrieve all menu items for a specific shop.

**Parameters:**
- `id` (required): The shop ID

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "shop_id": 1,
      "title": "Menu Page 1",
      "source_type": "image",
      "source_url": "https://yoursite.com/uploads/menu-page-1.jpg",
      "page_order": 0,
      "created_at": "2025-01-01 12:00:00",
      "updated_at": "2025-01-01 12:00:00"
    }
  ],
  "count": 1
}
```

**Example:**
```bash
curl -X GET "https://yoursite.com/wp-json/flip-menu/v1/shops/1/menu" \
     -H "X-API-Key: your-api-key-here"
```

---

#### 4. Get Complete Shop Data

**Endpoint:** `GET /shops/{id}/complete`

**Description:** Retrieve shop information along with all menu items in a single request.

**Parameters:**
- `id` (required): The shop ID

**Response:**
```json
{
  "success": true,
  "data": {
    "shop": {
      "id": 1,
      "name": "Restaurant Name",
      "description": "Restaurant description",
      "created_at": "2025-01-01 12:00:00",
      "updated_at": "2025-01-01 12:00:00"
    },
    "items": [
      {
        "id": 1,
        "shop_id": 1,
        "title": "Menu Page 1",
        "source_type": "image",
        "source_url": "https://yoursite.com/uploads/menu-page-1.jpg",
        "page_order": 0,
        "created_at": "2025-01-01 12:00:00",
        "updated_at": "2025-01-01 12:00:00"
      }
    ],
    "count": 1
  }
}
```

**Example:**
```bash
curl -X GET "https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete" \
     -H "X-API-Key: your-api-key-here"
```

---

#### 5. Verify API Key

**Endpoint:** `GET /verify`

**Description:** Test if your API key is valid.

**Response:**
```json
{
  "success": true,
  "message": "API key is valid"
}
```

**Example:**
```bash
curl -X GET "https://yoursite.com/wp-json/flip-menu/v1/verify" \
     -H "X-API-Key: your-api-key-here"
```

---

## Authentication

### API Key

The API uses API key authentication. Include your API key in requests using either:

**Method 1: Header (Recommended)**
```
X-API-Key: your-api-key-here
```

**Method 2: Query Parameter**
```
?api_key=your-api-key-here
```

### Getting Your API Key

1. Go to WordPress Admin > Flip Menu > API & Embed
2. Enable API access
3. Generate an API key
4. Copy and save the key securely

### Public Access

If no API key is set in the settings, the API allows public access. This is useful for testing but not recommended for production.

---

## Embeddable Widget

### Quick Start

Add this code to any HTML page:

```html
<!-- Flip Menu Widget -->
<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-api-key="your-api-key"
     data-width="800"
     data-height="600">
</div>
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

### Widget Attributes

| Attribute | Required | Default | Description |
|-----------|----------|---------|-------------|
| `data-shop-id` | Yes | - | The ID of the shop to display |
| `data-api-url` | Yes | - | Your WordPress site's REST API URL |
| `data-api-key` | No | - | API key for authentication |
| `data-width` | No | 800 | Widget width in pixels |
| `data-height` | No | 600 | Widget height in pixels |
| `data-theme` | No | default | Theme name (reserved for future use) |

### Multiple Widgets

You can embed multiple widgets on the same page:

```html
<!-- Restaurant 1 -->
<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-width="800"
     data-height="600">
</div>

<!-- Restaurant 2 -->
<div data-flip-menu-widget
     data-shop-id="2"
     data-api-url="https://yoursite.com/wp-json"
     data-width="800"
     data-height="600">
</div>

<!-- Load script once -->
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

### Widget Features

- ✅ Standalone - No dependencies required (jQuery optional for Turn.js)
- ✅ Responsive design
- ✅ Simple slider fallback if Turn.js not available
- ✅ Keyboard navigation (with Turn.js)
- ✅ Touch/swipe support (with Turn.js)
- ✅ Automatic initialization
- ✅ Multiple instances per page

### With Turn.js Enhancement

For the full flip effect, include jQuery and Turn.js before the widget:

```html
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="path/to/turn.min.js"></script>

<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-api-key="your-api-key"
     data-width="800"
     data-height="600">
</div>
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

---

## CORS Configuration

### Enabling CORS

1. Go to WordPress Admin > Flip Menu > API & Embed
2. Check "Enable CORS"
3. Set allowed origins:
   - `*` - Allow all domains (not recommended for production)
   - `https://example.com` - Allow specific domain
   - `https://example.com,https://another.com` - Multiple domains (comma-separated)

### CORS Headers

The plugin automatically adds these headers when CORS is enabled:

```
Access-Control-Allow-Origin: *
Access-Control-Allow-Methods: GET, OPTIONS
Access-Control-Allow-Headers: X-API-Key, Content-Type
Access-Control-Allow-Credentials: true
```

---

## Examples

### JavaScript Example

```javascript
// Fetch all shops
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops', {
  headers: {
    'X-API-Key': 'your-api-key-here'
  }
})
.then(response => response.json())
.then(data => {
  console.log('Shops:', data.data);
})
.catch(error => {
  console.error('Error:', error);
});

// Fetch shop with menu
fetch('https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete', {
  headers: {
    'X-API-Key': 'your-api-key-here'
  }
})
.then(response => response.json())
.then(data => {
  const shop = data.data.shop;
  const items = data.data.items;
  console.log('Shop:', shop.name);
  console.log('Menu items:', items.length);
})
.catch(error => {
  console.error('Error:', error);
});
```

### PHP Example

```php
<?php
$api_url = 'https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete';
$api_key = 'your-api-key-here';

$response = wp_remote_get($api_url, array(
    'headers' => array(
        'X-API-Key' => $api_key
    )
));

if (!is_wp_error($response)) {
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if ($data['success']) {
        $shop = $data['data']['shop'];
        $items = $data['data']['items'];

        echo "Shop: " . $shop['name'] . "\n";
        echo "Menu items: " . count($items) . "\n";
    }
}
?>
```

### jQuery Example

```javascript
jQuery(document).ready(function($) {
    $.ajax({
        url: 'https://yoursite.com/wp-json/flip-menu/v1/shops',
        method: 'GET',
        headers: {
            'X-API-Key': 'your-api-key-here'
        },
        success: function(data) {
            if (data.success) {
                console.log('Shops:', data.data);

                // Display shops
                data.data.forEach(function(shop) {
                    $('#shop-list').append(
                        '<div class="shop">' +
                        '<h3>' + shop.name + '</h3>' +
                        '<p>' + shop.description + '</p>' +
                        '</div>'
                    );
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
        }
    });
});
```

### React Example

```javascript
import React, { useEffect, useState } from 'react';

function ShopMenu({ shopId }) {
  const [shop, setShop] = useState(null);
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetch(`https://yoursite.com/wp-json/flip-menu/v1/shops/${shopId}/complete`, {
      headers: {
        'X-API-Key': 'your-api-key-here'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        setShop(data.data.shop);
        setItems(data.data.items);
      }
      setLoading(false);
    })
    .catch(error => {
      console.error('Error:', error);
      setLoading(false);
    });
  }, [shopId]);

  if (loading) return <div>Loading...</div>;
  if (!shop) return <div>Shop not found</div>;

  return (
    <div>
      <h1>{shop.name}</h1>
      <p>{shop.description}</p>
      <div className="menu-items">
        {items.map(item => (
          <div key={item.id}>
            <img src={item.source_url} alt={item.title} />
          </div>
        ))}
      </div>
    </div>
  );
}

export default ShopMenu;
```

---

## Error Handling

### Error Responses

All errors return a standard format:

```json
{
  "success": false,
  "message": "Error message here"
}
```

### Common Error Codes

| Code | Message | Solution |
|------|---------|----------|
| 403 | API access is disabled | Enable API in settings |
| 403 | Invalid API key | Check your API key |
| 404 | Shop not found | Verify shop ID exists |
| 500 | Server error | Check WordPress error logs |

---

## Security Best Practices

1. **Use HTTPS**: Always use HTTPS for API requests
2. **Secure API Keys**: Never commit API keys to public repositories
3. **Restrict Origins**: Set specific allowed origins instead of `*`
4. **Rotate Keys**: Regenerate API keys periodically
5. **Monitor Usage**: Check API access logs regularly
6. **Rate Limiting**: Consider implementing rate limiting (future feature)

---

## Performance Tips

1. **Cache Responses**: Cache API responses on your frontend
2. **Optimize Images**: Use appropriately sized menu images
3. **Minimize Requests**: Use `/complete` endpoint instead of multiple requests
4. **CDN**: Serve widget script from CDN if possible
5. **Lazy Loading**: Load widgets only when visible

---

## Troubleshooting

### Widget Not Displaying

**Issue:** Widget shows blank or error message

**Solutions:**
1. Check API is enabled in settings
2. Verify API key is correct
3. Check CORS is enabled
4. Open browser console for errors
5. Test API endpoint directly

### CORS Errors

**Issue:** "No 'Access-Control-Allow-Origin' header"

**Solutions:**
1. Enable CORS in plugin settings
2. Add your domain to allowed origins
3. Check server configuration
4. Clear browser cache

### API Returns 404

**Issue:** Endpoint not found

**Solutions:**
1. Check WordPress permalink settings
2. Flush rewrite rules (Settings > Permalinks > Save)
3. Verify plugin is activated
4. Check .htaccess file

---

## Support

For issues or questions:
- Check the main [README.md](README.md)
- Review [TURN_JS_SETUP.md](TURN_JS_SETUP.md)
- Test endpoints in API & Embed settings page
- Check WordPress error logs

---

## Version History

### 1.0.0
- Initial API implementation
- Embeddable widget
- CORS support
- API key authentication
- Complete documentation
