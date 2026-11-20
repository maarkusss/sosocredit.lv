<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_with_no_locale_returns_redirect()
    {
        $response = $this->get('/');

        $response->assertStatus(302);
    }
}
