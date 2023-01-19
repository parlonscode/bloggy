<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\SharePostFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class SharedPostsController extends AbstractController
{
    #[Route(
        '/posts/{slug}/share',
        name: 'app_posts_share',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function create(Request $request, Post $post, MailerInterface $mailer): Response
    {
        $form = $this->createForm(SharePostFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $subject = sprintf('%s recommends you to read "%s"', $data['sender_name'], $post->getTitle());

            $email = (new TemplatedEmail())
                ->from(
                    new Address(
                        $this->getParameter('app.contact_email'),
                        $this->getParameter('app.name')
                    )
                )
                ->to($data['receiver_email'])
                ->subject($subject)
                ->htmlTemplate('emails/shared_posts/create.html.twig')
                ->context([
                    'post' => $post,
                    'sender_name' => $data['sender_name'],
                    'sender_comments' => $data['sender_comments'],
                ])
            ;

            $mailer->send($email);

            $this->addFlash('success', '🚀 Post successfully shared with your friend!');

            return $this->redirectToRoute('app_home');
        }

        return $this->render('shared_posts/create.html.twig', compact('form', 'post'));
    }
}
