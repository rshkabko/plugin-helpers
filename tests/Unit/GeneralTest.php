<?php

namespace Flamix\Plugin\Tests\Unit;

use Flamix\Plugin\General\Helpers;
use Flamix\Plugin\General\Status;
use PHPUnit\Framework\TestCase;

class GeneralTest extends TestCase
{
    public function test_status_key_is_upper_snake_case_cut_to_60()
    {
        $this->assertSame('SENT_TO_CRM', Status::key('Sent to CRM'));
        $this->assertSame('PRIVET_MIR', Status::key('Привет, мир!'));
        $this->assertSame(60, strlen(Status::key(str_repeat('a', 80))));
    }

    public function test_parse_domain_with_and_without_a_scheme()
    {
        $this->assertSame('shop.com', Helpers::parseDomain('https://shop.com/catalog?x=1'));
        $this->assertSame('shop.com', Helpers::parseDomain('shop.com/catalog'));
    }

    public function test_parse_domain_of_a_host_that_contains_http()
    {
        $this->assertSame('httpbin.org', Helpers::parseDomain('httpbin.org'));
        $this->assertSame('portal.bitrix24.com', Helpers::parseDomain('portal.bitrix24.com/http'));
        $this->assertSame('Portal.bitrix24.com', Helpers::parseDomain('HTTPS://Portal.bitrix24.com'));
    }
}
