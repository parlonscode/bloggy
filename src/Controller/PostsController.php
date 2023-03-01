<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentFormType;
use App\Repository\TagRepository;
use App\Repository\PostRepository;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

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
    public function index(Request $request, ?string $tagSlug, PostRepository $postRepository, TagRepository $tagRepository, PaginatorInterface $paginator): Response
    {
        $tag = null;
        if ($tagSlug) {
            $tag = $tagRepository->findOneBySlug($tagSlug);
        }

        $query = $postRepository->createAllPublishedOrderedByNewestQuery($tag);

        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate(
            $query,
            $page,
            Post::NUM_ITEMS_PER_PAGE
        );

        $response = $this->render('posts/index.html.twig', [
            'pagination' => $pagination,
            'tagName' => $tag?->getName(),
        ])->setSharedMaxAge(30);

        $response->headers->set(
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, 'true'
        );
        
        return $response;
    }

    #[Route(
        '/posts/{slug}',
        name: 'app_posts_show',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST'],
    )]
    public function show(Request $request, Post $post, PostRepository $postRepository, CommentRepository $commentRepo): Response
    {
        $similarPosts = $postRepository->findSimilar($post);

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

        return $this->render('posts/show.html.twig', compact('post', 'comments', 'commentForm', 'similarPosts'));
    }

    #[Route('/posts/featured-content', name: 'app_posts_featured_content', methods: ['GET'], priority: 10)]
    public function featuredContent(PostRepository $postRepository, int $maxResults = 5): Response
    {
        $totalPosts = $postRepository->count([]);
        $latestPosts = $postRepository->findBy([], ['publishedAt' => 'DESC'], $maxResults);
        $mostCommentedPosts = $postRepository->findMostCommented($maxResults);

        return $this->render(
            'posts/_featured_content.html.twig',
            compact('totalPosts', 'latestPosts', 'mostCommentedPosts')
        )->setSharedMaxAge(50);
    }
}
