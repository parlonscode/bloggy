<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasherInterface) {}

    public function onBeforeEntityPersistedEvent(BeforeEntityPersistedEvent $event): void
    {
        $entityInstance = $event->getEntityInstance();

        if ($entityInstance instanceof User && $entityInstance->getPlainPassword()) {
            $hashedPassword = $this->userPasswordHasherInterface->hashPassword($entityInstance, $entityInstance->getPlainPassword());

            $entityInstance->setPassword($hashedPassword);
        }
    }

    public function onBeforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event): void
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
            BeforeEntityPersistedEvent::class => 'onBeforeEntityPersistedEvent',
            BeforeEntityUpdatedEvent::class => 'onBeforeEntityUpdatedEvent',
        ];
    }
}
