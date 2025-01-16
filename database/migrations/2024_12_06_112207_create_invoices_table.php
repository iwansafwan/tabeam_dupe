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

            //donator ID (FK)
            $table->unsignedBigInteger('donator_id');
            $table->foreign('donator_id')->references('id')->on('users');

            //general_fund_id (FK)
            $table->unsignedBigInteger('general_fund_id')->nullable();
            $table->foreign('general_fund_id')->references('id')->on('general_funds');

            //fund ID (FK)
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->foreign('fund_id')->references('id')->on('funds');

            // ratio/section ID (FK)
            $table->unsignedBigInteger('ratio_id')->nullable();
            $table->foreign('ratio_id')->references('id')->on('ratios');
            
            //Donation type where has 3 only option
            $table->enum('donation_type',['general','main','section'])->default('general');

            $table->text('notes')->nullable();
            $table->decimal('amount',10,2);
            $table->timestamps();
            $table->softDeletes();

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
