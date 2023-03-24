<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SearchFormType;
use Meilisearch\Bundle\SearchService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchedPostsController extends AbstractController
{
    #[Route('/search', name: 'app_searched_posts_create')]
    public function create(Request $request, EntityManagerInterface $em, SearchService $searchService): Response
    {
        $searchForm = $this->createForm(SearchFormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false
        ]);

        $searchQuery = $request->query->get('q');

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $results = $searchService->search($em, Post::class, $searchQuery);
        }

        return $this->renderForm('searched_posts/create.html.twig', [
            'searchQuery' => $searchQuery,
            'searchForm' => $searchForm,
            'results' => $results ?? []
        ]);
    }
}
