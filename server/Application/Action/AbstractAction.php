<?php

declare(strict_types=1);

namespace Application\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Zend\Diactoros\Response\JsonResponse;

abstract class AbstractAction implements MiddlewareInterface
{
    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    protected function createError(string $message): JsonResponse
    {
        $response = new JsonResponse(['error' => $message]);

        return $response->withStatus(404, $message);
    }

    protected function getCards(ServerRequestInterface $request): array
    {
        $ids = explode(',', $request->getAttribute('ids'));

        /** @var Card $card */
        $cards = $this->cardRepository->findById($ids);
        if (!$cards) {
            return $this->createError('No cards found in database for any of the ids: ' . implode(', ', $ids));
        }
    }
}
