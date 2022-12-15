<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\SharePostFormType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use Symfony\Component\Mime\Address;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    #[Route(
        '/tags/{tagSlug}',
        name: 'app_posts_by_tag',
        requirements: [
            'tagSlug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET']
    )]
    public function index(Request $request, PaginatorInterface $paginator, PostRepository $postRepository, TagRepository $tagRepository, ?string $tagSlug): Response
    {
        $tag = null;
        if ($tagSlug) {
            $tag = $tagRepository->findOneBySlug($tagSlug);
        }

        $query = $postRepository->createAllPublishedOrderedQuery($tag);

        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate(
            $query,
            $page,
            Post::NUM_ITEMS_PER_PAGE
        );

        return $this->render('posts/index.html.twig', [
            'pagination' => $pagination,
            'tagName' => $tag?->getName()
        ]);
    }

    #[Route(
        '/posts/{slug}',
        name: 'app_posts_show',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function show(Post $post, Request $request, CommentRepository $commentRepo): Response
    {
        $comments = $post->getActiveComments();

        $commentForm = $this->createForm(CommentFormType::class);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment = $commentForm->getData();
            $comment->setPost($post);

            $commentRepo->save($comment, flush: true);

            $this->addFlash('success', '🚀 Comment successfully added!');

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('posts/show.html.twig', compact('post', 'comments', 'commentForm'));
    }
}
