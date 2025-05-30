<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticleRepository;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {

        return $this->render('index/index.html.twig');
    }
}