<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10);          // income | expense
            $table->decimal('amount', 15, 2);
            $table->string('category', 50);
            $table->date('date');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
