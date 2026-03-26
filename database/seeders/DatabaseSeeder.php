<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Runs database/thingy-seed.sql as-is.
     * The SQL reads current_setting('app.schema', true) and defaults to 'thingy'.
     */
    public function run(): void
    {
        // Get schema name from environment (e.g., .env or .env.testing)
        $schema = env('DB_SCHEMA');

        // If DB_SCHEMA is set, expose it to the SQL script
        if ($schema !== null) {
            DB::statement("SELECT set_config('app.schema', ?, false)", [$schema]);
        }

        // Run database.sql to create tables
        $dbPath = base_path('database/database.sql');
        $dbSql = file_get_contents($dbPath);
        DB::unprepared($dbSql);

        // Run populate.sql to insert data
        $popPath = base_path('database/populate.sql');
        $popSql = file_get_contents($popPath);
        DB::unprepared($popSql);

        // Show a message in the Artisan console
        $this->command?->info('Database seeded with FreshTech data using schema: ' . ($schema ?? 'thingy (default)'));
    }
}
