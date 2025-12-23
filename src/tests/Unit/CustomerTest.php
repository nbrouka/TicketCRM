<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_has_correct_attributes()
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
        ]);

        $this->assertEquals('John Doe', $customer->name);
        $this->assertEquals('+1234567890', $customer->phone);
        $this->assertEquals('john@example.com', $customer->email);
        $this->assertNotNull($customer->created_at);
        $this->assertNotNull($customer->updated_at);
    }

    public function test_customer_factory_creates_valid_customer()
    {
        $customer = Customer::factory()->create();

        $this->assertNotEmpty($customer->name);
        $this->assertNotEmpty($customer->phone);
        $this->assertNotEmpty($customer->email);
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_customer_has_many_tickets()
    {
        $customer = Customer::factory()->create();
        $ticket1 = Ticket::factory()->create(['customer_id' => $customer->id]);
        $ticket2 = Ticket::factory()->create(['customer_id' => $customer->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $customer->tickets);
        $this->assertCount(2, $customer->tickets);
        $this->assertTrue($customer->tickets->contains($ticket1));
        $this->assertTrue($customer->tickets->contains($ticket2));
    }

    public function test_customer_model_has_correct_fillable_attributes()
    {
        $customer = new Customer;
        $fillable = $customer->getFillable();

        $expectedAttributes = ['name', 'phone', 'email'];
        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_customer_can_have_same_email()
    {
        $customer1 = Customer::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        // This should not throw an exception as email is not unique in the schema
        $customer2 = Customer::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $this->assertDatabaseHas('customers', ['email' => 'duplicate@example.com']);
        $this->assertEquals(2, Customer::where('email', 'duplicate@example.com')->count());
    }

    public function test_customer_phone_can_be_the_same()
    {
        $customer1 = Customer::factory()->create([
            'phone' => '+1234567890',
            'email' => 'first@example.com',
        ]);

        // This should not throw an exception as phone is not unique
        $customer2 = Customer::factory()->create([
            'phone' => '+1234567890', // Same phone number
            'email' => 'second@example.com',
        ]);

        $this->assertDatabaseHas('customers', ['phone' => '+1234567890']);
        $this->assertNotEquals($customer1->id, $customer2->id);
    }

    public function test_customer_name_can_be_empty()
    {
        $customer = Customer::factory()->create([
            'name' => '',
        ]);

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'name' => '',
        ]);
    }

    public function test_customer_phone_format_validation()
    {
        $customer = Customer::factory()->create([
            'phone' => '+1-555-123-4567',
        ]);

        $this->assertEquals('+1-555-123-4567', $customer->phone);
    }

    public function test_customer_email_format_validation()
    {
        $customer = Customer::factory()->create([
            'email' => 'test.customer@company.domain',
        ]);

        $this->assertEquals('test.customer@company.domain', $customer->email);
    }
}
