<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number')->unique();
            $table->enum('status', ['paid', 'unpaid', 'pending'])->default('unpaid');
            $table->timestamp('invoice_date')->nullable();
            // Invoiced To
            $table->string('client_company')->nullable();
            $table->string('client_name');
            $table->text('client_address');
            $table->string('client_city')->nullable();
            $table->string('client_province')->nullable();
            $table->string('client_postal_code')->nullable();
            $table->string('client_country')->default('Indonesia');
            // Pay To
            $table->string('company_name');
            $table->text('company_address');
            $table->string('company_npwp')->nullable();
            // Totals
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('taxable_base', 15, 2)->default(0);
            $table->decimal('vat', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('invoices'); }
};
