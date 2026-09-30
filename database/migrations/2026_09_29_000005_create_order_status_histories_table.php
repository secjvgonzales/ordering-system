<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'status', 'created_at'], 'order_status_history_unique');
        });

        DB::table('orders')
            ->orderBy('id')
            ->get(['id', 'status', 'created_at', 'updated_at'])
            ->each(function ($order) {
                DB::table('order_status_histories')->insertOrIgnore([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'changed_by' => null,
                    'note' => 'Initial history added for an existing order.',
                    'created_at' => $order->created_at ?? now(),
                    'updated_at' => $order->updated_at ?? $order->created_at ?? now(),
                ]);
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
