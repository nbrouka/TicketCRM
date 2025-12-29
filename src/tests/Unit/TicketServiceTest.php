<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Managers\TicketManager;
use App\Models\Ticket;
use App\Services\TicketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TicketService $ticketService;

    protected TicketManager $ticketManager;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock repository for the TicketManager
        $ticketRepositoryMock = $this->createMock(\App\Repositories\Interfaces\TicketRepositoryInterface::class);

        $this->ticketManager = new TicketManager($ticketRepositoryMock);
        $this->ticketService = new TicketService($this->ticketManager);
    }

    public function test_get_ticket_statistics_returns_correct_counts()
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $weekStart = $now->copy()->startOfWeek();
        $monthStart = $now->copy()->startOfMonth();

        // Create tickets for today
        Ticket::factory()->count(2)->create(['created_at' => $today]);

        // Create tickets for this week (but not today)
        Ticket::factory()->count(3)->create(['created_at' => $weekStart->copy()->addDay()]);

        // Create tickets for this month (but not this week)
        Ticket::factory()->count(4)->create(['created_at' => $monthStart->copy()->addWeek()]);

        // Create tickets from previous month (should not be counted)
        Ticket::factory()->count(1)->create(['created_at' => $monthStart->copy()->subMonth()]);

        $statistics = $this->ticketService->getTicketStatistics();

        // Should have 2 tickets created today
        $this->assertEquals(2, $statistics['day']);

        // Should have 5 tickets created this week (2 today + 3 other days this week)
        $this->assertEquals(5, $statistics['week']);

        // Should have 9 tickets created this month (2 today + 3 this week + 4 other days this month)
        $this->assertEquals(9, $statistics['month']);
    }

    public function test_get_ticket_statistics_handles_empty_data()
    {
        $statistics = $this->ticketService->getTicketStatistics();

        $this->assertEquals(0, $statistics['day']);
        $this->assertEquals(0, $statistics['week']);
        $this->assertEquals(0, $statistics['month']);
    }
}
