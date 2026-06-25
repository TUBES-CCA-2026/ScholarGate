<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Smoke test dasar untuk memastikan landing page publik dapat diakses.
 */
class ExampleTest extends TestCase
{
    /**
     * Landing page ScholarGate harus merespons HTTP 200.
     */
    public function test_the_landing_page_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
