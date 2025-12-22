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

        foreach ($customers as $customer) {
            $ticketsForCustomer = max(1, intval(self::TOTAL_TARGET_TICKETS / count($customers)));

            if ($customer->is($customers->last())) {
                $ticketsNeeded = self::TOTAL_TARGET_TICKETS - $ticketCount;
                $ticketsForCustomer = max($ticketsForCustomer, $ticketsNeeded);
            }

            $tickets = Ticket::factory($ticketsForCustomer)->create([
                'customer_id' => $customer->id,
            ]);

            // Add random files to each ticket (0 to 5 files)
            foreach ($tickets as $ticket) {
                // Add files to first TICKETS_WITH_FILES_LIMIT tickets
                if ($ticketCount < min(self::TICKETS_WITH_FILES_LIMIT, self::TOTAL_TARGET_TICKETS)) {
                    $this->addRandomFilesToTicket($ticket);
                }
                $ticketCount++;
            }
        }

        if ($ticketCount < self::TOTAL_TARGET_TICKETS) {
            $remainingTickets = self::TOTAL_TARGET_TICKETS - $ticketCount;
            for ($i = 0; $i < $remainingTickets; $i++) {
                $ticket = Ticket::factory()->create([
                    'customer_id' => $customers->random()->id,
                ]);

                if ($ticketCount < min(self::TICKETS_WITH_FILES_LIMIT + 5, self::TOTAL_TARGET_TICKETS)) {
                    $this->addRandomFilesToTicket($ticket);
                }
                $ticketCount++;
            }
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
