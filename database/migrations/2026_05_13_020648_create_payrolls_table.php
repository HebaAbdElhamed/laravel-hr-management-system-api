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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
        // ربط الموظف
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // الفترة المالية
        $table->integer('month'); // من 1 لـ 12
        $table->integer('year');  // مثلاً 2024

        // المبالغ المالية
        $table->decimal('basic_salary', 10, 2);   // المرتب الأساسي من غير خصم
        $table->decimal('deductions', 10, 2)->default(0); // إجمالي الخصومات
        $table->decimal('net_salary', 10, 2);    // الصافي اللي هيقبضه

        // تفاصيل الخصومات (عشان الموظف يعرف اتخصمله ليه)
        // بنخزنها JSON: {"late_minutes": 50, "absent_days": 1, "unpaid_leaves": 0}
        $table->json('details')->nullable();

        // الحالة
        $table->enum('status', ['pending', 'paid'])->default('pending');

        $table->timestamps();

        // قيد يمنع تكرار مرتب لنفس الموظف في نفس الشهر والسنة
        $table->unique(['user_id', 'month', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};