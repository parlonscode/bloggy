<?php

namespace App\EventSubscriber;

use App\Repository\PostRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Twig\Environment;

class TwigSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Environment $twig,
        private readonly PostRepository $postRepository,
        private readonly CacheInterface $cache
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $totalPosts = $this->cache->get('app.totalPosts', function (ItemInterface $item) {
            $item->expiresAfter(20);

            return $this->postRepository->count([]);
        });

        $latestPosts = $this->cache->get('app.latestPosts', function (ItemInterface $item) {
            $item->expiresAfter(30);

            return $this->postRepository->findBy([], ['publishedAt' => 'DESC'], 5);
        });

        $mostCommentedPosts = $this->cache->get('app.mostCommentedPosts', function (ItemInterface $item) {
            $item->expiresAfter(40);

            return $this->postRepository->findMostCommented(5);
        });

        $this->twig->addGlobal('totalPosts', $totalPosts);
        $this->twig->addGlobal('latestPosts', $latestPosts);
        $this->twig->addGlobal('mostCommentedPosts', $mostCommentedPosts);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
