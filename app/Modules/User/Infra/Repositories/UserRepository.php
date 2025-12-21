<?php

declare(strict_types=1);

namespace App\Modules\User\Infra\Repositories;

use App\Modules\User\Domain\Entity\User;
use App\Modules\User\Domain\Enum\UserType;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Domain\ValueObject\Email;
use App\Modules\User\Infra\Models\UserModel;
use ReflectionClass;

class UserRepository implements UserRepositoryInterface
{
    public function findById(int $id): ?User
    {
        $model = UserModel::query()->find($id);

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::query()
            ->where('email', $email->getValue())
            ->first();

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function existsByEmail(Email $email): bool
    {
        return UserModel::query()
            ->where('email', $email->getValue())
            ->exists();
    }

    public function save(User $user): void
    {
        $model = UserModel::query()->firstOrNew(['id' => $user->getId()]);

        $model->name = $user->getName();
        $model->email = $user->getEmail()->getValue();
        $model->password = $user->getPassword();
        $model->type = $user->getType()->value;
        $model->save();

        if ($user->getId() === 0) {
            $reflection = new ReflectionClass($user);
            $property = $reflection->getProperty('id');
            $property->setValue($user, $model->id);
        }
    }

    public function delete(User $user): void
    {
        UserModel::query()
            ->where('id', $user->getId())
            ->delete();
    }

    private function modelToEntity(UserModel $model): User
    {
        $email = new Email($model->email);

        return new User(
            id: $model->id,
            name: $model->name,
            email: $email,
            password: $model->password,
            type: UserType::from($model->type),
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
