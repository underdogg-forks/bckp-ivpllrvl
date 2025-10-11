# Bootstrap to Tailwind CSS Conversion Guide

## Overview
This document provides examples of converting Bootstrap classes to Tailwind CSS with dark mode support.

## Common Pattern Conversions

### Buttons

#### Bootstrap → Tailwind
```blade
<!-- Bootstrap -->
<a class="btn btn-default">Back</a>
<a class="btn btn-primary">Save</a>
<a class="btn btn-success">Create</a>
<a class="btn btn-danger">Delete</a>

<!-- Tailwind with Dark Mode -->
<a class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600">Back</a>
<a class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded hover:bg-blue-700 dark:hover:bg-blue-600">Save</a>
<a class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded hover:bg-green-700 dark:hover:bg-green-600">Create</a>
<a class="px-4 py-2 bg-red-600 dark:bg-red-500 text-white rounded hover:bg-red-700 dark:hover:bg-red-600">Delete</a>
```

### Panels/Cards

#### Bootstrap → Tailwind
```blade
<!-- Bootstrap -->
<div class="panel panel-default">
    <div class="panel-heading">
        Title
    </div>
    <div class="panel-body">
        Content
    </div>
</div>

<!-- Tailwind with Dark Mode -->
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Title</h3>
    </div>
    <div class="p-4 text-gray-700 dark:text-gray-300">
        Content
    </div>
</div>
```

### Grid System

#### Bootstrap → Tailwind
```blade
<!-- Bootstrap -->
<div class="row">
    <div class="col-xs-12 col-md-6">Column 1</div>
    <div class="col-xs-12 col-md-6">Column 2</div>
</div>

<!-- Tailwind with Dark Mode -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="text-gray-700 dark:text-gray-300">Column 1</div>
    <div class="text-gray-700 dark:text-gray-300">Column 2</div>
</div>
```

### Tables

#### Bootstrap → Tailwind
```blade
<!-- Bootstrap -->
<table class="table table-hover table-striped">
    <thead>
        <tr>
            <th>Header</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Data</td>
        </tr>
    </tbody>
</table>

<!-- Tailwind with Dark Mode -->
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
        <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                Header
            </th>
        </tr>
    </thead>
    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                Data
            </td>
        </tr>
    </tbody>
</table>
```

### Alerts

#### Bootstrap → Tailwind
```blade
<!-- Bootstrap -->
<div class="alert alert-success">Success message</div>
<div class="alert alert-danger">Error message</div>
<div class="alert alert-warning">Warning message</div>
<div class="alert alert-info">Info message</div>

<!-- Tailwind with Dark Mode -->
<div class="p-4 mb-4 text-green-700 dark:text-green-200 bg-green-100 dark:bg-green-900 rounded-lg">Success message</div>
<div class="p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900 rounded-lg">Error message</div>
<div class="p-4 mb-4 text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900 rounded-lg">Warning message</div>
<div class="p-4 mb-4 text-blue-700 dark:text-blue-200 bg-blue-100 dark:bg-blue-900 rounded-lg">Info message</div>
```

### Forms

#### Bootstrap → Tailwind
```blade
<!-- Bootstrap -->
<div class="form-group">
    <label for="email">Email</label>
    <input type="email" class="form-control" id="email">
</div>

<!-- Tailwind with Dark Mode -->
<div class="mb-4">
    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Email
    </label>
    <input 
        type="email" 
        id="email"
        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400"
    >
</div>
```

## Example File Conversion

This is a **hypothetical example** showing how to convert a Module file from Bootstrap to Tailwind CSS. The "Before" code represents the current state of similar files in the codebase that still need conversion.

### Before: Current Bootstrap Pattern (Example from Modules/UserClients/Resources/views/field.blade.php)

```blade
<div id="headerbar">
    <h1 class="headerbar-title">@lang('assigned_clients')</h1>
    
    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default" href="{{ url('users') }}">
                <i class="fa fa-arrow-left"></i> @lang('back')
            </a>
            <a class="btn btn-primary" href="{{ url('user_clients/create/' . $id) }}">
                <i class="fa fa-plus"></i> @lang('new')
            </a>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        {{ __('user') . ': ' . htmlsc($user->user_name) }}
    </div>
    <div class="panel-body">
        <table class="table table-hover table-striped">
            <!-- table content -->
        </table>
    </div>
</div>
```

### After: Tailwind with Dark Mode

```blade
<div class="bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            @lang('assigned_clients')
        </h1>
        
        <div class="flex gap-2">
            <a 
                href="{{ url('users') }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <i class="fa fa-arrow-left"></i>
                @lang('back')
            </a>
            <a 
                href="{{ url('user_clients/create/' . $id) }}" 
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                <i class="fa fa-plus"></i>
                @lang('new')
            </a>
        </div>
    </div>
</div>

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('user') . ': ' . htmlsc($user->user_name) }}
        </h3>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('client')
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        @lang('options')
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($user_clients as $user_client)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        <a 
                            href="{{ url('clients/view/' . $user_client->client_id) }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                        >
                            {!! format_client($user_client) !!}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        <form
                            action="{{ url('user_clients/delete/' . $user_client->user_client_id) }}"
                            method="POST"
                        >
                            @csrf
                            <button 
                                type="submit" 
                                onclick="return confirm('@lang('delete_user_client_warning')');"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-red-600 dark:bg-red-500 text-white rounded hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                            >
                                <i class="fa fa-trash-o"></i>
                                @lang('remove')
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
```

## Dark Mode Best Practices

1. **Always provide both light and dark variants:**
   ```blade
   class="bg-white dark:bg-gray-800"
   ```

2. **Use semantic color scales:**
   - Light backgrounds: `bg-white`, `bg-gray-50`, `bg-gray-100`
   - Dark backgrounds: `bg-gray-800`, `bg-gray-900`, `bg-black`

3. **Ensure sufficient contrast:**
   - Light text on dark: `text-gray-900 dark:text-gray-100`
   - Borders: `border-gray-300 dark:border-gray-600`

4. **Test in both modes:**
   - Use browser dev tools to toggle `dark` class
   - Check all interactive states (hover, focus, active)

## Automation

For bulk conversion, consider:
1. Creating a conversion script (similar to blade fixers)
2. Converting incrementally, module by module
3. Testing each conversion thoroughly
4. Maintaining a component library for consistency

## Resources

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Dark Mode Guide](https://tailwindcss.com/docs/dark-mode)
- [Bootstrap to Tailwind Converter](https://tailwind-converter.netlify.app/)
