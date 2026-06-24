<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Power;
use App\Models\Stock;
use Illuminate\Support\Facades\DB;

class SalesTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_requires_auth()
    {
        $resp = $this->get('/sales');
        $resp->assertRedirect('/login');
    }

    public function test_store_creates_sale_and_payment()
    {
        $user = User::factory()->create();
        $customer = Customer::create(['name'=>'Test Cust']);
        $product = Product::create(['name'=>'P', 'sku'=>'P1']);
    $power = Power::create(['sph'=>'0.00','cyl'=>null,'category'=>'general']);
        Stock::create(['product_id'=>$product->id,'power_id'=>$power->id,'quantity'=>10]);

        $payload = [
            'customer_id' => $customer->id,
            'invoice_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'power_id' => $power->id, 'quantity' => 2, 'unit_price' => 100]
            ],
            'paid' => 50,
            'payment_mode' => 'cash'
        ];

        $resp = $this->actingAs($user)->post('/sales', $payload);
        // ensure no validation errors
        $resp->assertSessionDoesntHaveErrors();
        if ($errors = session('errors')) {
            fwrite(STDERR, (string) $errors);
        }
        $this->assertDatabaseHas('sales', ['customer_id'=>$customer->id,'paid'=>50]);
        $this->assertDatabaseHas('payments', ['amount'=>50]);
    }
}
