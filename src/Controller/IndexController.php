<?php

namespace App\Controller;

use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ArticleRepository;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index')]
   public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query= $articleRepository->createQueryBuilder('a')
        ->getQuery();

        $pagination = $paginator ->paginate(
            $query,
            $request->$query->getInt('page',1),
            2);

        return $this->render('article/index.html.twig', [
            'pagination' => $articleRepository->findAll(),
        ]);
    }
}