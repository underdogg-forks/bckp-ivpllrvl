# Theme Guide

## Available Themes

The application now supports multiple themes through CSS variables:

1. **Default** - Standard blue theme with light/dark mode
2. **Nord** - Arctic blue palette with light mode
3. **Nord Dark** - Arctic blue palette, dark-first
4. **Mandarin** - Dark gray theme with warm accent colors (NEW!)

## Using the Mandarin Theme

The Mandarin theme is a dark-first theme based on gray tones with warm accent colors.

### Colors

**Primary Color:** Gray (#7A8288)
**Success:** Green (#58a959)  
**Info:** Cyan (#5bc0de)
**Warning:** Orange (#f89406)
**Danger:** Red (#ee5f5b)

**Background:** Dark gray (#272B30)
**Text:** Light gray (#C8C8C8)

### Applying the Theme

#### Option 1: In Blade Templates

Replace the default theme import with mandarin:

```blade
{{-- Default theme --}}
@vite('resources/css/themes/default.css')

{{-- Mandarin theme --}}
@vite('resources/css/themes/mandarin.css')
```

#### Option 2: Dynamic Theme Switching

Add theme switching logic to your layout:

```blade
@php
    $theme = session('theme', 'default'); // Get theme from session
@endphp

@vite("resources/css/themes/{$theme}.css")
```

#### Option 3: Via Configuration

In your layout file, check an environment variable or config:

```blade
@vite('resources/css/themes/' . config('app.theme', 'default') . '.css')
```

Then in `.env`:
```env
APP_THEME=mandarin
```

Or in `config/app.php`:
```php
'theme' => env('APP_THEME', 'default'),
```

### Component Compatibility

All components using semantic color classes will automatically adapt:

- `btn-primary` → Uses mandarin's gray primary color
- `text-brand-primary` → Gray (#7A8288)
- `bg-brand-success` → Green (#58a959)
- `text-link` → Light gray (#C8C8C8)
- `text-link-hover` → White

### Dark Mode

The mandarin theme includes a dark mode variant that makes the theme even darker:
- Background becomes #1a1d20 (almost black)
- Text becomes lighter (#e0e0e0)
- Cards/panels adjust to darker shades

To enable dark mode, add the `dark` class to your HTML element as usual:

```html
<html class="dark">
```

## Creating Custom Themes

To create your own theme:

1. **Copy an existing theme file:**
   ```bash
   cp resources/css/themes/default.css resources/css/themes/mytheme.css
   ```

2. **Update the CSS variables** in your new file:
   ```css
   @theme {
       --color-brand-primary: #your-color;
       --color-brand-success: #your-color;
       /* ... etc */
   }
   ```

3. **Add to vite.config.js:**
   ```js
   input: [
       // ... other files
       "resources/css/themes/mytheme.css",
   ],
   ```

4. **Use in your blade files:**
   ```blade
   @vite('resources/css/themes/mytheme.css')
   ```

## CSS Variables Reference

All themes must define these variables:

### Brand Colors
- `--color-brand-primary` - Primary brand color
- `--color-brand-success` - Success state color
- `--color-brand-info` - Info state color
- `--color-brand-warning` - Warning state color
- `--color-brand-danger` - Danger/error state color

### Gray Scale
- `--color-gray-base` through `--color-gray-lightest`

### Scaffolding
- `--color-body-bg` - Page background
- `--color-text` - Default text color
- `--color-link` - Link color
- `--color-link-hover` - Link hover color

### Button Colors
- `--color-btn-primary-bg` - Primary button background
- `--color-btn-primary-bg-hover` - Primary button hover
- `--color-btn-primary-text` - Primary button text
- `--color-btn-primary-border` - Primary button border
- `--color-btn-primary-focus-ring` - Primary button focus ring

- `--color-btn-default-bg` - Default button background
- `--color-btn-default-bg-hover` - Default button hover
- `--color-btn-default-text` - Default button text
- `--color-btn-default-border` - Default button border
- `--color-btn-default-focus-ring` - Default button focus ring

### Card/Panel Colors
- `--color-card-bg` - Card background
- `--color-card-border` - Card border
- `--color-card-text` - Card text

### Table Colors
- `--color-table-bg` - Table background
- `--color-table-hover-bg` - Table row hover
- `--color-table-border` - Table border
- `--color-table-header-bg` - Table header background
- `--color-table-header-text` - Table header text

## Benefits of Theme System

✅ **Easy Customization** - Change colors without touching Blade templates  
✅ **Consistent Branding** - All components use the same color variables  
✅ **Theme Switching** - Switch themes at runtime or per user  
✅ **Dark Mode Support** - Each theme can define light and dark variants  
✅ **Maintainable** - Update one variable, change the entire theme  

## Examples

### Mandarin Theme Preview

**Light Mode:**
- Dark gray background (#272B30)
- Light gray text (#C8C8C8)
- Gray primary buttons (#7A8288)

**Dark Mode:**
- Almost black background (#1a1d20)
- Lighter text (#e0e0e0)
- Same accent colors

### Default Theme Preview

**Light Mode:**
- White background
- Dark text
- Blue primary buttons (#2C8EDD)

**Dark Mode:**
- Dark gray background (#121212)
- Light text
- Lighter blue buttons (#42a5f5)

## Troubleshooting

**Theme not applying?**
1. Make sure you've run `npm run build` or `npm run dev`
2. Check that the theme file is imported in your blade template
3. Verify the theme file is listed in `vite.config.js`
4. Clear browser cache and hard reload

**Colors look wrong?**
1. Make sure you're using semantic classes (`text-brand-primary`, not `text-blue-600`)
2. Check that the theme file defines all required CSS variables
3. Verify dark mode class is being applied correctly if using dark mode

**Want to switch themes dynamically?**
Implement theme switching in your application settings:
1. Store user's theme preference in database or session
2. Load the corresponding theme CSS file
3. Optionally use JavaScript to switch themes without page reload
