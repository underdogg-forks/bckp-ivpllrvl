# Theme System Documentation

This application supports multiple themes with comprehensive CSS variable support using separate theme files. Each theme is defined in its own CSS file with hex color values for easy customization.

## Theme Architecture

Themes are now organized in separate CSS files located in `resources/css/themes/`:
- `default.css` - Default theme with standard colors
- `nord.css` - Nord theme (Polar Night variant)
- `nord-dark.css` - Nord Dark theme (darker backgrounds)
- `mandarin.css` - Mandarin theme (dark gray with warm accents)
- `metro.css` - Metro theme (modern, vibrant Microsoft-inspired) **NEW!**

All theme files are loaded via `vite.config.js` and compiled into separate CSS files that can be loaded on demand.

## Available Themes

### 1. Default Theme
The default theme with a clean, modern look suitable for most use cases.

**File:** `resources/css/themes/default.css`

**Usage:**
```html
<!-- Default theme is active by default -->
<body class="bg-body text-base-color">
    <!-- Your content -->
</body>
```

**To load in your layout:**
```blade
@vite(['resources/css/app.css', 'resources/css/themes/default.css'])
```

### 2. Nord Theme
A beautiful arctic, north-bluish color palette inspired by the beauty of the arctic.

**File:** `resources/css/themes/nord.css`

**Usage:**
```blade
@vite(['resources/css/app.css', 'resources/css/themes/nord.css'])
```

### 3. Nord Dark Theme  
Darker variant of Nord with deeper backgrounds for better contrast.

**File:** `resources/css/themes/nord-dark.css`

**Usage:**
```blade
@vite(['resources/css/app.css', 'resources/css/themes/nord-dark.css'])
```

### 4. Mandarin Theme
A sophisticated dark theme with gray-based primary colors and warm accent colors. Perfect for a professional, subdued look.

**File:** `resources/css/themes/mandarin.css`

**Colors:**
- Primary: Gray (#7A8288)
- Success: Green (#58a959)
- Warning: Orange (#f89406)
- Danger: Red (#ee5f5b)
- Background: Dark gray (#272B30)
- Text: Light gray (#C8C8C8)

**Usage:**
```blade
@vite(['resources/css/app.css', 'resources/css/themes/mandarin.css'])
```

**Dark Mode:**
The mandarin theme includes an even darker variant when the `dark` class is applied, making backgrounds nearly black while maintaining the warm accent colors.

### 5. Metro Theme **NEW!**
A modern, vibrant theme inspired by Microsoft's Metro/Fluent design language. Features bold colors with a clean, flat aesthetic.

**File:** `resources/css/themes/metro.css`

**Colors:**
- Primary: Metro Blue (#0078D7)
- Success: Metro Green (#107C10)
- Info: Metro Cyan (#00B7C3)
- Warning: Metro Yellow (#FFB900)
- Danger: Metro Red (#E81123)
- Background: White (#ffffff)
- Text: Dark gray (#333333)

**Usage:**
```blade
@vite(['resources/css/app.css', 'resources/css/themes/metro.css'])
```

**Dark Mode:**
The metro theme includes a dark variant with lighter accent colors and dark backgrounds for comfortable nighttime viewing.

## Dark Mode Support

The default theme includes dark mode support via the `.dark` class:

```html
<!-- Light mode -->
<html>
    <!-- Content -->
</html>

<!-- Dark mode -->
<html class="dark">
    <!-- Content -->
</html>
```

## CSS Variables (Hex Colors)

Each theme file defines the following CSS variables using hex colors:

### Gray Scale
- `--color-gray-base` - Base gray color
- `--color-gray-darker` - Darker gray
- `--color-gray-dark` - Dark gray
- `--color-gray` - Medium gray
- `--color-gray-light` - Light gray
- `--color-gray-lighter` - Lighter gray
- `--color-gray-lightest` - Lightest gray

### Brand Colors
- `--color-brand-primary` - Primary brand color
- `--color-brand-success` - Success color (green)
- `--color-brand-info` - Info color (blue/cyan)
- `--color-brand-warning` - Warning color (yellow/orange)
- `--color-brand-danger` - Danger color (red)

### Scaffolding
- `--color-body-bg` - Background color for `<body>`
- `--color-text` - Global text color
- `--color-link` - Link color
- `--color-link-hover` - Link hover color

## Utility Classes

### Brand Color Utilities

Use these classes to apply theme colors:

```html
<!-- Background colors -->
<div class="bg-brand-primary">Primary background</div>
<div class="bg-brand-success">Success background</div>
<div class="bg-brand-info">Info background</div>
<div class="bg-brand-warning">Warning background</div>
<div class="bg-brand-danger">Danger background</div>

<!-- Text colors -->
<p class="text-brand-primary">Primary text</p>
<p class="text-brand-success">Success text</p>
<p class="text-brand-info">Info text</p>
<p class="text-brand-warning">Warning text</p>
<p class="text-brand-danger">Danger text</p>
```

### General Utilities

```html
<!-- Body background and text color -->
<div class="bg-body text-base-color">Themed content</div>

<!-- Links -->
<a class="text-link hover:text-link-hover">Themed link</a>
```

## Creating a New Theme

To create a new theme (e.g., "mandarin-orange" or "reddit-red"):

### Step 1: Create Theme File

Create a new file in `resources/css/themes/your-theme.css`:

```css
/**
 * Your Custom Theme
 * 
 * Description of your theme
 */

@theme {
    /* Gray scale */
    --color-gray-base: #000000;
    --color-gray-darker: #1a1a1a;
    --color-gray-dark: #333333;
    --color-gray: #666666;
    --color-gray-light: #999999;
    --color-gray-lighter: #cccccc;
    --color-gray-lightest: #f5f5f5;
    
    /* Brand colors - customize these */
    --color-brand-primary: #ff6600;  /* Your primary color */
    --color-brand-success: #00cc66;  /* Your success color */
    --color-brand-info: #0099ff;     /* Your info color */
    --color-brand-warning: #ffcc00;  /* Your warning color */
    --color-brand-danger: #ff3333;   /* Your danger color */
    
    /* Scaffolding */
    --color-body-bg: #ffffff;
    --color-text: #333333;
    --color-link: #ff6600;
    --color-link-hover: #cc5200;
}

/* Optional: Dark mode variant */
.dark {
    --color-body-bg: #1a1a1a;
    --color-text: #e5e5e5;
    /* ... etc ... */
}
```

### Step 2: Add to vite.config.js

Update `vite.config.js` to include your new theme:

```javascript
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/css/themes/default.css",
                "resources/css/themes/nord.css",
                "resources/css/themes/nord-dark.css",
                "resources/css/themes/your-theme.css",  // Add your theme
                "resources/js/app.js"
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

### Step 3: Use in Your Layout

```blade
@vite(['resources/css/app.css', 'resources/css/themes/your-theme.css'])
```

## Theme Color Reference

### Default Theme Colors
- **Primary**: #2C8EDD (Blue)
- **Success**: #5cb85c (Green)
- **Info**: #5bc0de (Cyan)
- **Warning**: #f0ad4e (Orange)
- **Danger**: #d9534f (Red)

### Nord Theme Colors
- **Primary**: #4C566A (Polar Night)
- **Success**: #8FBCBB (Frost - Cyan)
- **Info**: #88C0D0 (Frost - Blue)
- **Warning**: #EBCB8B (Aurora - Yellow)
- **Danger**: #BF616A (Aurora - Red)

**Nord Palette Groups:**
- Polar Night: #2E3440, #3B4252, #434C5E, #4C566A
- Snow Storm: #D8DEE9, #E5E9F0, #ECEFF4
- Frost: #8FBCBB, #88C0D0, #81A1C1, #5E81AC
- Aurora: #BF616A, #D08770, #EBCB8B, #A3BE8C, #B48EAD

### Nord Dark Theme
Same as Nord but with darker background (#2E3440)

## Dynamic Theme Loading

You can dynamically load themes based on user preference:

```javascript
// Theme switcher example
function loadTheme(themeName) {
    // Remove existing theme links
    document.querySelectorAll('link[data-theme]').forEach(link => link.remove());
    
    // Create new theme link
    const themeLink = document.createElement('link');
    themeLink.rel = 'stylesheet';
    themeLink.href = `/build/assets/${themeName}.css`;  // Vite build output
    themeLink.setAttribute('data-theme', themeName);
    document.head.appendChild(themeLink);
    
    // Save preference
    localStorage.setItem('theme', themeName);
}

// Toggle dark mode
function toggleDarkMode() {
    document.documentElement.classList.toggle('dark');
    const isDark = document.documentElement.classList.contains('dark');
    localStorage.setItem('darkMode', isDark);
}

// Load saved preferences
const savedTheme = localStorage.getItem('theme') || 'default';
const savedDarkMode = localStorage.getItem('darkMode') === 'true';

if (savedTheme !== 'default') {
    loadTheme(savedTheme);
}

if (savedDarkMode) {
    document.documentElement.classList.add('dark');
}
```

## Advantages of This Approach

1. **Modular**: Each theme is in its own file, making it easy to manage
2. **Hex Colors**: Easier to read and customize than RGB triplets
3. **On-Demand Loading**: Load only the theme CSS you need
4. **Easy Extension**: Add new themes without modifying existing files
5. **Version Control Friendly**: Theme changes are isolated to specific files
6. **Build Optimization**: Vite can optimize and cache each theme separately

## Notes

- All CSS variable values use hex format for easier customization
- Theme files are processed by Tailwind's `@theme` directive
- The `app.css` file contains utilities that work with any theme
- Welcome page colors are defined separately in `app.css` and can be customized per theme if needed

