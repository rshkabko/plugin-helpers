<?php

namespace Flamix\Plugin\Tests\Unit;

use Flamix\Plugin\Init;
use PHPUnit\Framework\TestCase;

class InitTest extends TestCase
{
    public function test_init_defines_the_plugin_constants_once()
    {
        $plugin = Init::init('/var/www/wp-content/plugins/flamix-exchange', 'FLAMIX_INIT_TEST');

        $this->assertSame('flamix-exchange', $plugin->getPluginCode());
        $this->assertSame('flamix-exchange', FLAMIX_INIT_TEST_CODE);
        $this->assertSame('/var/www/wp-content/plugins/flamix-exchange', FLAMIX_INIT_TEST_DIR);

        $this->expectExceptionMessage('Plugin already defined!');
        Init::init('/other/flamix-exchange', 'FLAMIX_INIT_TEST');
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_defined_before_set_logs_path()
    {
        $this->assertArrayHasKey('FLAMIX_DEFINED_TEST_CODE', Init::init('/plugins/defined-test', 'FLAMIX_DEFINED_TEST')->defined());
    }
}
