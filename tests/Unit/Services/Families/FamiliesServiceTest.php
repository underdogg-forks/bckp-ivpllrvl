<?php

namespace Tests\Unit\Services\Families;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Families\Models\Family;
use Modules\Families\Services\FamiliesService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FamiliesServiceTest extends TestCase
{
    use RefreshDatabase;

    private FamiliesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(FamiliesService::class);
    }

    #[Test]
    public function it_retrieves_all_families(): void
    {
        // Arrange
        Family::create(['family_name' => 'Family 1']);
        Family::create(['family_name' => 'Family 2']);
        Family::create(['family_name' => 'Family 3']);

        // Act
        $result = $this->service->defaultSelect()->get();

        // Assert
        $this->assertCount(3, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
    }
}