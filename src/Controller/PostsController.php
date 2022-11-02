<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\SharePostFormType;
use App\Repository\PostRepository;
use Symfony\Component\Mime\Address;
use App\Repository\CommentRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Common\Collections\Criteria;

class PostsController extends AbstractController
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $query = $this->postRepository->getAllPublishedOrderedQuery();

        $page = $request->query->getInt('page', 1);

        $pagination = $paginator->paginate(
            $query,
            $page,
            Post::NUM_ITEMS_PER_PAGE
        );

        return $this->render('posts/index.html.twig', compact('pagination'));
    }

    #[Route(
        '/posts/{slug}',
        name: 'app_posts_show',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function show(Post $post, Request $request, CommentRepository $commentRepo): Response
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('isActive', true))
            ->orderBy(['createdAt' => 'ASC']);

        $comments = $post->getComments()->matching($criteria);

        $commentForm = $this->createForm(CommentFormType::class);

        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment = $commentForm->getData();
            $comment->setPost($post);

            $commentRepo->save($comment, flush: true);

            $this->addFlash('success', '🚀 Comment successfully added!');

            return $this->redirectToRoute('app_posts_show', ['slug' => $post->getSlug()]);
        }

        return $this->renderForm('posts/show.html.twig', compact('post', 'comments', 'commentForm'));
    }

    #[Route(
        '/posts/{slug}/share',
        name: 'app_posts_share',
        requirements: [
            'slug' => Requirement::ASCII_SLUG,
        ],
        methods: ['GET', 'POST']
    )]
    public function share(Request $request, MailerInterface $mailer, Post $post): Response
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
                ->htmlTemplate('emails/posts/share.html.twig')
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

        return $this->renderForm('posts/share.html.twig', compact('form', 'post'));
    }
}
