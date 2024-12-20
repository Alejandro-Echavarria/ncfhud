<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices_606', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('rnc', 11)->nullable();
            $table->text('business_name')->nullable();
            $table->string('ncf', 19)->nullable();
            $table->string('proof_date', 8)->nullable();
            $table->string('payment_date', 8)->nullable();
            $this->addNullableInteger($table, 'amount');
            $this->addNullableInteger($table, 'itbis');
            $this->addNullableInteger($table, 'withheld_itbis');

            $table->timestamps();
        });
    }

    /**
     * Add an unsigned nullable integer column to the table
     */
    private function addNullableInteger(Blueprint $table, string $columnName): void
    {
        $table->decimal($columnName, 15, 2)->default(0);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices_606');
    }
};
