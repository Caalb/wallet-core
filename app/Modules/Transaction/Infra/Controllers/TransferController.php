<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Controllers;

use App\Modules\Transaction\Application\DTO\TransferRequest as TransferDTO;
use App\Modules\Transaction\Application\UseCases\ProcessTransferUseCase;
use App\Modules\Transaction\Infra\Mappers\TransactionResponseMapper;
use App\Modules\Transaction\Infra\Requests\TransferRequest;
use App\Shared\Infrastructure\Http\AbstractController;
use Psr\Http\Message\ResponseInterface;

class TransferController extends AbstractController
{
    public function __construct(
        private ProcessTransferUseCase $processTransferUseCase,
    ) {
    }

    public function transfer(TransferRequest $request): ResponseInterface
    {
        $dto = new TransferDTO(
            payerId: $request->input('payer'),
            payeeId: $request->input('payee'),
            value: (float) $request->input('value'),
            idempotencyKey: $request->input('idempotency_key'),
        );

        $response = $this->processTransferUseCase->execute($dto);
        $mapped = TransactionResponseMapper::toHttpResponse($response);

        return $this->response
            ->json($mapped['data'])
            ->withStatus($mapped['status_code']);
    }
}
