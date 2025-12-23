<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\TicketStatus;
use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_has_correct_attributes()
    {
        $customer = Customer::factory()->create();

        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Test Ticket',
            'text' => 'This is a test ticket',
            'status' => TicketStatus::NEW,
        ]);

        $this->assertEquals($customer->id, $ticket->customer_id);
        $this->assertEquals('Test Ticket', $ticket->theme);
        $this->assertEquals('This is a test ticket', $ticket->text);
        $this->assertEquals(TicketStatus::NEW, $ticket->status);
        $this->assertNotNull($ticket->created_at);
        $this->assertNotNull($ticket->updated_at);
    }

    public function test_ticket_factory_creates_valid_ticket()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        $this->assertNotEmpty($ticket->theme);
        $this->assertNotEmpty($ticket->text);
        $this->assertNotNull($ticket->status);
        $this->assertDatabaseHas('tickets', ['id' => $ticket->id]);
    }

    public function test_ticket_belongs_to_customer()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(Customer::class, $ticket->customer);
        $this->assertEquals($customer->id, $ticket->customer->id);
    }

    public function test_ticket_model_has_correct_fillable_attributes()
    {
        $ticket = new Ticket;
        $fillable = $ticket->getFillable();

        $expectedAttributes = ['customer_id', 'theme', 'text', 'status', 'date_answer'];
        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_ticket_status_is_casted_to_enum()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $this->assertInstanceOf(TicketStatus::class, $ticket->status);
        $this->assertEquals(TicketStatus::IN_PROGRESS, $ticket->status);
    }

    public function test_date_answer_is_casted_to_datetime()
    {
        $customer = Customer::factory()->create();
        $date = Carbon::now();

        $ticket = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'date_answer' => $date,
        ]);

        $this->assertInstanceOf(Carbon::class, $ticket->date_answer);
        $this->assertEquals($date->format('Y-m-d H:i:s'), $ticket->date_answer->format('Y-m-d H:i:s'));
    }

    public function test_attach_files_method_attaches_files_to_ticket()
    {
        $customer = Customer::factory()->create();
        $ticket = Ticket::factory()->create(['customer_id' => $customer->id]);

        // Create temporary files for testing
        $file1 = tmpfile();
        $file2 = tmpfile();

        $fileName1 = stream_get_meta_data($file1)['uri'];
        $fileName2 = stream_get_meta_data($file2)['uri'];

        $ticket->attachFiles([$fileName1, $fileName2]);

        // Check that files were attached to the 'files' collection
        $this->assertEquals(2, $ticket->getMedia('files')->count());

        fclose($file1);
        fclose($file2);
    }

    public function test_ticket_filter_scope_applies_filters()
    {
        $customer = Customer::factory()->create();

        $ticket1 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Urgent ticket',
            'status' => TicketStatus::NEW,
        ]);

        $ticket2 = Ticket::factory()->create([
            'customer_id' => $customer->id,
            'theme' => 'Normal ticket',
            'status' => TicketStatus::IN_PROGRESS,
        ]);

        $filter = new \App\Filters\TicketFilter((object) ['status' => TicketStatus::NEW->value]);
        $filteredTickets = Ticket::filter($filter)->get();

        $this->assertCount(1, $filteredTickets);
        $this->assertEquals($ticket1->id, $filteredTickets->first()->id);
    }

    public function test_ticket_can_be_created_with_all_statuses()
    {
        $customer = Customer::factory()->create();

        foreach (TicketStatus::cases() as $status) {
            $ticket = Ticket::factory()->create([
                'customer_id' => $customer->id,
                'status' => $status,
            ]);

            $this->assertEquals($status, $ticket->status);
        }
    }
}
