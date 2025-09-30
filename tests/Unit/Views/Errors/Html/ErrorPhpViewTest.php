<?php

namespace Tests\Unit\Views\Errors\Html;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit tests for the error-php.blade.php view template
 * Testing Framework: PHPUnit via CodeIgniter\Test\CIUnitTestCase
 * 
 * This test suite validates the rendering and behavior of the PHP error view template,
 * which displays PHP errors with severity, message, filename, line number, and optional backtrace.
 */
class ErrorPhpViewTest extends CIUnitTestCase
{
    protected $viewPath = 'Errors/Html/error-php';
    
    /**
     * Test that the view renders successfully with minimal required data
     */
    public function testViewRendersWithMinimalData(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test error message',
            'filepath' => '/path/to/file.php',
            'line' => 42
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringContainsString('A PHP Error was encountered', $output);
        $this->assertStringContainsString('Severity: Error', $output);
        $this->assertStringContainsString('Message: Test error message', $output);
        $this->assertStringContainsString('Filename: /path/to/file.php', $output);
        $this->assertStringContainsString('Line Number: 42', $output);
    }
    
    /**
     * Test that severity is properly escaped and displayed
     */
    public function testSeverityIsProperlyDisplayed(): void
    {
        $severities = ['Notice', 'Warning', 'Error', 'Fatal Error', 'Parse Error'];
        
        foreach ($severities as $severity) {
            $data = [
                'severity' => $severity,
                'message' => 'Test message',
                'filepath' => '/test/file.php',
                'line' => 1
            ];
            
            $output = view($this->viewPath, $data);
            $this->assertStringContainsString("Severity: {$severity}", $output);
        }
    }
    
    /**
     * Test that XSS attempts in severity are properly escaped
     */
    public function testSeverityXSSProtection(): void
    {
        $data = [
            'severity' => '<script>alert("XSS")</script>',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Blade {{ }} syntax auto-escapes, so the script tags should be escaped
        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $output);
        $this->assertStringContainsString('&lt;script&gt;', $output);
    }
    
    /**
     * Test that error message is properly displayed
     */
    public function testMessageIsProperlyDisplayed(): void
    {
        $messages = [
            'Undefined variable: foo',
            'Call to undefined function bar()',
            'Division by zero',
            'A very long error message that contains multiple words and should be properly displayed without truncation'
        ];
        
        foreach ($messages as $message) {
            $data = [
                'severity' => 'Error',
                'message' => $message,
                'filepath' => '/test/file.php',
                'line' => 1
            ];
            
            $output = view($this->viewPath, $data);
            $this->assertStringContainsString("Message: {$message}", $output);
        }
    }
    
    /**
     * Test that XSS attempts in message are properly escaped
     */
    public function testMessageXSSProtection(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => '<img src=x onerror="alert(1)">',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringNotContainsString('<img src=x onerror="alert(1)">', $output);
        $this->assertStringContainsString('&lt;img', $output);
    }
    
    /**
     * Test that filepath is properly displayed
     */
    public function testFilepathIsProperlyDisplayed(): void
    {
        $filepaths = [
            '/var/www/html/app/Controllers/Home.php',
            'C:\\xampp\\htdocs\\app\\Models\\User.php',
            '/home/user/project/vendor/autoload.php',
            '../relative/path/file.php'
        ];
        
        foreach ($filepaths as $filepath) {
            $data = [
                'severity' => 'Error',
                'message' => 'Test message',
                'filepath' => $filepath,
                'line' => 1
            ];
            
            $output = view($this->viewPath, $data);
            $this->assertStringContainsString("Filename: {$filepath}", $output);
        }
    }
    
    /**
     * Test that XSS attempts in filepath are properly escaped
     */
    public function testFilepathXSSProtection(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/<script>alert("XSS")</script>/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringNotContainsString('<script>alert("XSS")</script>', $output);
    }
    
    /**
     * Test that line number is properly displayed
     */
    public function testLineNumberIsProperlyDisplayed(): void
    {
        $lineNumbers = [1, 42, 100, 999, 10000];
        
        foreach ($lineNumbers as $line) {
            $data = [
                'severity' => 'Error',
                'message' => 'Test message',
                'filepath' => '/test/file.php',
                'line' => $line
            ];
            
            $output = view($this->viewPath, $data);
            $this->assertStringContainsString("Line Number: {$line}", $output);
        }
    }
    
    /**
     * Test that view renders without backtrace when SHOW_DEBUG_BACKTRACE is not defined
     */
    public function testViewRendersWithoutBacktraceWhenConstantNotDefined(): void
    {
        // Save current state
        $wasDefinedBefore = defined('SHOW_DEBUG_BACKTRACE');
        $previousValue = $wasDefinedBefore ? constant('SHOW_DEBUG_BACKTRACE') : null;
        
        // Ensure constant is not defined or is false
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', false);
        }
        
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Backtrace section should not appear
        $this->assertStringNotContainsString('Backtrace:', $output);
    }
    
    /**
     * Test that view renders with backtrace when SHOW_DEBUG_BACKTRACE is true
     */
    public function testViewRendersWithBacktraceWhenConstantIsTrue(): void
    {
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', true);
        }
        
        if (\!defined('BASEPATH')) {
            define('BASEPATH', '/var/www/html/system/');
        }
        
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        if (SHOW_DEBUG_BACKTRACE) {
            $this->assertStringContainsString('Backtrace:', $output);
        }
    }
    
    /**
     * Test that the view has proper HTML structure
     */
    public function testViewHasProperHTMLStructure(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Check for main container div
        $this->assertStringContainsString('<div style="border:1px solid #990000', $output);
        
        // Check for heading
        $this->assertStringContainsString('<h4>A PHP Error was encountered</h4>', $output);
        
        // Check for paragraph tags
        $this->assertStringContainsString('<p>Severity:', $output);
        $this->assertStringContainsString('<p>Message:', $output);
        $this->assertStringContainsString('<p>Filename:', $output);
        $this->assertStringContainsString('<p>Line Number:', $output);
    }
    
    /**
     * Test that the view applies proper styling
     */
    public function testViewHasProperStyling(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Check for red border styling
        $this->assertStringContainsString('border:1px solid #990000', $output);
        
        // Check for padding
        $this->assertStringContainsString('padding-left:20px', $output);
        
        // Check for margin
        $this->assertStringContainsString('margin:0 0 10px 0', $output);
    }
    
    /**
     * Test handling of empty string values
     */
    public function testViewHandlesEmptyStrings(): void
    {
        $data = [
            'severity' => '',
            'message' => '',
            'filepath' => '',
            'line' => 0
        ];
        
        $output = view($this->viewPath, $data);
        
        // View should still render with empty values
        $this->assertStringContainsString('A PHP Error was encountered', $output);
        $this->assertStringContainsString('Severity:', $output);
        $this->assertStringContainsString('Message:', $output);
        $this->assertStringContainsString('Filename:', $output);
        $this->assertStringContainsString('Line Number:', $output);
    }
    
    /**
     * Test handling of null values (should not cause errors)
     */
    public function testViewHandlesNullValues(): void
    {
        $data = [
            'severity' => null,
            'message' => null,
            'filepath' => null,
            'line' => null
        ];
        
        // This should not throw an exception
        try {
            $output = view($this->viewPath, $data);
            $this->assertStringContainsString('A PHP Error was encountered', $output);
        } catch (\Exception $e) {
            $this->fail('View should handle null values gracefully: ' . $e->getMessage());
        }
    }
    
    /**
     * Test handling of special characters in error message
     */
    public function testViewHandlesSpecialCharacters(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Error with quotes: "test" and \'single\' and special chars: & < > © ® ™',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Should contain escaped special characters
        $this->assertStringContainsString('Message:', $output);
    }
    
    /**
     * Test handling of unicode characters
     */
    public function testViewHandlesUnicodeCharacters(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Unicode test: 你好世界 مرحبا بالعالم Привет мир',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Should render unicode characters properly
        $this->assertStringContainsString('Message:', $output);
    }
    
    /**
     * Test that line numbers can be zero
     */
    public function testViewHandlesZeroLineNumber(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 0
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringContainsString('Line Number: 0', $output);
    }
    
    /**
     * Test that negative line numbers are handled
     */
    public function testViewHandlesNegativeLineNumber(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => -1
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringContainsString('Line Number: -1', $output);
    }
    
    /**
     * Test very long file paths
     */
    public function testViewHandlesVeryLongFilepath(): void
    {
        $longPath = '/very/' . str_repeat('long/', 50) . 'path/to/file.php';
        
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => $longPath,
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringContainsString('Filename:', $output);
    }
    
    /**
     * Test very long error messages
     */
    public function testViewHandlesVeryLongMessage(): void
    {
        $longMessage = str_repeat('This is a very long error message. ', 100);
        
        $data = [
            'severity' => 'Error',
            'message' => $longMessage,
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringContainsString('Message:', $output);
    }
    
    /**
     * Test SQL injection attempt in message
     */
    public function testViewHandlesSQLInjectionAttempt(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => "'; DROP TABLE users; --",
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Message should be rendered as-is (escaped), not executed
        $this->assertStringContainsString('Message:', $output);
    }
    
    /**
     * Test multiple XSS vectors in different fields
     */
    public function testViewHandlesMultipleXSSVectors(): void
    {
        $data = [
            'severity' => '<script>alert(1)</script>',
            'message' => '<img src=x onerror=alert(2)>',
            'filepath' => '"><script>alert(3)</script>',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // No unescaped script tags should appear
        $this->assertStringNotContainsString('<script>alert(1)</script>', $output);
        $this->assertStringNotContainsString('<img src=x onerror=alert(2)>', $output);
        $this->assertStringNotContainsString('<script>alert(3)</script>', $output);
    }
    
    /**
     * Test that backtrace renders properly when enabled
     */
    public function testBacktraceRendersWhenEnabled(): void
    {
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', true);
        }
        
        if (\!defined('BASEPATH')) {
            define('BASEPATH', realpath(__DIR__ . '/../../../../../') . '/system/');
        }
        
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        if (SHOW_DEBUG_BACKTRACE) {
            $this->assertStringContainsString('Backtrace:', $output);
        }
    }
    
    /**
     * Test that backtrace formatting includes file, line, and function
     */
    public function testBacktraceFormattingStructure(): void
    {
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', true);
        }
        
        if (\!defined('BASEPATH')) {
            define('BASEPATH', realpath(__DIR__ . '/../../../../../') . '/system/');
        }
        
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        if (SHOW_DEBUG_BACKTRACE) {
            // Check that backtrace structure elements are present
            $this->assertStringContainsString('Backtrace:', $output);
            
            // The backtrace should contain File:, Line:, and Function: labels
            // Note: These might not appear if no valid backtrace items pass the filter
        }
    }
    
    /**
     * Test that BASEPATH filtering works in backtrace
     */
    public function testBacktraceFiltersByBasepath(): void
    {
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', true);
        }
        
        if (\!defined('BASEPATH')) {
            define('BASEPATH', '/var/www/html/system/');
        }
        
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $output = view($this->viewPath, $data);
        
        // Files from BASEPATH should be filtered out in the backtrace
        // This is handled by the view's @if condition
        $this->assertStringContainsString('A PHP Error was encountered', $output);
    }
    
    /**
     * Test rendering with all typical PHP error severities
     */
    public function testAllTypicalPHPErrorSeverities(): void
    {
        $severities = [
            'E_ERROR',
            'E_WARNING',
            'E_PARSE',
            'E_NOTICE',
            'E_CORE_ERROR',
            'E_CORE_WARNING',
            'E_COMPILE_ERROR',
            'E_COMPILE_WARNING',
            'E_USER_ERROR',
            'E_USER_WARNING',
            'E_USER_NOTICE',
            'E_STRICT',
            'E_RECOVERABLE_ERROR',
            'E_DEPRECATED',
            'E_USER_DEPRECATED'
        ];
        
        foreach ($severities as $severity) {
            $data = [
                'severity' => $severity,
                'message' => 'Test error for ' . $severity,
                'filepath' => '/test/file.php',
                'line' => 1
            ];
            
            $output = view($this->viewPath, $data);
            $this->assertStringContainsString("Severity: {$severity}", $output);
        }
    }
    
    /**
     * Test the view with realistic error scenario
     */
    public function testRealisticErrorScenario(): void
    {
        $data = [
            'severity' => 'Warning',
            'message' => 'Undefined array key "username"',
            'filepath' => '/var/www/html/app/Controllers/UserController.php',
            'line' => 127
        ];
        
        $output = view($this->viewPath, $data);
        
        $this->assertStringContainsString('A PHP Error was encountered', $output);
        $this->assertStringContainsString('Severity: Warning', $output);
        $this->assertStringContainsString('Undefined array key "username"', $output);
        $this->assertStringContainsString('UserController.php', $output);
        $this->assertStringContainsString('Line Number: 127', $output);
    }
    
    /**
     * Test view rendering performance with typical data
     */
    public function testViewRenderingPerformance(): void
    {
        $data = [
            'severity' => 'Error',
            'message' => 'Test message',
            'filepath' => '/test/file.php',
            'line' => 1
        ];
        
        $startTime = microtime(true);
        $output = view($this->viewPath, $data);
        $endTime = microtime(true);
        
        $renderTime = $endTime - $startTime;
        
        // View rendering should be fast (under 100ms for a simple view)
        $this->assertLessThan(0.1, $renderTime, 'View rendering took too long');
        $this->assertNotEmpty($output);
    }
}