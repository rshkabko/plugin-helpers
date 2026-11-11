<?php

namespace Flamix\Plugin\Tests\Unit;

use Flamix\Plugin\General\Checker;
use Flamix\Plugin\WP\Recommendations;
use PHPUnit\Framework\TestCase;

/**
 * WordPress functions are stubbed globally, so each test runs in its own process.
 *
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
#[\PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses]
#[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
class WordPressTest extends TestCase
{
    public function test_plugin_check_works_without_the_multisite_constant()
    {
        $this->stub('function get_option($name, $default = false) { return ["contact-form-7/wp-contact-form-7.php"]; }');

        $this->assertFalse(defined('MULTISITE'));
        $this->assertTrue(Checker::isPluginActive('contact-form-7/wp-contact-form-7.php'));
        $this->assertFalse(Checker::isPluginActive('woocommerce'));
    }

    public function test_network_plugins_count_on_multisite()
    {
        $this->stub('function get_option($name, $default = false) { return []; }
            function is_multisite() { return true; }
            function get_site_option($name, $default = false) { return ["woocommerce/woocommerce.php" => 1]; }');

        $this->assertTrue(Checker::isPluginActive('woocommerce'));
    }

    public function test_ukrainian_locale_gets_the_ua_banner()
    {
        $this->stub('function get_locale() { return "uk"; }');

        $this->assertStringContainsString('/banner/ua/300/600/image', Recommendations::banner('cf7'));
    }

    public function test_other_locales_get_their_banner_or_en()
    {
        $this->stub('function get_locale() { return $GLOBALS["locale"]; }');

        $GLOBALS['locale'] = 'ru_RU';
        $this->assertStringContainsString('/banner/ru/', Recommendations::banner('cf7'));
        $GLOBALS['locale'] = 'de_DE';
        $this->assertStringContainsString('/banner/en/', Recommendations::banner('cf7'));
    }

    private function stub(string $functions): void
    {
        eval($functions);
    }
}
