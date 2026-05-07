<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE users MODIFY avatar VARCHAR(2048) NULL');
        DB::statement('ALTER TABLE books MODIFY image VARCHAR(2048) NULL');
        DB::statement('ALTER TABLE posts MODIFY image VARCHAR(2048) NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE users MODIFY avatar LONGTEXT NULL');
        DB::statement('ALTER TABLE books MODIFY image LONGTEXT NULL');
        DB::statement('ALTER TABLE posts MODIFY image LONGTEXT NULL');
    }
};
