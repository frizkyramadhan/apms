<?php

namespace Tests\Feature;

use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_guest_is_sent_to_login_from_user_pages(): void
    {
        $this->get('/users')->assertRedirect('/login');
        $this->get('/roles')->assertRedirect('/login');
        $this->get('/permissions')->assertRedirect('/login');
    }
}
