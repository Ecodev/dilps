<?php

declare(strict_types=1);

namespace Application\Action;

use Application\Model\Card;
use Application\Model\User;
use Application\Repository\CardRepository;
use Application\Service\ImageService;
use Application\Stream\TemporaryFile;
use Imagine\Image\ImagineInterface;
use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\Shape\RichText;
use PhpOffice\PhpPresentation\Shape\RichText\Run;
use PhpOffice\PhpPresentation\Slide;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Writer\PowerPoint2007;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

/**
 * Serve multiples cards as PowerPoint file
 */
class PptxAction extends AbstractAction
{
    /**
     * @var CardRepository
     */
    private $cardRepository;

    /**
     * @var ImagineInterface
     */
    private $imagine;

    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * @var bool
     */
    private $needSeparator = false;

    /**
     * @var string
     */
    private $textColor = Color::COLOR_WHITE;

    /**
     * @var string
     */
    private $backgroundColor = Color::COLOR_BLACK;

    public function __construct(CardRepository $cardRepository, ImageService $imageService, ImagineInterface $imagine)
    {
        $this->cardRepository = $cardRepository;
        $this->imageService = $imageService;
        $this->imagine = $imagine;
    }

    /**
     * Serve multiples cards as PowerPoint file
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $ids = explode(',', $request->getAttribute('ids'));
        $this->textColor = $request->getAttribute('textColor', $this->textColor);
        $this->backgroundColor = $request->getAttribute('backgroundColor', $this->backgroundColor);

        /** @var Card $card */
        $cards = $this->cardRepository->findById($ids);
        if (!$cards) {
            return $this->createError('No cards found in database for any of the ids: ' . implode(', ', $ids));
        }

        $title = 'DILPS ' . date('c', time());
        $presentation = $this->export($cards, $title);

        // Write to disk
        $tempFile = tempnam('data/tmp/', 'pptx');
        $writer = new PowerPoint2007($presentation);
        $writer->save($tempFile);

        $headers = [
            'content-type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'content-length' => filesize($tempFile),
            'content-disposition' => 'inline; filename="' . $title . '.pptx"',
        ];
        $stream = new TemporaryFile($tempFile, 'r');
        $response = new Response($stream, 200, $headers);

        return $response;
    }

    /**
     * Export all cards into a presentation
     *
     * @param Card[] $cards
     * @param string $title
     *
     * @return PhpPresentation
     */
    private function export(array $cards, string $title): PhpPresentation
    {
        $presentation = new PhpPresentation();

        // Set a few meta data
        $properties = $presentation->getDocumentProperties();
        $properties->setCreator(User::getCurrent() ? User::getCurrent()->getLogin() : '');
        $properties->setLastModifiedBy('DILPS');
        $properties->setTitle($title);
        $properties->setSubject('Présentation PowerPoint générée par le système DILPS');
        $properties->setDescription("Certaines images sont soumises aux droits d'auteurs. Vous pouvez nous contactez à diatheque@unil.ch pour plus d'informations.");
        $properties->setKeywords("Université de Lausanne, Section d'Histoire de l'art");
        $properties->setCategory("Histoire de l'art");

        // Remove default slide
        $presentation->removeSlideByIndex(0);

        foreach ($cards as $card) {
            $this->exportCard($presentation, $card);
        }

        return $presentation;
    }

    private function exportCard(PhpPresentation $presentation, Card $card): void
    {
        // Skip if no image
        if (!$card->hasImage() || !is_readable($card->getPath())) {
            return;
        }

        // Create slide
        $slide = $presentation->createSlide();
        $slide->setBackground();

        // Set background color
        $backgroundColor = new \PhpOffice\PhpPresentation\Slide\Background\Color();
        $backgroundColor->setColor(new Color($this->backgroundColor));
        $slide->setBackground($backgroundColor);

        $this->insertImage($slide, $card);
        $this->insertLegend($slide, $card);
    }

    private function insertImage(Slide $slide, Card $card): void
    {
        $path = $this->imageService->resize($card, 1200);

        // Get dimensions
        $image = $this->imagine->open($path);
        $size = $image->getSize();
        $width = $size->getWidth();
        $height = $size->getHeight();
        $ratio = $width / $height;

        $shape = $slide->createDrawingShape();
        $shape->setPath($path);

        if ($height < 1100) {
            $shape->setWidth(915);
            $shape->setOffsetX(21);
            $shape->setOffsetY(((615 - (915 / $ratio)) / 2) + 10);
        } else {
            $shape->setHeight(615);
            $shape->setOffsetX(((950 - (615 * $ratio)) / 2) + 3);
            $shape->setOffsetY(10);
        }
    }

    private function insertLegend(Slide $slide, Card $card): void
    {
        $shape = $slide->createRichTextShape();
        $shape->setHeight(75);
        $shape->setWidth(900);
        $shape->setOffsetX(25);
        $shape->setOffsetY(625);
        $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $this->needSeparator = false;
        foreach ($card->getArtists() as $artist) {
            $this->appendText($shape, true, true, false, $artist->getName());
        }

        $this->appendText($shape, true, false, true, $card->getName());
        $this->appendText($shape, true, false, false, $card->getDating());
        $shape->createBreak();
        $this->needSeparator = false;
        $this->appendText($shape, false, false, false, $card->getMaterial());
        $this->appendText($shape, false, false, false, $card->getFormat());
        $this->appendText($shape, false, false, false, $card->getLocality());

        if ($card->getInstitution()) {
            $this->appendText($shape, false, false, false, $card->getInstitution()->getName());
        }
    }

    private function appendText(RichText $shape, bool $big, bool $bold, bool $italic, string $value): void
    {
        if (!$value) {
            return;
        }

        if ($this->needSeparator) {
            $textRun = $shape->createTextRun(', ');
            $this->setFont($textRun, $big, false, false);
        }

        $textRun = $shape->createTextRun(str_replace(',', ' ', $value));
        $this->setFont($textRun, $big, $bold, $italic);

        $this->needSeparator = true;
    }

    private function setFont(Run $textRun, bool $big, bool $bold, bool $italic): void
    {
        $font = $textRun->getFont();
        $font->setSize($big ? 14 : 12);
        $font->setColor(new Color($this->textColor));
        $font->setBold($bold);
        $font->setItalic($italic);
    }
}
