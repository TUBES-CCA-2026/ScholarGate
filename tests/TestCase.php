<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Base test case untuk seluruh test berbasis Laravel.
 *
 * Test feature ScholarGate mewarisi class ini agar memiliki akses ke kernel,
 * route, container, dan helper pengujian Laravel.
 */
abstract class TestCase extends BaseTestCase
{
    // Konfigurasi bersama test dapat ditambahkan di sini ketika diperlukan.
}
