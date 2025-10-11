<?php

namespace Modules\Products\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Modules\Families\Models\Family;
use Modules\Products\Controllers\ProductsController;
use Modules\Products\Models\Product;
use Modules\TaxRates\Models\TaxRate;
use Modules\Units\Models\Unit;
use PHPUnit\Framework\Attributes\CoversClass;

use function Tests\Feature\Products\route;

use Tests\Feature\Products\Test;
use Tests\TestCase;

#[CoversClass(ProductsController::class)]
class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected Family $family;

    protected Unit $unit;

    protected TaxRate $taxRate;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user    = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->family  = Family::factory()->create();
        $this->unit    = Unit::factory()->create();
        $this->taxRate = TaxRate::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_products_list()
    {
        // Arrange: create some products using factory
        $products = Product::factory()->count(3)->create();

        // Act: call the index route
        $response = $this->get(route('products.index'));

        // Assert: check that products are visible in the response
        $response->assertSuccessful();
        $response->assertViewHas('products');
        $response->assertSee('Products');
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

    #[Test]
    public function it_creates_new_product(): void
    {
        $productData = [
            'product_sku'         => '123-ABC',
            'product_name'        => 'Test Product',
            'product_description' => 'A test product.',
            'product_price'       => 100.00,
            'tax_rate_id'         => $this->taxRate->tax_rate_id,
            'family_id'           => $this->family->family_id,
            'unit_id'             => $this->unit->unit_id,
        ];

        $response = $this->post(route('products.form'), $productData);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('ip_products', [
            'product_sku'  => '123-ABC',
            'product_name' => 'Test Product',
        ]);
    }

    #[Test]
    public function it_updates_existing_product(): void
    {
        $product = Product::factory()->create([
            'product_name' => 'Original Product',
        ]);

        $updateData = [
            'product_sku'         => '123-DEF',
            'product_name'        => 'Edited Product',
            'product_description' => 'An edited test product.',
            'product_price'       => 120.00,
        ];

        $response = $this->post(route('products.form', ['id' => $product->product_id]), $updateData);

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseHas('ip_products', [
            'product_id'    => $product->product_id,
            'product_name'  => 'Edited Product',
            'product_price' => 120.00,
        ]);
    }

    #[Test]
    public function it_deletes_product(): void
    {
        $product = Product::factory()->create();

        $response = $this->delete(route('products.delete', ['id' => $product->product_id]));

        $response->assertRedirect(route('products.index'));
        $this->assertDatabaseMissing('ip_products', ['product_id' => $product->product_id]);
    }

    #[Test]
    public function it_cancels_product_form_and_redirects(): void
    {
        $response = $this->post(route('products.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('products.index'));
    }

    #[Test]
    public function it_loads_product_form_with_related_data(): void
    {
        Family::factory()->count(2)->create();
        Unit::factory()->count(3)->create();
        TaxRate::factory()->count(2)->create();

        $response = $this->get(route('products.form'));

        $response->assertSuccessful();
        $response->assertViewHas('families', function ($families) {
            return $families->count() >= 2;
        });
        $response->assertViewHas('units', function ($units) {
            return $units->count() >= 3;
        });
        $response->assertViewHas('tax_rates', function ($taxRates) {
            return $taxRates->count() >= 2;
        });
    }
}

class ProductsAjaxControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected Family $family;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user   = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->family = Family::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_product_lookups_modal(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->post(route('products.ajax.modalProductLookups'));

        $response->assertSuccessful();
        $response->assertViewHas('products');
        $response->assertViewHas('families');
        $response->assertViewHas('default_item_tax_rate');
    }

    #[Test]
    public function it_filters_products_by_name_in_modal(): void
    {
        Product::factory()->create(['product_name' => 'Widget Alpha']);
        Product::factory()->create(['product_name' => 'Widget Beta']);
        Product::factory()->create(['product_name' => 'Gadget Gamma']);

        $response = $this->post(route('products.ajax.modalProductLookups'), [
            'filter_product' => 'Widget',
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 2;
        });
    }

    #[Test]
    public function it_filters_products_by_family_in_modal(): void
    {
        $familyA = Family::factory()->create();
        $familyB = Family::factory()->create();

        Product::factory()->count(2)->create(['family_id' => $familyA->family_id]);
        Product::factory()->count(3)->create(['family_id' => $familyB->family_id]);

        $response = $this->post(route('products.ajax.modalProductLookups'), [
            'filter_family' => $familyA->family_id,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 2;
        });
    }

    #[Test]
    public function it_filters_products_by_both_name_and_family(): void
    {
        $familyA = Family::factory()->create();

        Product::factory()->create([
            'product_name' => 'Widget Alpha',
            'family_id'    => $familyA->family_id,
        ]);
        Product::factory()->create([
            'product_name' => 'Widget Beta',
            'family_id'    => $familyA->family_id,
        ]);
        Product::factory()->create([
            'product_name' => 'Gadget Alpha',
            'family_id'    => $familyA->family_id,
        ]);

        $response = $this->post(route('products.ajax.modalProductLookups'), [
            'filter_product' => 'Widget',
            'filter_family'  => $familyA->family_id,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('products', function ($products) {
            return $products->count() === 2;
        });
    }

    #[Test]
    public function it_resets_product_table_when_requested(): void
    {
        $response = $this->post(route('products.ajax.modalProductLookups'), [
            'reset_table' => true,
        ]);

        $response->assertSuccessful();
        $response->assertViewIs('products.partial_product_table_modal');
    }

    #[Test]
    public function it_processes_product_selections(): void
    {
        $products   = Product::factory()->count(3)->create(['product_price' => 100.00]);
        $productIds = $products->pluck('product_id')->toArray();

        $response = $this->post(route('products.ajax.processProductSelections'), [
            'product_ids' => $productIds,
        ]);

        $response->assertSuccessful();
        $data = $response->json();
        $this->assertCount(3, $data);
        $this->assertArrayHasKey('product_price', $data[0]);
    }

    #[Test]
    public function it_formats_product_prices_with_two_decimals(): void
    {
        $product = Product::factory()->create(['product_price' => 99.9]);

        $response = $this->post(route('products.ajax.processProductSelections'), [
            'product_ids' => [$product->product_id],
        ]);

        $data = $response->json();
        $this->assertEquals('99.90', $data[0]['product_price']);
    }

    #[Test]
    public function it_returns_empty_array_for_no_product_ids(): void
    {
        $response = $this->post(route('products.ajax.processProductSelections'), [
            'product_ids' => [],
        ]);

        $response->assertSuccessful();
        $this->assertEmpty($response->json());
    }
}
