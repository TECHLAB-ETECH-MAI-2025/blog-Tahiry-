<?php

namespace App\Controller\Api;

use App\Entity\Article;
use App\Entity\ArticleLike;
use App\Repository\ArticleLikeRepository;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class ArticleController extends AbstractController
{
    #[Route('/api/articles', name: 'api_articles', methods: ['GET'])]
    public function getArticles(Request $request, ArticleRepository $articleRepository): JsonResponse
    {
        
        $draw = $request->query->getInt('draw', 1);
        $start = $request->query->getInt('start', 0);
        $length = $request->query->getInt('length', 3);
        $search = $request->query->all('search')['value'] ?? null;
        $order = $request->query->all('order')[0] ?? null;

        
        $results = $articleRepository->findForDataTable($start, $length, $search, $order);
        $totalRecords = $articleRepository->count([]);
        $filteredRecords = $search ? $articleRepository->countSearch($search) : $totalRecords;

        
        $data = [];
        foreach ($results as $article) {
            $data[] = [
                'id' => $article->getId(),
                'title' => '<a href="/article/'. $article->getId() . '">' . $article->getTitle() . '</a>',
                'content' => substr($article->getContent(), 0, 75) . '...',
                'createdAt' => $article->getCreatedAt()->format('Y-m-d H:i:s'),
                'commentCount' => $article->getCommentaires()->count()
            ];
        }

        return $this->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
   
    }

    #[Route('/api/articles/{id}/like', name: 'api_article_like', methods: ['POST'])]
    public function toogleLike(Article $article, ArticleLikeRepository $likeRepository, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();

        $existingLike = $likeRepository->findOneBy([
            'article' => $article,
            'article_user' => $user,
        ]);

        if ($existingLike) {
           
            $em->remove($existingLike);
            $em->flush();
            return new JsonResponse([
                'success' => true,
                'liked' => false,
                'likesCount' => $article->getArticleLikes()->count()
            ]);
        } else {
            
            $like = new ArticleLike();
            $like->setArticle($article);
            $like->setArticleUser($user);
            $like->setCreatedAt(new \DateTimeImmutable());
            $em->persist($like);
            $em->flush();
            return new JsonResponse([
                'success' => true,
                'liked' => true,
                'likesCount' => $article->getArticleLikes()->count()
            ]);
        }
    }
}