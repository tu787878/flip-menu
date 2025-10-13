# Turn.js Setup Instructions

## IMPORTANT: This plugin requires the Turn.js library to function

The Turn.js library is **NOT included** in this plugin due to licensing restrictions. You must download and add it separately.

## Steps to Add Turn.js

### 1. Download Turn.js

Visit the official Turn.js website:
- **URL:** [http://www.turnjs.com/](http://www.turnjs.com/)
- Download the Turn.js library (turn.min.js)

### 2. Add to Plugin

Place the `turn.min.js` file in this directory:
```
/wp-content/plugins/flip-menu/public/js/turn.min.js
```

The plugin expects the file at this exact location.

### 3. Verify Installation

After adding the file, your directory structure should look like:
```
flip-menu/
└── public/
    └── js/
        ├── turn.min.js          ← ADD THIS FILE
        └── flip-menu-public.js
```

### 4. Check if Working

1. Activate the plugin in WordPress
2. Create a shop and upload some menu images
3. Add the shortcode `[flip_menu shop_id="1"]` to a page
4. If you see a flip menu, it's working!
5. If you see JavaScript errors in the browser console, Turn.js is not properly loaded

## Turn.js Licensing

**IMPORTANT:** Turn.js has different licensing options:

### Non-Commercial Use
- **Free** for non-commercial projects
- Educational and personal use
- Non-profit organizations

### Commercial Use
- **Requires a commercial license**
- Businesses and commercial websites
- For-profit organizations

**You must purchase a license for commercial use!**

Visit [http://www.turnjs.com/](http://www.turnjs.com/) for licensing details.

## Alternative Solutions

If you cannot use Turn.js due to licensing restrictions, consider these alternatives:

1. **Turn.js Alternatives:**
   - FlipBook.js
   - Page Flip (pure CSS)
   - Book Block
   - jFlip

2. **Modify the Plugin:**
   - Replace Turn.js integration in `/public/class-flip-menu-public.php`
   - Update the shortcode rendering method
   - Adjust CSS in `/public/css/flip-menu-public.css`

## Troubleshooting

### Turn.js Not Loading

**Symptom:** Menu images don't flip, JavaScript console shows errors

**Solutions:**
1. Verify `turn.min.js` is in the correct location
2. Check file permissions (should be readable)
3. Clear WordPress and browser cache
4. Check browser console for specific errors

### Menu Not Displaying

**Symptom:** Blank space where menu should be

**Possible Causes:**
1. Turn.js not loaded
2. No menu items uploaded
3. Invalid shop_id in shortcode
4. CSS conflicts with theme

**Debug Steps:**
```javascript
// Add to browser console:
console.log(jQuery.fn.turn);  // Should show function, not undefined
```

### Performance Issues

**Symptom:** Menu is slow or laggy

**Solutions:**
1. Optimize image sizes (recommended: max 1200px width)
2. Reduce number of pages
3. Use JPG instead of PNG for photos
4. Enable browser caching

## Support Resources

- **Turn.js Documentation:** [http://www.turnjs.com/docs](http://www.turnjs.com/docs)
- **jQuery Documentation:** [https://api.jquery.com/](https://api.jquery.com/)
- **WordPress Codex:** [https://codex.wordpress.org/](https://codex.wordpress.org/)

## Testing Checklist

Before deploying to production:

- [ ] Turn.js file is in correct location
- [ ] Plugin activates without errors
- [ ] Can create shops
- [ ] Can upload images
- [ ] Shortcode displays menu correctly
- [ ] Menu flips on click
- [ ] Previous/Next buttons work
- [ ] Keyboard navigation works
- [ ] Mobile/touch gestures work
- [ ] Responsive on different screen sizes
- [ ] No JavaScript console errors

## Need Help?

If you're having trouble setting up Turn.js:

1. Check file paths are correct
2. Verify Turn.js version compatibility (tested with v4.1.0)
3. Test with a default WordPress theme to rule out theme conflicts
4. Disable other plugins to check for conflicts

---

**Remember:** You MUST add turn.min.js to the plugin for it to work!
