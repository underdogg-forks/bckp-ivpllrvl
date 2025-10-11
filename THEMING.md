# Theme System Documentation

This application supports multiple themes with comprehensive CSS variable support, making it easy to add new themes or customize existing ones.

## Available Themes

### 1. Default Theme
The default theme with a clean, modern look suitable for most use cases.

**Usage:**
```html
<!-- Default theme is active by default -->
<body class="bg-body text-base-color">
    <!-- Your content -->
</body>
```

### 2. Nord Theme
A beautiful arctic, north-bluish color palette inspired by the beauty of the arctic.

**Usage:**
```html
<!-- Add data-theme="nord" attribute to the root element -->
<html data-theme="nord">
    <!-- Your content -->
</html>
```

## Dark Mode Support

All themes support dark mode out of the box. Toggle dark mode by adding/removing the `dark` class:

```html
<!-- Light mode -->
<html data-theme="nord">
    <!-- Content -->
</html>

<!-- Dark mode -->
<html data-theme="nord" class="dark">
    <!-- Content -->
</html>
```

## Available CSS Variables

### Color Variables

All themes define the following CSS variables:

#### Gray Scale
- `--gray-base` - Base gray color
- `--gray-darker` - Darker gray (#222 equivalent)
- `--gray-dark` - Dark gray (#333 equivalent)
- `--gray` - Medium gray (#555 equivalent)
- `--gray-light` - Light gray (#777 equivalent)
- `--gray-lighter` - Lighter gray (#eee equivalent)
- `--gray-lightest` - Lightest gray (#f5f5f5 equivalent)

#### Brand Colors
- `--brand-primary` - Primary brand color
- `--brand-success` - Success color (green)
- `--brand-info` - Info color (blue/cyan)
- `--brand-warning` - Warning color (yellow/orange)
- `--brand-danger` - Danger color (red)

#### Scaffolding
- `--body-bg` - Background color for `<body>`
- `--text-color` - Global text color
- `--link-color` - Link color
- `--link-hover-color` - Link hover color

#### Typography
- `--font-family-sans-serif`
- `--font-family-serif`
- `--font-family-monospace`
- `--font-size-base`
- `--font-size-large`
- `--font-size-small`
- `--font-size-h1` through `--font-size-h6`

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

To create a new theme, add a new section in `resources/css/app.css`:

```css
/* Your Custom Theme */
[data-theme="custom"] {
    /* Gray scale */
    --gray-base: 0 0 0;
    --gray-darker: 34 34 34;
    --gray-dark: 51 51 51;
    /* ... etc ... */
    
    /* Brand colors */
    --brand-primary: 255 0 0;  /* Your primary color */
    --brand-success: 0 255 0;  /* Your success color */
    /* ... etc ... */
    
    /* Scaffolding */
    --body-bg: 255 255 255;
    --text-color: 0 0 0;
    /* ... etc ... */
}

/* Dark mode variant for your theme */
[data-theme="custom"].dark {
    /* Override variables for dark mode */
    --body-bg: 0 0 0;
    --text-color: 255 255 255;
    /* ... etc ... */
}
```

Then use it:

```html
<html data-theme="custom">
    <!-- Your themed content -->
</html>
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

## JavaScript Theme Switcher Example

```javascript
// Toggle between themes
function setTheme(themeName) {
    document.documentElement.setAttribute('data-theme', themeName);
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
    setTheme(savedTheme);
}

if (savedDarkMode) {
    document.documentElement.classList.add('dark');
}
```

## Notes

- All CSS variable values are in RGB format (e.g., `255 0 0` for red) to work with Tailwind's opacity modifiers
- HSL values are used for some variables (like sidebar) where noted in the code
- Colors automatically adapt based on the active theme and dark mode setting
- The `@source` directives ensure Tailwind scans all blade files in both `resources/views` and `Modules` directories
