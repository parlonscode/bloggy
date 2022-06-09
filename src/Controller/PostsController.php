<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SharePostFormType;
use App\Repository\PostRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostsController extends AbstractController
{
    public function __construct(private PostRepository $postRepository) {}

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->postRepository->getAllPublishedOrderedQuery();

        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate(
            $query,
            $page,
            Post::NUM_ITEMS_PER_PAGE
        );

        return $this->render('posts/index.html.twig', compact('pagination'));
    }

    #[Route(
        '/posts/{date}/{slug}',
        name: 'app_posts_show',
        requirements: [
            'date' => Requirement::DATE_YMD,
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET']
    )]
    public function show(string $date, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishDateAndSlug($date, $slug);

        if (is_null($post)) {
            throw $this->createNotFoundException('Post not found!');
        }

        return $this->render('posts/show.html.twig', compact('post'));
    }

    #[Route(
        '/posts/{date}/{slug}/share',
        name: 'app_posts_share',
        requirements: [
            'date' => Requirement::DATE_YMD,
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function share(Request $request, string $date, string $slug): Response
    {
        $post = $this->postRepository->findOneByPublishDateAndSlug($date, $slug);

        if (is_null($post)) {
            throw $this->createNotFoundException('Post not found!');
        }

        $form = $this->createForm(SharePostFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dd($form->getData());
        }

        return $this->renderForm('posts/share.html.twig', compact('form', 'post'));
    }
}
