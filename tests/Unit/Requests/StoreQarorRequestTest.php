<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\StoreQarorRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StoreQarorRequestTest extends TestCase
{
    /** @test */
    public function it_validates_required_fields(): void
    {
        $request = new StoreQarorRequest();

        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('published_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
        $this->assertArrayHasKey('pdf', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_published_id_uniqueness(): void
    {
        \App\Models\Qaror::factory()->create(['published_id' => 12345]);

        $request = new StoreQarorRequest();

        $validator = Validator::make([
            'published_id' => 12345,
            'title' => 'Test',
            'pdf' => 'fake.pdf',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('published_id', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_title_max_length(): void
    {
        config(['qaror.max_title_length' => 255]);

        $request = new StoreQarorRequest();

        $validator = Validator::make([
            'published_id' => 999,
            'title' => str_repeat('a', 300),
            'pdf' => 'fake.pdf',
        ], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('title', $validator->errors()->toArray());
    }

    /** @test */
    public function it_has_custom_error_messages(): void
    {
        $request = new StoreQarorRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('published_id.required', $messages);
        $this->assertArrayHasKey('published_id.unique', $messages);
        $this->assertArrayHasKey('title.required', $messages);
        $this->assertArrayHasKey('pdf.required', $messages);
        $this->assertArrayHasKey('pdf.mimes', $messages);
        $this->assertArrayHasKey('pdf.max', $messages);
    }
}
