<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\UpdateQarorRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class UpdateQarorRequestTest extends TestCase
{
    /** @test */
    public function it_allows_nullable_pdf(): void
    {
        $request = new UpdateQarorRequest();

        $validator = Validator::make([], $request->rules());

        $this->assertFalse($validator->fails());
    }

    /** @test */
    public function it_validates_pdf_mime_type(): void
    {
        $request = new UpdateQarorRequest();

        $rules = $request->rules();

        $this->assertStringContainsString('mimes:pdf', $rules['pdf']);
    }

    /** @test */
    public function it_has_custom_error_messages(): void
    {
        $request = new UpdateQarorRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('pdf.mimes', $messages);
        $this->assertArrayHasKey('pdf.max', $messages);
    }
}
