<?php

declare(strict_types=1);

namespace HyperfTest\Unit\User\Domain\ValueObject;

use App\Modules\User\Domain\Exceptions\InvalidEmailException;
use App\Modules\User\Domain\ValueObject\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testShouldCreateValidEmail(): void
    {
        $email = new Email('john@gmail.com');

        $this->assertEquals('john@gmail.com', $email->getValue());
        $this->assertEquals('john@gmail.com', (string) $email);
    }

    public function testShouldNormalizeEmail(): void
    {
        $email = new Email('  JOHN@GMAIL.COM  ');

        $this->assertEquals('john@gmail.com', $email->getValue());
    }

    public function testShouldExtractDomain(): void
    {
        $email = new Email('john@gmail.com');

        $this->assertEquals('gmail.com', $email->getDomain());
    }

    public function testShouldExtractLocalPart(): void
    {
        $email = new Email('john.doe@gmail.com');

        $this->assertEquals('john.doe', $email->getLocalPart());
    }

    public function testShouldCompareEquality(): void
    {
        $email1 = new Email('john@gmail.com');
        $email2 = new Email('JOHN@GMAIL.COM');
        $email3 = new Email('jane@gmail.com');

        $this->assertTrue($email1->equals($email2));
        $this->assertFalse($email1->equals($email3));
    }

    public function testShouldBeJsonSerializable(): void
    {
        $email = new Email('john@gmail.com');

        $this->assertEquals('"john@gmail.com"', json_encode($email));
    }

    public function testShouldThrowExceptionForInvalidEmail(): void
    {
        $this->expectException(InvalidEmailException::class);

        new Email('invalid-email');
    }

    public function testShouldThrowExceptionForEmptyEmail(): void
    {
        $this->expectException(InvalidEmailException::class);

        new Email('');
    }

    public function testShouldThrowExceptionForEmailWithoutAt(): void
    {
        $this->expectException(InvalidEmailException::class);

        new Email('johnatexample.com');
    }
}
