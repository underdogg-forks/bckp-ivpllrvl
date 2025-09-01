<?php

namespace Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Products\Controllers\ProductsController;
use Modules\Products\Models\Product;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ProductsController::class)]
class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_products_list()
    {
        // Arrange: create some products using factory
        $products = Product::factory()->count(3)->create();

        // Act: call the index route
        $response = $this->get(route('products.index'));

        // Assert: check that products are visible in the response
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
        $response->assertStatus(200);
    }

    #[Test]
    public function it_handles_product_creation_and_editing()
    {
        // Arrange: create product data
        $data = [
            'name'  => 'Test Product',
            'price' => 99.99,
            // add other required fields
        ];

        // Act: submit the form to create a product
        $response = $this->post(route('products.form'), $data);

        // Assert: product is created in the database
        $this->assertDatabaseHas('products', ['name' => 'Test Product', 'price' => 99.99]);
        $response->assertRedirect(route('products.index'));

        // Act: edit the product
        $product  = Product::query()->first();
        $editData = [
            'name'  => 'Updated Product',
            'price' => 149.99,
            // add other required fields
        ];
        $response = $this->post(route('products.form', $product->id), $editData);

        // Assert: product is updated
        $this->assertDatabaseHas('products', ['name' => 'Updated Product', 'price' => 149.99]);
        $response->assertRedirect(route('products.index'));
    }
}
