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
        Schema::create('queues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('queue_number', 20);
            $table->enum('status', ['waiting', 'called', 'completed', 'skipped'])->default('waiting');
            $table->date('queue_date');
            $table->timestamp('created_at');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->foreign('service_id')->references('id')->on('services');
            $table->foreign('admin_id')->references('id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queues');
    }
};
