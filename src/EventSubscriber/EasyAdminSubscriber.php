<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasherInterface,
        private readonly EntityManagerInterface $em
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ('app_admin' === $event->getRequest()->attributes->get('_route')) {
            $this->em->getFilters()->disable('published_filter');
        }
    }

    public function updateUserPassword(BeforeEntityPersistedEvent|BeforeEntityUpdatedEvent $event): void
    {
        $entityInstance = $event->getEntityInstance();

        if ($entityInstance instanceof User && $entityInstance->getPlainPassword()) {
            $hashedPassword = $this->userPasswordHasherInterface->hashPassword($entityInstance, $entityInstance->getPlainPassword());

            $entityInstance->setPassword($hashedPassword);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            BeforeEntityPersistedEvent::class => 'updateUserPassword',
            BeforeEntityUpdatedEvent::class => 'updateUserPassword',
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
