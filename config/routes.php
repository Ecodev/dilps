<?php

declare(strict_types=1);
/*
 * Setup routes with a single request method:
 *
 * $app->get('/', Application\Action\HomePageAction::class, 'home');
 * $app->post('/album', Application\Action\AlbumCreateAction::class, 'album.create');
 * $app->put('/album/:id', Application\Action\AlbumUpdateAction::class, 'album.put');
 * $app->patch('/album/:id', Application\Action\AlbumUpdateAction::class, 'album.patch');
 * $app->delete('/album/:id', Application\Action\AlbumDeleteAction::class, 'album.delete');
 *
 * Or with multiple request methods:
 *
 * $app->route('/contact', Application\Action\ContactAction::class, ['GET', 'POST', ...], 'contact');
 *
 * Or handling all request methods:
 *
 * $app->route('/contact', Application\Action\ContactAction::class)->setName('contact');
 *
 * or:
 *
 * $app->route(
 *     '/contact',
 *     Application\Action\ContactAction::class,
 *     Zend\Expressive\Router\Route::HTTP_METHOD_ANY,
 *     'contact'
 * );
 */
$app->get('/', Application\Action\HomePageAction::class, 'home');
$app->get('/api/ping', Application\Action\PingAction::class, 'api.ping');
$app->post('/graphql', Application\Action\GraphQLAction::class, 'graphql');
$app->get('/image-src/{id:\d+}[/{maxHeight:\d+}]', Application\Action\ImageAction::class, 'image');
