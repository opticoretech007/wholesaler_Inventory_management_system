<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetSalesPurchasesData extends Command
{
    protected $signature = 'app:reset-sales-purchases {--force : Skip confirmation prompt}';
    protected $description = 'Truncate only Sales & Purchases related tables (stock, payments, returns included). Master data like Products, Categories, Suppliers, Customers, Powers is left untouched.';

    /**
     * List every table you want wiped, in any order — FK checks are
     * disabled during the operation so order doesn't matter.
     */
    protected array $tables = [
        // Purchase-related
        'purchase_return_items',
        'purchase_returns',
        'purchase_items',
        'purchases',

        // Sales-related
        'sale_items',
        'sales',

        // Stock-related
        'stock_transactions',
        'stocks',

        // Payments
        'payments',

        // Master/reference data used heavily in testing
        // (left out by default — uncomment if you also want these wiped)
        // 'products',
        // 'categories',
        // 'classes',
        // 'subclasses',
        // 'powers',
        // 'suppliers',
        // 'customers',
    ];

    public function handle()
    {
        if (!$this->option('force')) {
            $this->warn('This will permanently delete ALL rows from the following tables:');
            foreach ($this->tables as $table) {
                $this->line(" - {$table}");
            }
            if (!$this->confirm('Are you sure you want to continue?')) {
                $this->info('Cancelled. No data was touched.');
                return;
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($this->tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
                $this->info("✔ Truncated: {$table}");
            } else {
                $this->warn("⚠ Skipped (table not found): {$table}");
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Done. All listed tables are now empty and auto-increment IDs are reset to 1.');
    }
}
