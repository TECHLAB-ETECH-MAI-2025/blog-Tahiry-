<?php

namespace App\Controller\Api;

use App\Entity\Commentaire;
use App\Entity\Article;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

#[Route('/api/comments')]
class CommentaireController extends AbstractController
{
    #[Route('/{id}/add', name: 'api_comment_add', methods: ['POST'])]
    public function addComment(
        Request $request,
        Article $article,
        EntityManagerInterface $em,
        HubInterface $hub
    ): JsonResponse {

        $comment = new Commentaire();
        $comment->setContent($request->request->get('content', ''));
        $comment->setAuthor($this->getUser());
        $comment->setArticle($article);
        $comment->setCreatedAt(new \DateTime());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'commentHtml' => $this->renderView('commentaire/_comment.html.twig', [
                    'commentaire' => $comment,
                    'csrfToken' => $this->container->get('security.csrf.token_manager')->getToken('delete'.$comment->getId())->getValue()
                ]),
            ]);
        }

        return new JsonResponse([
            'success' => false
        ]);
    }

    #[Route('/article/{id}', name: 'api_comments_get', methods: ['GET'])]
    #[Route('/article/{id}/latest', name: 'api_comments_latest', methods: ['GET'])]
    public function getComments(Article $article, EntityManagerInterface $em): JsonResponse
    {
        $comments = $em->getRepository(Commentaire::class)
            ->findBy(['article' => $article], ['createdAt' => 'DESC'], 10);

        $commentsData = array_map(function($comment) {
            return [
                'id' => $comment->getId(),
                'author' => $comment->getAuthor(),
                'content' => $comment->getContent(),
                'createdAt' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'csrfToken' => $this->container->get('security.csrf.token_manager')->getToken('delete'.$comment->getId())->getValue()
            ];
        }, $comments);

        return $this->json($commentsData);
    }
}