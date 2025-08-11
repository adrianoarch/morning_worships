<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\WorshipService;
use Illuminate\Support\Facades\Auth;
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

    #[Test]
    public function it_returns_watched_worships_count_for_authenticated_user(): void
    {
        // 1. Create a mock of the User model
        $userMock = $this->createMock(User::class);

        // 2. Mock the relationship chain
        $relationshipMock = $this->createMock(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
        $relationshipMock->method('count')->willReturn(5);
        $userMock->method('watchedWorships')->willReturn($relationshipMock);

        // 3. Mock the Auth facade
        Auth::shouldReceive('user')->once()->andReturn($userMock);

        // 4. Call the service method and assert
        $this->assertEquals(5, $this->worshipService->getWatchedWorshipsCount());
    }

    #[Test]
    public function it_returns_zero_for_guest_user(): void
    {
        // 1. Mock the Auth facade to return null
        Auth::shouldReceive('user')->once()->andReturn(null);

        // 2. Call the service method and assert
        $this->assertEquals(0, $this->worshipService->getWatchedWorshipsCount());
    }
}
