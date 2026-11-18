<?php

namespace Tests\Feature;

use Tests\TestCase;

class AdminLoginRedirectTest extends TestCase
{
    public function test_guest_visiting_admin_dashboard_is_sent_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect(route('login', ['role' => 'admin']));
    }

    public function test_guest_visiting_faculty_dashboard_is_sent_to_faculty_login(): void
    {
        $response = $this->get('/faculty/dashboard');

        $response->assertRedirect(route('login', ['role' => 'faculty']));
    }
}