<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SearchFormType;
use Meilisearch\Bundle\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchedPostsController extends AbstractController
{
    #[Route('/search', name: 'app_searched_posts_create')]
    public function create(Request $request, SearchService $searchService): Response
    {
        $searchForm = $this->createForm(SearchFormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        $searchQuery = $request->query->get('q');

        $searchForm->handleRequest($request);

        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $searchResponse = $searchService->rawSearch(Post::class, $searchQuery, [
                'attributesToHighlight' => ['title', 'body'],
                'highlightPreTag' => '<mark>',
                'highlightPostTag' => '</mark>',
                'attributesToCrop' => ['body'],
                'cropLength' => 20,
            ]);
            $results = $searchResponse['hits'];
        }

        return $this->render('searched_posts/create.html.twig', [
            'searchQuery' => $searchQuery,
            'searchForm' => $searchForm,
            'tableau' => ['nom' => 'Parlons Code', 'age' => 6],
            'results' => $results ?? [],
        ]);
    }
}
