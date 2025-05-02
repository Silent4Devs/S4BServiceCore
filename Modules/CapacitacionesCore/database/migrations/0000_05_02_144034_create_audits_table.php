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
        Schema::connection('capacitaciones_db')->create('audits', function (Blueprint $table) {

            $morphPrefix = config('audit.user.morph_prefix', 'user');

            $table->bigIncrements('id');
            $table->string($morphPrefix . '_type')->nullable();
            $table->unsignedBigInteger($morphPrefix . '_id')->nullable();
            $table->string('event');
            $table->morphs('auditable');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();

            $table->index([$morphPrefix . '_id', $morphPrefix . '_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $connection = 'capacitaciones_db';
        $table = 'audits';

        Schema::connection($connection)->drop($table);
    }
};
