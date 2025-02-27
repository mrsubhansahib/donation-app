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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link to users
            $table->string('stripe_subscription_id')->unique(); // Stripe subscription ID
            $table->string('stripe_price_id'); // The Stripe price ID for this subscription
            $table->string('price');
            $table->string('currency');
            $table->string('type');
            $table->string('status')->default('active'); // Active, canceled, trialing
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->timestamp('canceled_at')->nullable();
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
        Schema::dropIfExists('subscriptions');
    }
};
