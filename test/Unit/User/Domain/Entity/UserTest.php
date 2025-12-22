<?php

declare(strict_types=1);

namespace HyperfTest\Unit\User\Domain\Entity;

use App\Modules\User\Domain\Entity\User;
use App\Modules\User\Domain\Enum\UserType;
use App\Modules\User\Domain\Exceptions\UnauthorizedTransferException;
use App\Modules\User\Domain\ValueObject\Email;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testShouldCreateUser(): void
    {
        $user = new User(
            id: 1,
            name: 'Chico',
            email: new Email('chico@gmail.com'),
            password: 'hashed_password',
            type: UserType::COMMON,
        );

        $this->assertEquals(1, $user->getId());
        $this->assertEquals('Chico', $user->getName());
        $this->assertEquals('chico@gmail.com', $user->getEmail()->getValue());
        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertEquals(UserType::COMMON, $user->getType());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getUpdatedAt());
    }

    public function testCommonUserCanTransfer(): void
    {
        $commonUser = new User(
            id: 1,
            name: 'Chico',
            email: new Email('chico@gmail.com'),
            password: 'hashed_password',
            type: UserType::COMMON,
        );

        $this->expectNotToPerformAssertions();
        $commonUser->validateCanTransfer();
    }

    public function testMerchantUserCannotTransfer(): void
    {
        $merchant = new User(
            id: 1,
            name: 'Store Inc',
            email: new Email('store@gmail.com'),
            password: 'hashed_password',
            type: UserType::MERCHANT,
        );

        $this->expectException(UnauthorizedTransferException::class);

        $merchant->validateCanTransfer();
    }

    public function testShouldUpdateProfile(): void
    {
        $user = new User(
            id: 1,
            name: 'Chico',
            email: new Email('chico@gmail.com'),
            password: 'hashed_password',
            type: UserType::COMMON,
        );

        $user->updateProfile('Carlos Jr');

        $this->assertEquals('Carlos Jr', $user->getName());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getUpdatedAt());
    }

    public function testShouldSetPassword(): void
    {
        $user = new User(
            id: 1,
            name: 'Chico',
            email: new Email('chico@gmail.com'),
            password: 'old_hashed_password',
            type: UserType::COMMON,
        );

        $user->setPassword('new_hashed_password');

        $this->assertEquals('new_hashed_password', $user->getPassword());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getUpdatedAt());
    }
}
