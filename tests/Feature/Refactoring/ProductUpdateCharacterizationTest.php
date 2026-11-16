<?php

namespace Tests\Feature\Refactoring;

use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

// RF-001: Satukan alur update produk; kontrak gambar, stok, relasi, dan audit tetap sama.
class ProductUpdateCharacterizationTest extends TestCase
{
    use RefreshDatabase;

    public static function updateCases(): array
    {
        return ['normal tanpa gambar' => [false, false], 'normal dengan gambar' => [true, false],
            'komposit tanpa gambar' => [false, true], 'komposit dengan gambar' => [true, true]];
    }

    #[DataProvider('updateCases')]
    public function test_update_preserves_stock_and_synchronizes_the_correct_relation(bool $withImage, bool $composite): void
    {
        Storage::fake('local');
        $this->seed([PermissionSeeder::class, RoleSeeder::class, UserSeeder::class]);
        $admin = User::where('email', 'arya@gmail.com')->firstOrFail();
        $admin->markEmailAsVerified();
        $this->actingAs($admin);
        $category = Category::create(['name' => 'RF test', 'image' => 'test.png', 'description' => 'Test']);
        $attributes = ['image' => 'old.png', 'barcode' => 'RF-TARGET', 'sku' => 'RF-TARGET', 'title' => 'Before',
            'category_id' => $category->id, 'buy_price' => 7000, 'sell_price' => 10000, 'stock' => 50, 'tax_rate' => 0];
        $product = Product::create($attributes);
        $component = Product::create([...$attributes, 'barcode' => 'RF-COMPONENT', 'sku' => 'RF-COMPONENT']);
        $warehouse = Warehouse::create(['code' => 'PUSAT', 'name' => 'Pusat', 'type' => 'main', 'is_active' => true, 'sort_order' => 0]);
        $product->warehouses()->attach($warehouse->id, ['stock' => 50]);
        $unit = Unit::firstOrCreate(['code' => 'PCS'], ['name' => 'Pieces', 'symbol' => 'pcs']);
        Storage::disk('local')->put('public/products/old.png', 'old image');
        $payload = ['barcode' => $product->barcode, 'sku' => $product->sku, 'title' => 'After',
            'description' => 'Updated', 'category_id' => $category->id, 'buy_price' => 7000, 'sell_price' => 12000,
            'stock' => 999, 'min_stock' => 6, 'max_stock' => 90, 'tax_type' => 'exclusive', 'tax_rate' => 0,
            'is_composite' => $composite,
            ...($composite ? ['components' => [['component_product_id' => $component->id, 'qty' => 2]]]
                : ['units' => [['unit_id' => $unit->id, 'is_base' => true, 'conversion_factor' => 1, 'sell_price' => 12000]]])];
        if ($withImage) {
            $payload['image'] = UploadedFile::fake()->image('replacement.png');
        }

        $this->put(route('products.update', $product), $payload)->assertSessionHasNoErrors()->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertSame('After', $product->title);
        $this->assertEquals(12000, $product->sell_price);
        $this->assertSame(50, $product->stock);
        $this->assertDatabaseHas('product_warehouse', ['product_id' => $product->id, 'warehouse_id' => $warehouse->id, 'stock' => 50]);
        $this->assertEquals(6, $product->min_stock);
        $this->assertEquals(90, $product->max_stock);
        if ($composite) {
            $this->assertDatabaseHas('composite_product_items', ['composite_product_id' => $product->id, 'component_product_id' => $component->id, 'qty' => 2]);
        } else {
            $this->assertEquals(12000, $product->units()->firstOrFail()->pivot->sell_price);
        }
        $expectedImage = $withImage ? $payload['image']->hashName() : 'old.png';
        $this->assertSame($expectedImage, $product->getRawOriginal('image'));
        Storage::disk('local')->assertExists('public/products/'.$expectedImage);
        if ($withImage) {
            Storage::disk('local')->assertMissing('public/products/old.png');
        }
        $audit = AuditLog::where('auditable_id', $product->id)->where('event', 'product.updated')->sole();
        $this->assertSame('Before', $audit->before['title']);
        $this->assertSame('After', $audit->after['title']);
        $this->assertSame(1, AuditLog::where('auditable_id', $product->id)->where('event', 'product.price_updated')->count());
    }
}
