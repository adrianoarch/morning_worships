<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\WorshipService;
use Illuminate\Support\Facades\Auth;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorshipServiceTest extends TestCase
{
    private WorshipService $worshipService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->worshipService = new WorshipService();
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_returns_watched_worships_count_for_authenticated_user(): void
    {
        // 1. Create a mock using Mockery
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('watchedWorships->count')->andReturn(5);

        // 2. Mock the Auth facade
        Auth::shouldReceive('user')->once()->andReturn($userMock);

        // 3. Call the service method and assert
        $this->assertEquals(5, $this->worshipService->getWatchedWorshipsCount());
    }

    #[Test]
    public function it_returns_zero_for_guest_user(): void
    {
        // This test can remain as is, but for consistency, we can also use Mockery for Auth
        Auth::shouldReceive('user')->once()->andReturn(null);

        $this->assertEquals(0, $this->worshipService->getWatchedWorshipsCount());
    }
}
