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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('otp_attempts')->default(0)->after('otp_expires_at')->comment('Jumlah kali gagal memasukkan OTP');
            $table->timestamp('last_otp_sent_at')->nullable()->after('otp_attempts')->comment('Waktu terakhir OTP dikirim untuk cooldown resend');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_attempts', 'last_otp_sent_at']);
        });
    }
};
