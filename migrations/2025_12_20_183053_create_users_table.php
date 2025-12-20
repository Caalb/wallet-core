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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('type', ['COMMON', 'MERCHANT'])->default('COMMON');
            $table->timestamps();
        });

        Schema::getConnection()->statement(
            'ALTER TABLE users ADD CONSTRAINT users_name_not_empty CHECK (char_length(trim(name)) > 0)',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
