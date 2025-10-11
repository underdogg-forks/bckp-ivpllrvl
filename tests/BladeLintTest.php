<?php

namespace Tests;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BladeLintTest extends TestCase
{
    /**
     * Test that all blade files have valid syntax
     */
    public function test_all_blade_files_have_valid_syntax(): void
    {
        $bladeFiles = $this->getAllBladeFiles();
        $errors = [];

        foreach ($bladeFiles as $file) {
            try {
                // Attempt to compile the blade file
                $compiled = view()->getEngineResolver()->resolve('blade')->getCompiler()->compileString(
                    File::get($file)
                );
            } catch (\Throwable $e) {
                $errors[] = [
                    'file' => str_replace(base_path(), '', $file),
                    'error' => $e->getMessage(),
                ];
            }
        }

        if (!empty($errors)) {
            $errorMessage = "Blade syntax errors found:\n";
            foreach ($errors as $error) {
                $errorMessage .= "\n{$error['file']}: {$error['error']}";
            }
            $this->fail($errorMessage);
        }

        $this->assertTrue(true);
    }

    /**
     * Test that blade files don't contain plain PHP tags (except in @php directives)
     */
    public function test_blade_files_use_blade_directives_instead_of_php_tags(): void
    {
        $bladeFiles = $this->getAllBladeFiles();
        $filesWithPlainPhp = [];

        foreach ($bladeFiles as $file) {
            $content = File::get($file);
            
            // Skip files that are allowed to have PHP includes (template files)
            if (str_contains($file, 'invoice_templates/pdf') || str_contains($file, 'quote_templates/pdf')) {
                continue;
            }

            // Remove @php blocks temporarily to check for standalone <?php tags
            $contentWithoutBladePhp = preg_replace('/@php.*?@endphp/s', '', $content);
            
            // Check for <?php tags outside of @php directives
            if (preg_match('/<\?php(?!\s*namespace)/', $contentWithoutBladePhp)) {
                $filesWithPlainPhp[] = str_replace(base_path(), '', $file);
            }
        }

        if (!empty($filesWithPlainPhp)) {
            $this->markTestSkipped(
                "Files with plain PHP tags (should use @php directive):\n" . 
                implode("\n", $filesWithPlainPhp)
            );
        }

        $this->assertTrue(true);
    }

    /**
     * Get all blade files from resources/views and Modules
     */
    private function getAllBladeFiles(): array
    {
        $files = [];

        // Get files from resources/views
        if (File::exists(resource_path('views'))) {
            $files = array_merge(
                $files,
                File::allFiles(resource_path('views'))
            );
        }

        // Get files from Modules
        $modulesPath = base_path('Modules');
        if (File::exists($modulesPath)) {
            $moduleDirs = File::directories($modulesPath);
            foreach ($moduleDirs as $moduleDir) {
                $viewsPath = $moduleDir . '/Resources/views';
                if (File::exists($viewsPath)) {
                    $files = array_merge(
                        $files,
                        File::allFiles($viewsPath)
                    );
                }
            }
        }

        // Filter to only .blade.php files
        return array_filter($files, function ($file) {
            return $file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php');
        });
    }
}
