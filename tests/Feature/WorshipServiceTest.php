<?php

namespace Tests\Feature;

use App\Models\MorningWorship;
use App\Services\WorshipService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WorshipServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorshipService $worshipService;

    public function setUp(): void
    {
        parent::setUp();
        $this->worshipService = $this->app->make(WorshipService::class);
    }

    #[Test]
    public function it_returns_paginated_worships(): void
    {
        // 1. Arrange: Create 20 worship records
        MorningWorship::factory()->count(20)->create();

        // 2. Act: Call the service method
        $result = $this->worshipService->getPaginatedWorships();

        // 3. Assert
        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertCount(15, $result->items());
        $this->assertEquals(20, $result->total());
    }

    #[Test]
    public function it_can_search_worships_by_title(): void
    {
        // 1. Arrange
        MorningWorship::factory()->create(['title' => 'A Very Specific Title To Find']);
        MorningWorship::factory()->count(5)->create(); // Create other random worships

        // 2. Act
        $result = $this->worshipService->getPaginatedWorships(search: 'A Very Specific Title');

        // 3. Assert
        $this->assertEquals(1, $result->total());
        $this->assertEquals('A Very Specific Title To Find', $result->items()[0]->title);
    }
}
