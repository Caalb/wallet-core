<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('payer_id')
                ->constrained('users', 'id')
                ->onDelete('restrict');
            $table->foreignId('payee_id')
                ->constrained('users', 'id');
            $table->bigInteger('amount_cents');
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED'])->default('PENDING');
            $table->text('failure_reason')->nullable();
            $table->uuid('idempotency_key')->unique();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['payer_id', 'created_at']);
            $table->index(['payee_id', 'created_at']);
            $table->index(['payer_id', 'status']);
            $table->index('status');
            $table->index(['idempotency_key', 'status'], 'idx_transactions_idempotency_status');
        });

        Schema::getConnection()->statement(
            'ALTER TABLE transactions ADD CONSTRAINT transactions_amount_positive CHECK (amount_cents > 0)',
        );

        Schema::getConnection()->statement(
            'ALTER TABLE transactions ADD CONSTRAINT transactions_different_users CHECK (payer_id != payee_id)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
