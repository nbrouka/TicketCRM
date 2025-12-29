<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Database\Seeder;

class CustomerTicketSeeder extends Seeder
{
    private const CUSTOMER_COUNT = 10;

    private const TOTAL_TARGET_TICKETS = 60;

    private const TICKETS_WITH_FILES_LIMIT = 20;

    private const MAX_FILES_PER_TICKET = 5;

    private const FILE_EXTENSIONS = ['pdf', 'doc', 'docx', 'txt', 'jpg', 'png', 'zip'];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = Customer::factory(self::CUSTOMER_COUNT)->create();

        $ticketCount = 0;
        $totalTickets = self::TOTAL_TARGET_TICKETS;

        // Define date ranges for diverse statistics
        $today = now()->startOfDay();
        $oneWeekAgo = now()->subWeek()->startOfDay();
        $oneMonthAgo = now()->subMonth()->startOfDay();

        // Calculate distribution: 20% today, 30% this week, 30% this month, 20% older
        $todayTickets = intval($totalTickets * 0.2);
        $weekTickets = intval($totalTickets * 0.3);
        $monthTickets = intval($totalTickets * 0.3);
        $olderTickets = $totalTickets - $todayTickets - $weekTickets - $monthTickets;

        $ticketsCreated = 0;

        // Create tickets for today (last 20%)
        for ($i = 0; $i < $todayTickets; $i++) {
            $this->createTicketWithDate($customers, $ticketsCreated, $totalTickets);
            $ticketsCreated++;
        }

        // Create tickets for this week (previous 30%)
        for ($i = 0; $i < $weekTickets; $i++) {
            $randomDate = $today->copy()->subDays(rand(1, 6)); // Not including today
            $this->createTicketWithDate($customers, $ticketsCreated, $totalTickets, $randomDate);
            $ticketsCreated++;
        }

        // Create tickets for this month (previous 30%)
        for ($i = 0; $i < $monthTickets; $i++) {
            $randomDate = $oneWeekAgo->copy()->subDays(rand(1, 21)); // Not including this week
            $this->createTicketWithDate($customers, $ticketsCreated, $totalTickets, $randomDate);
            $ticketsCreated++;
        }

        // Create tickets from older periods (remaining 20%)
        for ($i = 0; $i < $olderTickets; $i++) {
            $randomDate = $oneMonthAgo->copy()->subDays(rand(1, 30)); // Older than one month
            $this->createTicketWithDate($customers, $ticketsCreated, $totalTickets, $randomDate);
            $ticketsCreated++;
        }
    }

    /**
     * Create a ticket with a specific created_at date
     */
    private function createTicketWithDate($customers, int $ticketCount, int $totalTickets, $date = null): void
    {
        if ($date === null) {
            $date = now(); // Today
        }

        $ticket = Ticket::factory()->create([
            'customer_id' => $customers->random()->id,
            'created_at' => $date,
            'updated_at' => $date,
        ]);

        // Add random files to each ticket (0 to 5 files)
        if ($ticketCount < min(self::TICKETS_WITH_FILES_LIMIT, $totalTickets)) {
            $this->addRandomFilesToTicket($ticket);
        }
    }

    /**
     * Add random files to a ticket
     */
    private function addRandomFilesToTicket(Ticket $ticket): void
    {
        $fileCount = rand(0, self::MAX_FILES_PER_TICKET);
        for ($i = 0; $i < $fileCount; $i++) {
            $extension = self::FILE_EXTENSIONS[array_rand(self::FILE_EXTENSIONS)];
            $fileName = 'ticket_'.$ticket->id.'_file_'.($i + 1).'.'.$extension;
            $tempFile = tempnam(sys_get_temp_dir(), 'seed_file_');
            // Rename to include proper extension
            $tempFileWithExt = $tempFile.'.'.$extension;
            rename($tempFile, $tempFileWithExt);
            file_put_contents($tempFileWithExt, 'Test file content for ticket '.$ticket->id.' file '.($i + 1));
            $ticket->addMedia($tempFileWithExt)
                ->usingName($fileName)
                ->usingFileName($fileName)
                ->preservingOriginal()
                ->toMediaCollection('files');

            if (file_exists($tempFileWithExt)) {
                unlink($tempFileWithExt);
            }
        }
    }
}
