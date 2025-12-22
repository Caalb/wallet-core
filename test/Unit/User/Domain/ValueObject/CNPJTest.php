<?php

declare(strict_types=1);

namespace HyperfTest\Unit\User\Domain\ValueObject;

use App\Modules\User\Domain\Exceptions\InvalidCNPJException;
use App\Modules\User\Domain\ValueObject\CNPJ;
use PHPUnit\Framework\TestCase;

class CNPJTest extends TestCase
{
    public function testShouldCreateValidCNPJ(): void
    {
        $cnpj = new CNPJ('11.222.333/0001-81');

        $this->assertEquals('11222333000181', $cnpj->getValue());
        $this->assertEquals('11222333000181', (string) $cnpj);
    }

    public function testShouldCreateCNPJWithoutFormatting(): void
    {
        $cnpj = new CNPJ('11222333000181');

        $this->assertEquals('11222333000181', $cnpj->getValue());
    }

    public function testShouldFormatCNPJ(): void
    {
        $cnpj = new CNPJ('11222333000181');

        $this->assertEquals('11.222.333/0001-81', $cnpj->getFormatted());
    }

    public function testShouldCompareEquality(): void
    {
        $cnpj1 = new CNPJ('11.222.333/0001-81');
        $cnpj2 = new CNPJ('11222333000181');
        $cnpj3 = new CNPJ('11.444.777/0001-61');

        $this->assertTrue($cnpj1->equals($cnpj2));
        $this->assertFalse($cnpj1->equals($cnpj3));
    }

    public function testShouldBeJsonSerializable(): void
    {
        $cnpj = new CNPJ('11222333000181');

        $json = json_encode($cnpj);
        $this->assertStringContainsString('11.222.333', $json);
        $this->assertStringContainsString('0001-81', $json);
    }

    public function testShouldThrowExceptionForInvalidCNPJ(): void
    {
        $this->expectException(InvalidCNPJException::class);

        new CNPJ('11.111.111/1111-11');
    }

    public function testShouldThrowExceptionForShortCNPJ(): void
    {
        $this->expectException(InvalidCNPJException::class);

        new CNPJ('1122233300018');
    }

    public function testShouldThrowExceptionForEmptyCNPJ(): void
    {
        $this->expectException(InvalidCNPJException::class);

        new CNPJ('');
    }

    public function testShouldThrowExceptionForNonNumericCNPJ(): void
    {
        $this->expectException(InvalidCNPJException::class);

        new CNPJ('ab.cde.fgh/ijkl-mn');
    }
}
