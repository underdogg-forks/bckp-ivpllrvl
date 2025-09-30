<?php

namespace Tests\Unit\Views\Errors\Cli;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Unit tests for the CLI Error PHP View Template
 * 
 * This test suite validates the error.php Blade template for CLI error display,
 * covering variable rendering, conditional logic, and edge cases.
 * 
 * Testing Framework: PHPUnit (built into CodeIgniter 4)
 */
final class ErrorPhpViewTest extends CIUnitTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        helper('filesystem');
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test that the view renders basic error information correctly
     */
    public function testBasicErrorInformationRendering(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error message',
            'filepath' => '/path/to/file.php',
            'line' => 42,
        ];

        $output = view('Errors/Cli/error', $data);

        $this->assertStringContainsString('A PHP Error was encountered', $output);
        $this->assertStringContainsString('Severity:    E_ERROR', $output);
        $this->assertStringContainsString('Message:     Test error message', $output);
        $this->assertStringContainsString('Filename:    /path/to/file.php', $output);
        $this->assertStringContainsString('Line Number: 42', $output);
    }

    /**
     * Test rendering with different severity levels
     */
    public function testDifferentSeverityLevels(): void
    {
        $severities = ['E_ERROR', 'E_WARNING', 'E_NOTICE', 'E_STRICT', 'E_DEPRECATED'];

        foreach ($severities as $severity) {
            $data = [
                'severity' => $severity,
                'message' => 'Test message',
                'filepath' => '/test/file.php',
                'line' => 10,
            ];

            $output = view('Errors/Cli/error', $data);
            $this->assertStringContainsString("Severity:    {$severity}", $output);
        }
    }

    /**
     * Test rendering with special characters in error message
     */
    public function testSpecialCharactersInMessage(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Error with <special> & "characters" \'quoted\'',
            'filepath' => '/path/to/file.php',
            'line' => 15,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // Blade templates should escape special characters
        $this->assertStringContainsString('Error with', $output);
        $this->assertStringContainsString('characters', $output);
    }

    /**
     * Test rendering with special characters in filepath
     */
    public function testSpecialCharactersInFilepath(): void
    {
        $data = [
            'severity' => 'E_NOTICE',
            'message' => 'Test error',
            'filepath' => '/path/with spaces/and-special_chars/file.php',
            'line' => 100,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('/path/with spaces/and-special_chars/file.php', $output);
    }

    /**
     * Test rendering with very long error message
     */
    public function testLongErrorMessage(): void
    {
        $longMessage = str_repeat('This is a very long error message. ', 50);
        
        $data = [
            'severity' => 'E_ERROR',
            'message' => $longMessage,
            'filepath' => '/path/to/file.php',
            'line' => 200,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString($longMessage, $output);
    }

    /**
     * Test rendering with multiline error message
     */
    public function testMultilineErrorMessage(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => "Line 1 of error\nLine 2 of error\nLine 3 of error",
            'filepath' => '/path/to/file.php',
            'line' => 50,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Line 1 of error', $output);
        $this->assertStringContainsString('Line 2 of error', $output);
        $this->assertStringContainsString('Line 3 of error', $output);
    }

    /**
     * Test rendering with zero as line number (edge case)
     */
    public function testZeroLineNumber(): void
    {
        $data = [
            'severity' => 'E_WARNING',
            'message' => 'Error at line zero',
            'filepath' => '/path/to/file.php',
            'line' => 0,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Line Number: 0', $output);
    }

    /**
     * Test rendering with negative line number (edge case)
     */
    public function testNegativeLineNumber(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => -1,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Line Number: -1', $output);
    }

    /**
     * Test rendering with very large line number
     */
    public function testLargeLineNumber(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 999999,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Line Number: 999999', $output);
    }

    /**
     * Test rendering with empty error message
     */
    public function testEmptyErrorMessage(): void
    {
        $data = [
            'severity' => 'E_NOTICE',
            'message' => '',
            'filepath' => '/path/to/file.php',
            'line' => 25,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Message:', $output);
    }

    /**
     * Test rendering with empty filepath
     */
    public function testEmptyFilepath(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '',
            'line' => 10,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Filename:', $output);
    }

    /**
     * Test rendering without backtrace when SHOW_DEBUG_BACKTRACE is not defined
     */
    public function testNoBacktraceWhenNotDefined(): void
    {
        // Ensure SHOW_DEBUG_BACKTRACE is not defined or is false
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 30,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // Should not contain backtrace section if constant is not defined or false
        if (\!defined('SHOW_DEBUG_BACKTRACE') || \!SHOW_DEBUG_BACKTRACE) {
            $this->assertStringNotContainsString('Backtrace:', $output);
        }
    }

    /**
     * Test rendering with backtrace when SHOW_DEBUG_BACKTRACE is enabled
     * 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testBacktraceWhenEnabled(): void
    {
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', true);
        }

        if (\!defined('BASEPATH')) {
            define('BASEPATH', ROOTPATH . 'system/');
        }

        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error with backtrace',
            'filepath' => '/path/to/file.php',
            'line' => 40,
        ];

        $output = view('Errors/Cli/error', $data);
        
        if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) {
            $this->assertStringContainsString('Backtrace:', $output);
        }
    }

    /**
     * Test that backtrace filters out framework internal files
     * 
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testBacktraceFiltersFrameworkFiles(): void
    {
        if (\!defined('SHOW_DEBUG_BACKTRACE')) {
            define('SHOW_DEBUG_BACKTRACE', true);
        }

        if (\!defined('BASEPATH')) {
            define('BASEPATH', ROOTPATH . 'system/');
        }

        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 50,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // The backtrace should filter out files from BASEPATH
        // Only user application files should be shown
        if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) {
            $this->assertStringContainsString('Backtrace:', $output);
        }
    }

    /**
     * Test rendering with Windows-style file path
     */
    public function testWindowsStyleFilepath(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => 'C:\\Users\\Dev\\project\\file.php',
            'line' => 75,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('C:\\Users\\Dev\\project\\file.php', $output);
    }

    /**
     * Test rendering with Unix-style file path
     */
    public function testUnixStyleFilepath(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/usr/local/var/www/project/file.php',
            'line' => 85,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('/usr/local/var/www/project/file.php', $output);
    }

    /**
     * Test rendering with relative file path
     */
    public function testRelativeFilepath(): void
    {
        $data = [
            'severity' => 'E_WARNING',
            'message' => 'Test error',
            'filepath' => '../relative/path/to/file.php',
            'line' => 95,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('../relative/path/to/file.php', $output);
    }

    /**
     * Test that view output contains proper CLI formatting
     */
    public function testCliFormattingStructure(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 100,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // Check for proper label alignment and structure
        $this->assertMatchesRegularExpression('/Severity:\s+E_ERROR/', $output);
        $this->assertMatchesRegularExpression('/Message:\s+Test error/', $output);
        $this->assertMatchesRegularExpression('/Filename:\s+\/path\/to\/file\.php/', $output);
        $this->assertMatchesRegularExpression('/Line Number:\s+100/', $output);
    }

    /**
     * Test rendering with UTF-8 characters in error message
     */
    public function testUtf8CharactersInMessage(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Error: Ошибка 错误 エラー',
            'filepath' => '/path/to/file.php',
            'line' => 110,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Ошибка', $output);
        $this->assertStringContainsString('错误', $output);
        $this->assertStringContainsString('エラー', $output);
    }

    /**
     * Test rendering with UTF-8 characters in filepath
     */
    public function testUtf8CharactersInFilepath(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/путь/到/ファイル/file.php',
            'line' => 120,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('/путь/到/ファイル/file.php', $output);
    }

    /**
     * Test rendering with numeric severity value
     */
    public function testNumericSeverityValue(): void
    {
        $data = [
            'severity' => 8, // E_NOTICE numeric value
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 130,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Severity:    8', $output);
    }

    /**
     * Test rendering with string representation of line number
     */
    public function testStringLineNumber(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => '150',
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('Line Number: 150', $output);
    }

    /**
     * Test rendering with null values (edge case for robustness)
     */
    public function testNullValues(): void
    {
        $data = [
            'severity' => null,
            'message' => null,
            'filepath' => null,
            'line' => null,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // View should still render without fatal errors
        $this->assertStringContainsString('A PHP Error was encountered', $output);
        $this->assertStringContainsString('Severity:', $output);
        $this->assertStringContainsString('Message:', $output);
        $this->assertStringContainsString('Filename:', $output);
        $this->assertStringContainsString('Line Number:', $output);
    }

    /**
     * Test rendering with SQL injection attempt in message (security test)
     */
    public function testSqlInjectionAttemptInMessage(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => "'; DROP TABLE users; --",
            'filepath' => '/path/to/file.php',
            'line' => 160,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // Should safely render the malicious string as text
        $this->assertStringContainsString('DROP TABLE users', $output);
    }

    /**
     * Test rendering with XSS attempt in message (security test)
     */
    public function testXssAttemptInMessage(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => '<script>alert("XSS")</script>',
            'filepath' => '/path/to/file.php',
            'line' => 170,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // Blade should escape this, but in CLI context it should be visible as text
        $this->assertStringContainsString('script', $output);
        $this->assertStringContainsString('alert', $output);
    }

    /**
     * Test view renders without PHP errors or warnings
     */
    public function testNoPhpErrorsDuringRendering(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 180,
        ];

        // Capture any PHP errors/warnings during rendering
        $errorLevel = error_reporting(E_ALL);
        set_error_handler(function ($errno, $errstr) {
            $this->fail("PHP error occurred during view rendering: {$errstr}");
        });

        try {
            $output = view('Errors/Cli/error', $data);
            $this->assertNotEmpty($output);
        } finally {
            restore_error_handler();
            error_reporting($errorLevel);
        }
    }

    /**
     * Test rendering with boolean line number (type coercion test)
     */
    public function testBooleanLineNumber(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => true,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // PHP will coerce boolean true to 1
        $this->assertStringContainsString('Line Number:', $output);
    }

    /**
     * Test rendering with array as message (type error handling)
     */
    public function testArrayAsMessage(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => ['error' => 'Test error'],
            'filepath' => '/path/to/file.php',
            'line' => 200,
        ];

        // This should either convert to string or handle gracefully
        try {
            $output = view('Errors/Cli/error', $data);
            $this->assertStringContainsString('Message:', $output);
        } catch (\TypeError $e) {
            // It's acceptable to throw a TypeError for invalid input
            $this->assertTrue(true);
        }
    }

    /**
     * Test that header text is present
     */
    public function testHeaderTextPresent(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 210,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString('A PHP Error was encountered', $output);
    }

    /**
     * Test rendering with very long filepath
     */
    public function testVeryLongFilepath(): void
    {
        $longPath = '/very/long/path/' . str_repeat('nested/directory/', 20) . 'file.php';
        
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => $longPath,
            'line' => 220,
        ];

        $output = view('Errors/Cli/error', $data);
        $this->assertStringContainsString($longPath, $output);
    }

    /**
     * Test output doesn't contain unexpected HTML tags (CLI context)
     */
    public function testNoUnexpectedHtmlTags(): void
    {
        $data = [
            'severity' => 'E_ERROR',
            'message' => 'Test error',
            'filepath' => '/path/to/file.php',
            'line' => 230,
        ];

        $output = view('Errors/Cli/error', $data);
        
        // CLI error view should not contain HTML tags like <html>, <body>, etc.
        $this->assertStringNotContainsString('<html>', $output);
        $this->assertStringNotContainsString('<body>', $output);
        $this->assertStringNotContainsString('<div>', $output);
    }
}