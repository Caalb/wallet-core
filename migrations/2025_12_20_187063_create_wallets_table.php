<?php

declare(strict_types=1);

use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');
            $table->bigInteger('balance_cents')->default(0);
            $table->timestamps();

            $table->unique('user_id', 'wallets_user_id_unique');
        });

        Schema::getConnection()->statement(
            'ALTER TABLE wallets ADD CONSTRAINT wallets_balance_non_negative CHECK (balance_cents >= 0)',
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
