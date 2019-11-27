<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/{user}/notifications", name="notification")
     * @param User $user
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"user": "email"}})
     * @return Response
     */
    public function index(User $user)
    {
        if (!$user === $this->getUser()) {
            throw new AccessDeniedException('U hebt geen toegang tot deze pagina!');
        }
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

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
            'controller_name' => 'NotificationController',
        ]);
    }

    /**
     * @Route(path="/notifications/read-all/{user}", name="mark_all_as_read")
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"user": "email"}})
     * @param User $user
     * @return RedirectResponse
     */
    public function markAllAsRead(User $user)
    {
        $notifications = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Notification::class)
            ->findAllNotificationsForUser($user)
        ;

        foreach ($notifications as $notification) {
            $notification->setIsRead(true);
            $this->em->merge($notification);
        }
        $this->em->flush();

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route(path="/notifications/create-friend-request/{user}", name="create_friend_request")
     * @param User $user
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"user": "email"}})
     * @return RedirectResponse
     * @throws \Exception
     */
    public function createFriendRequest(User $user)
    {
        $notification = new Notification();
        $notification
            ->setSender($this->getUser())
            ->setUser($user)
            ->setMessage('Sent a friend request')
            ->setIsRead(false)
            ->setCreatedAt(new \DateTime())
            ->setType('friendRequest')
            ->setStatus('Pending')
        ;

        $this->em->persist($notification);
        $this->em->flush();

        return $this->redirectToRoute('profile', [
            'user' => $user->getEmail(),
        ]);
    }

    /**
     * @Route(path="/notifications/remove-notification/{notification}", name="remove_notification")
     * @param Notification $notification
     * @return RedirectResponse
     */
    public function removeFriendRequest(Notification $notification)
    {
        $this->em->remove($notification);
        $this->em->flush();

        return $this->redirectToRoute('notification', [
            'user' => $this->getUser()->getEmail(),
        ]);
    }
}
