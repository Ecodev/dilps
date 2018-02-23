<?php

declare(strict_types=1);

namespace Application\Action;

use Application\Model\Card;
use Application\Repository\CardRepository;
use Imagine\Image\Box;
use Imagine\Image\ImagineInterface;
use Interop\Http\Server\MiddlewareInterface;
use Interop\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Response\JsonResponse;

class ImageAction implements MiddlewareInterface
{
    private const CACHE_IMAGE_PATH = 'data/cache/images/';

    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    public function __construct(CardRepository $cardRepository, ImagineInterface $imagine)
    {
        $this->cardRepository = $cardRepository;
        $this->imagine = $imagine;
    }

    /**
     * Serve an image from disk, with optional dynamic resizing
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $id = $request->getAttribute('id');

        /** @var Card $card */
        $card = $this->cardRepository->findOneById($id);
        if (!$card) {
            return $this->getError("Card $id not found in database");
        }

        $path = $card->getPath();
        if (!is_readable($path)) {
            return $this->getError("Image for card $id not found on disk, or not readable");
        }

        $maxHeight = (int) $request->getAttribute('maxHeight');
        if ($maxHeight) {
            $path = $this->resize($card, $maxHeight);
        }

        $resource = fopen($path, 'r');
        $type = mime_content_type($path);
        $response = new Response($resource, 200, ['content-type' => $type]);

        return $response;
    }

    /**
     * @param string $message
     *
     * @return JsonResponse
     */
    private function getError(string $message): JsonResponse
    {
        $response = new JsonResponse(['error' => $message]);

        return $response->withStatus(404, $message);
    }

    /**
     * Resize image as JPG and return the path to the resized version
     *
     * @param Card $card
     * @param int $maxHeight
     *
     * @return string
     */
    private function resize(Card $card, int $maxHeight): string
    {
        if ($card->getHeight() <= $maxHeight) {
            return $card->getPath();
        }

        $basename = pathinfo($card->getFilename(), PATHINFO_FILENAME);
        $path = realpath('.') . '/' . self::CACHE_IMAGE_PATH . $basename . '-' . $maxHeight . '.jpg';

        if (file_exists($path)) {
            return $path;
        }

        $card = $this->imagine->open($card->getPath());
        $card->thumbnail(new Box(1000000, $maxHeight))->save($path);

        return $path;
    }
}
