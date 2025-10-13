# Flip Menu WordPress Plugin

A WordPress plugin that uses Turn.js to create interactive flip menus for different shops and restaurants. Users can upload PDFs or images to create beautiful flip-through menus.

## Features

- Create multiple shops/restaurants with individual flip menus
- Upload menu images for flip display
- Upload PDF files (requires conversion to images for display)
- Easy-to-use admin interface
- Shortcode support for embedding menus anywhere
- **REST API for external access**
- **Embeddable widget for any website**
- **API key authentication**
- **CORS support for cross-origin requests**
- Responsive design
- Keyboard navigation (arrow keys)
- Touch/swipe support on mobile devices

## Installation

1. Upload the `flip-menu` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to "Flip Menu" in your WordPress admin to start creating shops and menus

## Usage

### Creating a Shop

1. Navigate to **Flip Menu > Add New Shop** in your WordPress admin
2. Enter the shop name and description
3. Click "Create Shop"

### Adding Menu Items

1. Go to **Flip Menu > Manage Menus**
2. Select the shop from the dropdown
3. Upload images or PDFs:
   - **Images**: Upload individual menu page images with page order numbers
   - **PDFs**: Upload a PDF file (note: requires additional setup for page conversion)

### Displaying a Flip Menu

Use the shortcode in any post or page:

```
[flip_menu shop_id="1"]
```

#### Shortcode Attributes

- `shop_id` (required): The ID of the shop (visible in the "All Shops" list)
- `width` (optional): Width in pixels (default: 800)
- `height` (optional): Height in pixels (default: 600)

Example with custom dimensions:
```
[flip_menu shop_id="1" width="1000" height="700"]
```

## REST API & Embeddable Widget

### REST API

The plugin provides a complete REST API for external access to shop and menu data.

**Base URL:** `https://yoursite.com/wp-json/flip-menu/v1`

**Available Endpoints:**
- `GET /shops` - Get all shops
- `GET /shops/{id}` - Get single shop
- `GET /shops/{id}/menu` - Get shop menu items
- `GET /shops/{id}/complete` - Get shop with menu items

**Example:**
```bash
curl -X GET "https://yoursite.com/wp-json/flip-menu/v1/shops/1/complete" \
     -H "X-API-Key: your-api-key"
```

### Embeddable Widget

Allow other websites to embed your flip menus with a simple code snippet:

```html
<div data-flip-menu-widget
     data-shop-id="1"
     data-api-url="https://yoursite.com/wp-json"
     data-api-key="your-api-key"
     data-width="800"
     data-height="600">
</div>
<script src="https://yoursite.com/wp-content/plugins/flip-menu/public/js/flip-menu-widget.js"></script>
```

### Configuration

1. Go to **Flip Menu > API & Embed** in WordPress admin
2. Enable API access
3. Generate an API key (optional, for security)
4. Enable CORS for cross-domain access
5. Use the embed code generator to create widget code

**For complete API documentation, see [API_DOCUMENTATION.md](API_DOCUMENTATION.md)**

## Turn.js Library

This plugin requires the Turn.js library for the flip effect. You need to:

1. Download Turn.js from [http://www.turnjs.com/](http://www.turnjs.com/)
2. Place the `turn.min.js` file in the `/public/js/` directory of the plugin

**Important:** Turn.js has licensing requirements for commercial use. Please review their license before using this plugin in a commercial environment.

## PDF Support

### Converting PDFs to Images

For PDFs to display properly in the flip menu, they need to be converted to images. This requires:

1. **ImageMagick or Imagick PHP extension** installed on your server
2. Additional code to convert PDF pages to images on upload

### Basic PDF Conversion Implementation

Add this to your `admin/class-flip-menu-admin.php` in the `handle_pdf_upload` method:

```php
// After successful PDF upload
if (extension_loaded('imagick')) {
    $imagick = new Imagick();
    $imagick->setResolution(150, 150);
    $imagick->readImage($movefile['file']);

    foreach ($imagick as $page_num => $page) {
        $page->setImageFormat('jpg');
        $filename = 'menu-page-' . ($page_num + 1) . '.jpg';
        $upload_dir = wp_upload_dir();
        $image_path = $upload_dir['path'] . '/' . $filename;
        $page->writeImage($image_path);

        // Insert each page as a separate menu item
        $wpdb->insert(
            $table_name,
            array(
                'shop_id' => $shop_id,
                'title' => sanitize_text_field($_POST['title']) . ' - Page ' . ($page_num + 1),
                'source_type' => 'image',
                'source_url' => $upload_dir['url'] . '/' . $filename,
                'page_order' => $page_num
            )
        );
    }
}
```

## File Structure

```
flip-menu/
├── admin/
│   ├── class-flip-menu-admin.php       # Admin functionality
│   ├── css/
│   │   └── flip-menu-admin.css         # Admin styles
│   ├── js/
│   │   └── flip-menu-admin.js          # Admin scripts
│   └── partials/
│       ├── flip-menu-admin-display.php      # Shop list page
│       ├── flip-menu-admin-add-shop.php     # Add shop form
│       └── flip-menu-admin-manage-menus.php # Menu management page
├── includes/
│   ├── class-flip-menu.php             # Main plugin class
│   ├── class-flip-menu-activator.php   # Activation hooks
│   ├── class-flip-menu-deactivator.php # Deactivation hooks
│   ├── class-flip-menu-loader.php      # Hooks loader
│   └── class-flip-menu-i18n.php        # Internationalization
├── public/
│   ├── class-flip-menu-public.php      # Public-facing functionality
│   ├── css/
│   │   └── flip-menu-public.css        # Frontend styles
│   └── js/
│       ├── turn.min.js                 # Turn.js library (YOU NEED TO ADD THIS)
│       └── flip-menu-public.js         # Frontend scripts
├── flip-menu.php                       # Main plugin file
└── README.md                           # This file
```

## Database Tables

The plugin creates two custom tables:

### wp_flip_menu_shops
Stores shop information:
- `id` - Shop ID
- `name` - Shop name
- `description` - Shop description
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

### wp_flip_menu_items
Stores menu items:
- `id` - Item ID
- `shop_id` - Foreign key to shops table
- `title` - Item title
- `source_type` - Type: 'image' or 'pdf'
- `source_url` - URL to the file
- `page_order` - Display order
- `created_at` - Creation timestamp
- `updated_at` - Update timestamp

## Requirements

- WordPress 5.0 or higher
- PHP 7.0 or higher
- jQuery (included with WordPress)
- Turn.js library (must be added separately)
- (Optional) ImageMagick/Imagick for PDF conversion

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Customization

### Styling

Customize the appearance by editing:
- `/public/css/flip-menu-public.css` - Frontend styles
- `/admin/css/flip-menu-admin.css` - Admin styles

### Turn.js Options

Modify Turn.js settings in `/public/class-flip-menu-public.php` in the `flip_menu_shortcode` method:

```javascript
flipbook.turn({
    width: 800,
    height: 600,
    autoCenter: true,
    display: 'double',      // 'single' or 'double' page display
    acceleration: true,
    gradients: true,
    elevation: 50
});
```

## Support & Development

### Known Limitations

1. PDF files require server-side conversion to images
2. Turn.js has commercial licensing requirements
3. Large images may affect performance

### Future Enhancements

- Automatic PDF to image conversion
- Drag-and-drop reordering of pages
- Fullscreen mode
- Zoom functionality
- Mobile-optimized controls
- Multiple menu templates

## License

This plugin is licensed under GPL v2 or later.

**Note:** Turn.js has its own licensing requirements. Please ensure compliance with Turn.js license terms for commercial use.

## Credits

- Turn.js by Emmanuel Garcia - [http://www.turnjs.com/](http://www.turnjs.com/)
- WordPress Plugin Boilerplate by DevinVinson

## Changelog

### Version 1.0.0
- Initial release
- Shop management
- Image upload support
- PDF upload support (requires conversion)
- Shortcode implementation
- Responsive design
- Keyboard navigation

## Author

Your Name or Your Company

## Support

For support, please visit [your support URL]
