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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->text('rnc')->nullable();

            $table->integer('identification_type')->unsigned()->default(0);

            $table->text('ncf')->nullable();
            $table->text('ncf_modified')->nullable();

            $table->string('income_type', 255)->nullable();

            $table->string('proof_date', 8)->nullable();
            $table->string('withholding_date', 8)->nullable();

            $this->addUnsignedNullableInteger($table, 'amount');
            $this->addUnsignedNullableInteger($table, 'itbis');
            $this->addUnsignedNullableInteger($table, 'third_party_itbis_withheld');
            $this->addUnsignedNullableInteger($table, 'received_itbis');
            $this->addUnsignedNullableInteger($table, 'third_party_income_retention');
            $this->addUnsignedNullableInteger($table, 'isr');
            $this->addUnsignedNullableInteger($table, 'selective_tax');
            $this->addUnsignedNullableInteger($table, 'other_taxes_fees');
            $this->addUnsignedNullableInteger($table, 'legal_tip_amount');
            $this->addUnsignedNullableInteger($table, 'cash');
            $this->addUnsignedNullableInteger($table, 'check_transfer_deposit');
            $this->addUnsignedNullableInteger($table, 'debit_credit_card');
            $this->addUnsignedNullableInteger($table, 'credit_sale');
            $this->addUnsignedNullableInteger($table, 'bonds_certificates');
            $this->addUnsignedNullableInteger($table, 'barter');
            $this->addUnsignedNullableInteger($table, 'other_sales_forms');

            $table->string('month_period', 2);
            $table->string('year_period', 4);

            $table->timestamps();
        });
    }

    /**
     * Add an unsigned nullable integer column to the table
     */
    private function addUnsignedNullableInteger(Blueprint $table, string $columnName): void
    {
        $table->decimal($columnName, 15, 2)->unsigned()->default(0);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
