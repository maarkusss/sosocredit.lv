<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StripPhoneFormattingTest extends TestCase
{
    public function test_helper_removes_extension_that_starts_with_plus(): void
    {
        $phone_extension = '+7';
        $full_phone_number = '+71234567890';
        $expected_phone_number = '1234567890';

        $stripped = strip_phone_number_formatting($full_phone_number, $phone_extension);

        $this->assertNotEquals($stripped, $full_phone_number);
        $this->assertEquals($stripped, $expected_phone_number);
    }

    public function test_helper_adds_plus_if_extension_does_not_start_with_plus(): void
    {
        $phone_extension = '7';
        $expected_phone_number = '1234567890';
        $full_phone_number = '+71234567890';

        $stripped = strip_phone_number_formatting($full_phone_number, $phone_extension);

        $this->assertNotEquals($stripped, $full_phone_number);
        $this->assertEquals($stripped, $expected_phone_number);
    }

    public function test_helper_removes_brackets(): void
    {
        $phone_extension = '380';
        $expected_phone_number = '121234567';
        $full_phone_number = '+38(012)1234567';

        $stripped = strip_phone_number_formatting($full_phone_number, $phone_extension);

        $this->assertNotEquals($stripped, $full_phone_number);
        $this->assertEquals($stripped, $expected_phone_number);
    }
}
