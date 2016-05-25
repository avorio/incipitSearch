<?php
    namespace ADWLM\IncipitSearch;

    /**
     * Copyright notice
     *
     * (c) 2016
     * Anna Neovesky Anna.Neovesky@adwmainz.de
     * Gabriel Reimers g.a.reimers@gmail.com
     *
     * Digital Academy www.digitale-akademie.de
     * Academy of Sciences and Literatur | Mainz www.adwmainz.de
     *
     * Licensed under The MIT License (MIT)
     */

    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    use Twig_Loader_Filesystem;
    use Twig_Environment;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;

    require 'vendor/autoload.php';

    $logger = new Logger('annilogger');
    $filehandler = new StreamHandler("logs/app.log");
    $logger->pushHandler($filehandler);


    $configuration = [
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ];

    $container = new \Slim\Container($configuration);
    $app = new \Slim\App($container);

    // Register component on container
    $container['view'] = function ($container) {
        $view = new \Slim\Views\Twig('templates', [
            'cache' => false
        ]);
        $view->addExtension(new \Slim\Views\TwigExtension(
            $container['router'],
            $container['request']->getUri()
        ));

        return $view;
    };

    $app->get('/hello/{name}', function (Request $request, Response $response) {
        $name = $request->getAttribute('name');
        $response->getBody()->write("Hello, $name");
        return $response;
    });

    // currently without URL rewrite; access: http://incipitsearch.local/index.php/hello;
    // http://localhost:8080/hello
    //TODO: specify URL rewrite http://docs.slimframework.com/routing/rewrite/


    $app->get('/', function (Request $request, Response $response) {
        return $this->view->render($response, 'index.html', []);
    });
    $app->run();