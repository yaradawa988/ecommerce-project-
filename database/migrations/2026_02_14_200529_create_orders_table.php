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
        Schema::create('orders', function (Blueprint $table) {
           $table->id();

        // العميل
        $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

        // السعر والحسابات
        $table->decimal('subtotal', 10, 2)->default(0);
        $table->decimal('discount', 10, 2)->default(0);
        $table->decimal('shipping_cost', 10, 2)->default(0);
        $table->decimal('total', 10, 2)->default(0);

        // تخزين كود الخصم لو تم استخدامه
        $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();

        // حالة الطلب (حياة الطلب)
        $table->enum('status', [
            'pending',       // بانتظار الدفع أو المراجعة
            'confirmed',     // تم تأكيد الطلب
            'processing',    // يتم تجهيز الطلب
            'shipped',       // تم الشحن
            'delivered',     // وصل للعميل
            'cancelled',     // ملغي
            'returned'       // مرتجع
        ])->default('pending');

        // حالة الدفع
        $table->enum('payment_status', ['unpaid','paid','failed','refunded'])->default('unpaid');
        $table->string('payment_method')->nullable();

        // عناوين الشحن
        $table->string('customer_name');
        $table->string('customer_phone');
        $table->string('customer_email')->nullable();
        $table->string('shipping_address');
        $table->string('shipping_city');
        $table->string('shipping_state')->nullable();
        $table->string('shipping_zip')->nullable();
        $table->string('shipping_country');

        // معلومات الشحن (tracking)
        $table->string('tracking_number')->nullable();
        $table->string('shipping_provider')->nullable();

        // تواريخ مراحل الرحلة
        $table->timestamp('confirmed_at')->nullable();
        $table->timestamp('processed_at')->nullable();
        $table->timestamp('shipped_at')->nullable();
        $table->timestamp('delivered_at')->nullable();
        $table->timestamp('cancelled_at')->nullable();
        $table->timestamp('returned_at')->nullable();

        $table->timestamps();
        $table->index(['status', 'payment_status']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
