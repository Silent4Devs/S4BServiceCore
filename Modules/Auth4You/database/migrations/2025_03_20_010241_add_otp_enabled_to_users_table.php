<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            $table->boolean('otp_enabled')->default(false)->after('password');
            $table->string('otp_code')->nullable()->after('otp_enabled');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
        });
    }

    public function down()
    {
        Schema::connection('auth_db')->table('users', function (Blueprint $table) {
            $table->dropColumn(['otp_enabled', 'otp_code', 'otp_expires_at']);
        });
    }
};
