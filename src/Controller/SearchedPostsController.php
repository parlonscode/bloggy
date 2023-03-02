<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SearchedPostsController extends AbstractController
{
    #[Route('/search', name: 'app_searched_posts_create')]
    public function create(Request $request, PostRepository $postRepository): Response
    {
        if ($q = $request->query->get('q')) {
            $results = $postRepository->search($q);

            dd($results);
        }
        
        return $this->render('searched_posts/create.html.twig');
    }
}
