<?php

namespace App\Twig\Runtime;

use App\Repository\PostRepository;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(private readonly PostRepository $postRepository)
    {
    }

    public function pluralize(int $quantity, string $singular, ?string $plural = null): string
    {
        $plural ??= $singular.'s';

        $singularOrPlural = 1 === $quantity ? $singular : $plural;

        return "$quantity $singularOrPlural";
    }

    // public function totalPosts(): int
    // {
    //     return $this->postRepository->count([]);
    // }

    // public function latestPosts(int $maxResults = 5): array
    // {
    //     return $this->postRepository->findBy([], ['publishedAt' => 'DESC'], $maxResults);
    // }

    // public function mostCommentedPosts(int $maxResults = 5): array
    // {
    //     return $this->postRepository->findMostCommented($maxResults);
    // }

    // public function shasum256($value): string
    // {
    //     return hash('sha256', $value);
    // }
}
