<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Message;
use App\Entity\Notification;
use App\Form\CreateMessageFormType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/message/{message}", name="message")
     * @param Message $message
     * @return Response
     * @throws Exception
     */
    public function index(Message $message)
    {
        $notifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNotificationsForUser($this->getUser())
        ;

        $nonReadNotifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNonReadNotificationsForUser($this->getUser())
        ;

        if (isset($_POST['submit'])) {
            $comment = new Comment();
            $notification = new Notification();
            $notification
                ->setSender($this->getUser())
                ->setType('newComment')
                ->setCreatedAt(new DateTime('now'))
                ->setIsRead(false)
                ->setMessage(' posted a comment on your message.')
                ->setUser($message->getUser())
                ->setPath('/message/' . $message->getId())
            ;
            $comment
                ->setUser($this->getUser())
                ->setContent($_POST['comment'])
                ->setMessage($message)
                ->setCreatedAt(new DateTime('now'))
            ;

            $this->em->persist($notification);
            $this->em->persist($comment);
            $this->em->flush();
        }

        return $this->render('message/index.html.twig', [
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
            'controller_name' => 'MessageController',
            'message' => $message,
        ]);
    }

    /**
     * @Route(path="/messages/edit/{message}", name="edit_message")
     * @param Message $message
     * @return RedirectResponse
     */
    public function editMessage(Message $message)
    {
        $data = $_POST['message'];

        $message->setContent($data);

        $this->em->merge($message);
        $this->em->flush();

        return $this->redirectToRoute('message', [
            'message' => $message->getId(),
        ]);
    }

    /**
     * @Route(path="/messages/view/delete/{message}", name="view_delete_message")
     * @param Message|null $message
     * @return Response
     */
    public function viewDeleteModalMessage(Message $message = null)
    {
        $notifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNotificationsForUser($this->getUser())
        ;

        $nonReadNotifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNonReadNotificationsForUser($this->getUser())
        ;

        if (!$message instanceof Message) {
            throw new NotFoundHttpException('Dit bericht is niet gevonden!');
        }

        return $this->render('message/hard_delete_message.html.twig', [
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
            'message' => $message,
        ]);
    }

    /**
     * @Route(path="/messages/delete/{message}", name="delete_message")
     * @param Message|null $message
     * @return RedirectResponse
     */
    public function hardDeleteMessage(Message $message = null)
    {
        if (!$message instanceof Message) {
            throw new NotFoundHttpException('Dit bericht is niet gevonden!');
        }
        
        foreach ($message->getComments() as $comment) {
            $this->em->remove($comment);
            $this->em->flush();
        }

        $this->em->remove($message);
        $this->em->flush();

        $this->addFlash('danger', 'U hebt het bericht "'.$message->getTitle().'" verwijderd!');
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route(path="/messages/create", name="create_message")
     * @param Request $request
     * @return Response
     */
    public function createMessage(Request $request)
    {
        $notifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNotificationsForUser($this->getUser())
        ;

        $nonReadNotifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNonReadNotificationsForUser($this->getUser())
        ;

        $form = $this->createForm(CreateMessageFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $message = new Message();

            $message
                ->setTitle($data['title'])
                ->setContent($data['content'])
                ->setUser($this->getUser())
            ;

            $this->em->persist($message);
            $this->em->flush();

            $this->addFlash('success', 'U hebt een bericht aangemaakt!');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('message/create.html.twig', [
            'form' => $form->createView(),
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
        ]);
    }
}
