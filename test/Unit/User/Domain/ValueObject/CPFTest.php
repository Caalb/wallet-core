<?php

declare(strict_types=1);

namespace HyperfTest\Unit\User\Domain\ValueObject;

use App\Modules\User\Domain\Exceptions\InvalidCPFException;
use App\Modules\User\Domain\ValueObject\CPF;
use PHPUnit\Framework\TestCase;

class CPFTest extends TestCase
{
    public function testShouldCreateValidCPF(): void
    {
        $cpf = new CPF('123.456.789-09');

        $this->assertEquals('12345678909', $cpf->getValue());
        $this->assertEquals('12345678909', (string) $cpf);
    }

    public function testShouldCreateCPFWithoutFormatting(): void
    {
        $cpf = new CPF('12345678909');

        $this->assertEquals('12345678909', $cpf->getValue());
    }

    public function testShouldFormatCPF(): void
    {
        $cpf = new CPF('12345678909');

        $this->assertEquals('123.456.789-09', $cpf->getFormatted());
    }

    public function testShouldCompareEquality(): void
    {
        $cpf1 = new CPF('123.456.789-09');
        $cpf2 = new CPF('12345678909');
        $cpf3 = new CPF('987.654.321-00');

        $this->assertTrue($cpf1->equals($cpf2));
        $this->assertFalse($cpf1->equals($cpf3));
    }

    public function testShouldBeJsonSerializable(): void
    {
        $cpf = new CPF('12345678909');

        $json = json_encode($cpf);
        $this->assertStringContainsString('123.456.789-09', $json);
    }

    public function testShouldThrowExceptionForInvalidCPF(): void
    {
        $this->expectException(InvalidCPFException::class);

        new CPF('111.111.111-11');
    }

    public function testShouldThrowExceptionForShortCPF(): void
    {
        $this->expectException(InvalidCPFException::class);

        new CPF('123456789');
    }

    public function testShouldThrowExceptionForEmptyCPF(): void
    {
        $this->expectException(InvalidCPFException::class);

        new CPF('');
    }

    public function testShouldThrowExceptionForNonNumericCPF(): void
    {
        $this->expectException(InvalidCPFException::class);

        new CPF('abc.def.ghi-jk');
    }
}
