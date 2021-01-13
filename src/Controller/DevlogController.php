<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DevlogController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('devlog/index.html.twig', [
            'controller_name' => 'DevlogController',
        ]);
    }

    /**
     * @Route("/diary", name="diary")
     */
    public function diary(ArticleRepository $repo): Response
    {
        $articles = $repo->findAll();

        return $this->render('devlog/diary.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/diary/new", name="diary_create")
     * @Route("/diary/{id}/edit", name="diary_edit")
     */
    public function form(Article $article = null, Request $request, EntityManagerInterface $manager){
        if (!$article) {
            $article = new Article();
        }

        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) {
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('diary');
        }

        return $this->render('devlog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
}
