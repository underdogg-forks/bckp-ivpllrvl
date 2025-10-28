<?php

namespace Modules\Users\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Users\Models\User;
use Modules\Users\Services\UsersService;
use Tests\TestCase;

class UsersServiceSaveTest extends TestCase
{
    use RefreshDatabase;

    private UsersService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(UsersService::class);
    }

    public function test_save_with_db_array_only(): void
    {
        $data = [
            'user_name' => 'Test User',
            'user_email' => 'testuser@example.com',
            'user_password' => 'password123',
        ];
        $id = $this->service->save($data);
        $this->assertNotNull($id);
        $user = User::find($id);
        $this->assertNotNull($user);
        $this->assertEquals('Test User', $user->user_name);
        $this->assertEquals('testuser@example.com', $user->user_email);
    }

    public function test_save_with_request_and_db_array(): void
    {
        $request = request();
        $data = [
            'user_name' => 'Request User',
            'user_email' => 'requestuser@example.com',
            'user_password' => 'password456',
        ];
        $id = $this->service->save($request, null, $data);
        $this->assertNotNull($id);
        $user = User::find($id);
        $this->assertNotNull($user);
        $this->assertEquals('Request User', $user->user_name);
        $this->assertEquals('requestuser@example.com', $user->user_email);
    }
}

