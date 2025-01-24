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
            $table->foreignId('client_id')->constrained();
            $table->text('rnc')->nullable();
            $table->text('business_name')->nullable();
            $table->text('ncf')->nullable();
            $table->string('proof_date', 8)->nullable();
            $this->addNullableInteger($table, 'amount');
            $this->addNullableInteger($table, 'itbis');
            $this->addNullableInteger($table, 'withheld_itbis');

            $table->string('month_period', 2);
            $table->string('year_period', 4);

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
