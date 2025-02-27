<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users
            $table->string('stripe_invoice_id')->unique(); // Stripe invoice ID
            $table->string('stripe_subscription_id'); // Stripe invoice ID
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('status'); // Paid, Open, Draft
            $table->timestamp('invoice_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
