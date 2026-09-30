<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->string('payment_status')->default('unpaid')->after('payment_method');
            $table->string('maya_checkout_id')->nullable()->after('payment_status');
            $table->string('maya_payment_id')->nullable()->after('maya_checkout_id');
            $table->string('maya_reference')->nullable()->after('maya_payment_id');
            $table->timestamp('paid_at')->nullable()->after('maya_reference');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'payment_status',
                'maya_checkout_id',
                'maya_payment_id',
                'maya_reference',
                'paid_at',
            ]);
        });
    }
};
