#! /usr/bin/env php
<?php

/**
 * A script to update image size data in DB from file on disk
 */
use Application\Model\Card;
use Imagine\Image\ImagineInterface;

require_once __DIR__ . '/../htdocs/index.php';

global $container;

/** @var ImagineInterface $imagine */
$imagine = $container->get(ImagineInterface::class);

$connection = _em()->getConnection();
$filesInDb = _em()->getRepository(Card::class)->getFilenamesForDimensionUpdate();
$count = 0;
$total = count($filesInDb);
foreach ($filesInDb as $i => $card) {
    if ($i === 0 || $i % 500 === 0) {
        echo $i . '/' . $total . PHP_EOL;
    }

    if (!file_exists($card['filename'])) {
        continue;
    }

    $image = $imagine->open($card['filename']);
    $size = $image->getSize();

    // To force clearing the cache of Imagick
    $image->__destruct();

    $width = $size->getWidth();
    $height = $size->getHeight();

    if ($width !== (int) $card['width'] || $height !== (int) $card['height']) {
        $count += $connection->update('card', ['width' => $width, 'height' => $height], ['id' => $card['id']]);
    }
}

echo '
Updated records in DB: ' . $count . '
';
