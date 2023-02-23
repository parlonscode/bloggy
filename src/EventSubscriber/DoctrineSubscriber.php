<?php

namespace App\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class DoctrineSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ('app_admin' !== $event->getRequest()->attributes->get('_route')) {
            $filter = $this->em->getFilters()->enable('published_filter');
            $filter->setParameter('current_datetime', new \DateTimeImmutable());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
