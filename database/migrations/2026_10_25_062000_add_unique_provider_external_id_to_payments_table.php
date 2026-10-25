
<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // El indice ya se crea en create_payments_table.
    }

    public function down(): void
    {
        // No eliminar el indice de la migracion original.
    }
};
