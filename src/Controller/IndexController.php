<?php
declare(strict_types=1);

namespace App\Controller;

use App\Http\HtmlResponse;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Laminas\Diactoros\Response;
use League\Plates\Engine;
use Psr\Http\Message\RequestInterface;

class IndexController
{

    public function __construct(
        private readonly Engine $templateEngine,
        private readonly QueryBuilder $queryBuilder,
    )
    {
    }

    public function load(RequestInterface $request): Response
    {
        return new HtmlResponse($this->templateEngine->render('index'));
    }

}
