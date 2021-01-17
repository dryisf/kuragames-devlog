<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class DevlogController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('diary');
    }

    /**
     * @Route("/diary", name="diary")
     * @Route("/")
     */
    public function diary(ArticleRepository $articleRepo, CommentRepository $commentRepo, Comment $comment = null, Request $request, EntityManagerInterface $manager, PaginatorInterface $paginator): Response
    {
        $articles_data = $articleRepo->findAll();
        arsort($articles_data);
        $articles_per_page = 2;
        $article_page = $request->query->getInt('logs', 1);
        $articles = $paginator->paginate($articles_data, $article_page, $articles_per_page, ['pageParameterName' => 'logs']);

        $comments_data = $commentRepo->findAll();
        arsort($comments_data);
        $comments_per_page = 5;
        $comment_page = $request->query->getInt('comments', 1);
        $comments = $paginator->paginate($comments_data, $comment_page, $comments_per_page,['pageParameterName' => 'comments']);

        $comment = new Comment();
        $user = $this->security->getUser();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($user){
                $comment->setIsVerified(true);
            } else {
                $comment->setIsVerified(false);
            }

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('diary');
        }

        return $this->render('devlog/diary.html.twig', [
            'formComment' => $form->createView(),
            'totalArticles' => $articles_data,
            'articles' => $articles,
            'articlesPerPage' => $articles_per_page,
            'articlePage' => $article_page,
            'totalComments' => $comments_data,
            'comments' => $comments,
            'commentsPerPage' => $comments_per_page,
            'commentPage' => $comment_page
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
            $upload = $form->get('upload')->getData();

            if ($upload) {
                $originalFilename = pathinfo($upload->getClientOriginalName());
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename["filename"]);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$upload->guessExtension();

                try {
                    $upload->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                $article->setUpload($newFilename);
            }

            $content = $form->get('content')->getData();
            $contentEnglish = $form->get('content_english')->getData();

            if($content) {
                $newContent = trim(preg_replace('/\s\s+/', '<br>', $content));
                $article->setContent($newContent);
            }

            if($contentEnglish) {
                $newContentEnglish = trim(preg_replace('/\s\s+/', '<br>',$contentEnglish));
                $article->setContentEnglish($newContentEnglish);
            }

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

     /**
     * @Route("/diary/{id}/delete", name="diary_delete")
     */
    public function delete(Article $article = null, Request $request, EntityManagerInterface $manager){
            $manager->remove($article);
            $manager->flush();

            return $this->redirectToRoute('diary');
    }
}
