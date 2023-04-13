<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    // #[Route('/sitemap.{_format<html|xml>}', name: 'app_sitemap', defaults: ['_format' => 'html'])]
    #[Route('/sitemap.xml', name: 'app_sitemap', methods: ['GET'])]
    public function show(Request $request, PostRepository $postRepository): Response
    {
        $hostname = $request->getSchemeAndHttpHost();

        $urls = [];

        $urls[] = ['loc' => $this->generateUrl('app_home')];

        $posts = $postRepository->findBy([], ['publishedAt' => 'DESC']);

        foreach ($posts as $post) {
            $urls[] = [
                'loc' => $this->generateUrl('app_posts_show', ['slug' => $post->getSlug()]),
                'lastmod' => $post->getUpdatedAt()->format('Y-m-d'),
                'changefreq' => 'weekly',
                'priority' => 0.9,
            ];
        }

        $response = $this->render('sitemap/show.html.twig', compact('urls', 'hostname'));

        $response->headers->set('Content-Type', 'text/xml');

        return $response;
    }
}
