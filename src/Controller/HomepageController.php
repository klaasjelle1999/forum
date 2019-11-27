<?php

namespace App\Controller;

use App\Entity\Message;
use phpDocumentor\Reflection\Types\This;
use App\Entity\Notification;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        $messages = $this->getDoctrine()->getManager()->getRepository(Message::class)->findAll();
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

        if ($this->getUser()->getExtraInformationUser() === null) {
            return $this->redirectToRoute('edit_user', ['user' => $this->getUser()->getEmail()]);
        }

        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
            'messages' => $messages,
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
        ]);
    }
}
