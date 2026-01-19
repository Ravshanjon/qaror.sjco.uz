<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\ImportQarorRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ImportQarorRequestTest extends TestCase
{
    /** @test */
    public function it_requires_file_field(): void
    {
        $request = new ImportQarorRequest();

        $validator = Validator::make([], $request->rules());

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('file', $validator->errors()->toArray());
    }

    /** @test */
    public function it_validates_file_mime_types(): void
    {
        $request = new ImportQarorRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('mimes:xlsx,csv', $rules['file']);
    }

    /** @test */
    public function it_validates_max_file_size(): void
    {
        config(['qaror.max_excel_size' => 10240]);

        $request = new ImportQarorRequest();
        $rules = $request->rules();

        $this->assertStringContainsString('max:10240', $rules['file']);
    }
}
