<?php

declare(strict_types=1);

use App\Modules\User\Domain\Enum\DocumentType;
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $documentTypes = array_column(DocumentType::cases(), 'value');

        Schema::create('user_documents', function (Blueprint $table) use ($documentTypes) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('document', 14);
            $table->enum('type', $documentTypes);
            $table->timestamps();

            $table->unique(['user_id', 'type']);
            $table->index('document');
        });

        Schema::getConnection()->statement(
            'ALTER TABLE user_documents ADD CONSTRAINT user_documents_document_length CHECK (char_length(document) IN (11, 14))',
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_documents');
    }
};
