<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentaire;
use App\Form\CommentType;
use App\Repository\CommentaireRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\ArticleForm;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/article')]
final class ArticleController extends AbstractController
{
    #[Route('/',name: 'app_article_index', methods: ['GET'])]
   public function index(ArticleRepository $articleRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query= $articleRepository->createQueryBuilder('a')
        ->getQuery();

        $pagination = $paginator ->paginate(
            $query,
            $request->query->getInt('page',1),
            1);

        return $this->render('article/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/publicnew', name: 'app_article_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTime());
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/new.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_show', methods: ['GET', 'POST'])]
public function show(Article $article, Request $request, CommentaireRepository $commentRepository): Response
{
    // Création d'un nouveau commentaire
    $comment = new Commentaire();
    $comment->setArticle($article); // Associe automatiquement l'article
    $comment->setCreatedAt(new \DateTime());

    // Création du formulaire
    $commentForm = $this->createForm(CommentType::class, $comment);
    $commentForm->handleRequest($request);

    // Traitement du formulaire s'il est soumis et valide
    if ($commentForm->isSubmitted() && $commentForm->isValid()) {
        $commentRepository->save($comment, true);

        // Redirection pour éviter la resoumission du formulaire
        return $this->redirectToRoute('app_article_show', ['id' => $article->getId()], Response::HTTP_SEE_OTHER);
    }

    return $this->render('article/show.html.twig', [
        'article' => $article,
        'commentForm' => $commentForm->createView(),
        // Les commentaires sont accessibles via $article->getComments()
    ]);
}

    #[Route('/{id}/edit', name: 'app_article_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ArticleForm::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('article/edit.html.twig', [
            'article' => $article,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_article_delete', methods: ['POST'])]
    public function delete(Request $request, Article $article, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$article->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($article);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_article_index', [], Response::HTTP_SEE_OTHER);
    }
}
