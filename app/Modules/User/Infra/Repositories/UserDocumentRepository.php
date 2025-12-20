<?php

declare(strict_types=1);

namespace App\Modules\User\Infra\Repositories;

use App\Modules\User\Domain\Entity\UserDocument;
use App\Modules\User\Domain\Enum\DocumentType;
use App\Modules\User\Domain\Exceptions\InvalidCNPJException;
use App\Modules\User\Domain\Exceptions\InvalidCPFException;
use App\Modules\User\Domain\Repositories\UserDocumentRepositoryInterface;
use App\Modules\User\Domain\ValueObject\CNPJ;
use App\Modules\User\Domain\ValueObject\CPF;
use App\Modules\User\Infra\Models\UserDocumentModel;
use RuntimeException;

class UserDocumentRepository implements UserDocumentRepositoryInterface
{
    public function findByUserId(int $userId): array
    {
        $models = UserDocumentModel::query()
            ->where('user_id', $userId)
            ->get();

        return $models->map(function ($model) {
            return $this->modelToEntity($model);
        })->toArray();
    }

    public function findByDocument(string $document): ?UserDocument
    {
        $model = UserDocumentModel::query()
            ->where('document', $document)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function existsByDocument(string $document): bool
    {
        return UserDocumentModel::query()
            ->where('document', $document)
            ->exists();
    }

    public function save(UserDocument $userDocument): void
    {
        $model = UserDocumentModel::query()->firstOrNew(['id' => $userDocument->getId()]);

        $model->user_id = $userDocument->getUserId();
        $model->document = $userDocument->getDocument()->getValue();
        $model->type = $userDocument->getType()->value;
        $model->save();

        if ($userDocument->getId() === 0) {
            $userDocument->setId($model->id);
        }
    }

    public function deleteByUserId(int $userId): void
    {
        UserDocumentModel::query()
            ->where('user_id', $userId)
            ->delete();
    }

    private function modelToEntity(UserDocumentModel $model): UserDocument
    {
        $documentType = DocumentType::from($model->type);

        try {
            $document = $documentType === DocumentType::CNPJ
                ? new CNPJ($model->document)
                : new CPF($model->document);
        } catch (InvalidCNPJException|InvalidCPFException $e) {
            throw new RuntimeException(
                "Invalid document in database for user_document ID {$model->id}: {$model->document}",
                0,
                $e,
            );
        }

        return new UserDocument(
            id: $model->id,
            userId: $model->user_id,
            document: $document,
            type: $documentType,
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
