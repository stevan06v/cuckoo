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
        Schema::create('contract_classifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_id');
            $table->unsignedBigInteger('user_id');
            $table->double('hourly_rate')
                ->default(0);
            $table->foreign('contract_id')
                ->references('id')
                ->on('contracts')
                ->onDelete('cascade');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_classifications');
    }
};
