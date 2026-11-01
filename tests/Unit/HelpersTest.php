<?php

namespace Flamix\Plugin\Tests\Unit;

use Flamix\Plugin\Init;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    private string $dir;

    protected function tearDown(): void
    {
        $date = date('Y-m-d');
        @unlink("{$this->dir}/logs/orders/{$date}-info-" . md5($date) . '.log');
        @rmdir("{$this->dir}/logs/orders");
        @rmdir("{$this->dir}/logs");
        @rmdir($this->dir);
    }

    public function test_flamix_log_writes_to_the_channel_folder()
    {
        $this->dir = sys_get_temp_dir() . '/plugin-helpers-test-' . getmypid();
        Init::init('/var/www/plugins/flamix-log-test', 'FLAMIX_LOG_TEST')->setLogsPath($this->dir);
        $date = date('Y-m-d');
        $file = FLAMIX_LOGS_PATH . "/logs/orders/{$date}-info-" . md5($date) . '.log';

        flamix_log('Order sent', ['id' => 7], 'orders');

        $this->assertFileExists($file);
        $this->assertStringContainsString('commerceml.INFO: Order sent {"id":7}', file_get_contents($file));
    }
}
