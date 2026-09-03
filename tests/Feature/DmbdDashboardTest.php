<?php

namespace Tests\Feature;

use Tests\TestCase;

class DmbdDashboardTest extends TestCase
{
    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk()->assertSee('ARKA Plant Management System');
    }

    public function test_master_unit_pages_require_login(): void
    {
        $this->get('/dmbd/units')->assertRedirect('/login');
        $this->get('/dmbd/units/data')->assertRedirect('/login');
    }

    public function test_master_unit_data_returns_datatable_payload(): void
    {
        $user = \App\Models\User::query()->where('username', 'admin')->first();
        if (! $user) {
            $this->markTestSkipped('admin user missing');
        }

        $this->actingAs($user)
            ->getJson('/dmbd/units/data?draw=1&start=0&length=10&status=ACTIVE&order[0][column]=0&order[0][dir]=asc')
            ->assertOk()
            ->assertJsonStructure(['draw', 'recordsTotal', 'recordsFiltered', 'data']);
    }
}
