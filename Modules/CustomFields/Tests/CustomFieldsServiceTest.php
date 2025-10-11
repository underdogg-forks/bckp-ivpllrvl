<?php

namespace Tests\Unit\Modules\CustomFields\Services;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\CustomFields\Services\CustomFieldsService;
use Tests\TestCase;

class CustomFieldsServiceTest extends TestCase
{
    use RefreshDatabase;

    private CustomFieldsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CustomFieldsService();
    }

    public function test_service_has_correct_table(): void
    {
        $this->assertEquals('ip_custom_fields', $this->service->table);
    }

    public function test_service_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('custom_field_id', $this->service->primary_key);
    }
}
