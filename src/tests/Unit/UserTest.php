<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_attributes()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
    }

    public function test_user_factory_creates_valid_user()
    {
        $user = User::factory()->create();

        $this->assertNotEmpty($user->name);
        $this->assertNotEmpty($user->email);
        $this->assertNotNull($user->password);
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_user_email_is_unique()
    {
        $user1 = User::factory()->create([
            'email' => 'unique@example.com',
        ]);

        $this->expectException(QueryException::class);

        User::factory()->create([
            'email' => 'unique@example.com',
        ]);
    }

    public function test_user_model_has_correct_fillable_attributes()
    {
        $user = new User;
        $fillable = $user->getFillable();

        $expectedAttributes = ['name', 'email', 'password'];
        foreach ($expectedAttributes as $attribute) {
            $this->assertContains($attribute, $fillable);
        }
    }

    public function test_user_password_is_hashed_when_set()
    {
        $user = new User;
        $user->password = 'plain_text_password';

        $this->assertNotEquals('plain_text_password', $user->password);
        $this->assertTrue(password_verify('plain_text_password', $user->password));
    }
}
