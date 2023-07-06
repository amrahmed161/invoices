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
            $table->bigIncrements('id');
            $table->string('invoice_number');
            $table->date('invoice_Date');
            $table->date('Due_date');
            $table->string('product');
            $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->decimal('Amount_collection',8,2)->nullable();
            $table->decimal('Amount_commission',8,2);
            $table->decimal('Discount',8,2);
            $table->string('Rate_vat',999);
            $table->decimal('Value_vat',8,2);
            $table->decimal('Total',8,2);
            $table->string('Status',50);
            $table->integer('value_status');
            $table->text('note')->nullable();
            $table->date('Payment_Date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
