
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'suppliers',
            'ingredients',
            'purchases',
            'expenses',
            'notes',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->foreignId('umkm_id')
                    ->constrained('umkms')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'notes',
            'expenses',
            'purchases',
            'ingredients',
            'suppliers',
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropConstrainedForeignId('umkm_id');
            });
        }
    }
};
