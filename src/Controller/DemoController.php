<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DemoController extends AbstractController
{
    #[Route('/demo', name: 'app_demo')]
    public function index(UserRepository $userRepository, EntityManagerInterface $em): Response
    {
        // $user = new User;
        // $user->setName('Carlos Cool');
        // $user->setEmail('carloscool@gmail.com');
        // $user->setPassword('$2y$13$VOV6Zs0DFNHSmU8Mk7DCwuHRe//dgvKaShMMKguZq9.zZVG2A6Ate');

        // $userRepository->add($user);

        // $user = $userRepository->find(6);

        // $user->setName('Nouvel Utsssilisateur');

        // $em->flush();

        return $this->render('demo.html.twig');
    }
}
