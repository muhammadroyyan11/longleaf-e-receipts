<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->date('transaction_date');
            $table->string('gateway');
            $table->string('transaction_id');
            $table->decimal('amount', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void { Schema::dropIfExists('transactions'); }
};
