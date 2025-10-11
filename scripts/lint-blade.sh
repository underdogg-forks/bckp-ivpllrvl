#!/bin/bash

# Blade Linting Script
# This script checks all blade files for syntax errors

set -e

echo "🔍 Linting Blade files..."

# Run the blade lint test
php artisan test --filter=BladeLintTest

echo "✅ Blade linting complete!"
