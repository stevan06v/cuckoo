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
        Schema::create('times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contract_classification_id');
            $table->foreign('contract_classification_id')
                ->references('id')
                ->on('contract_classifications')
                ->onDelete('cascade');
            $table->mediumText('description')
                ->nullable();
            $table->timestamp('date')
                ->nullable();
            $table->double('total_hours_worked')
                ->nullable();
            $table->double('total_minutes_worked')
                ->nullable();
            $table->boolean('is_special')
                ->nullable();
            $table->boolean('billed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('times');
    }
};
