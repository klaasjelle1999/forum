<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\Relationship;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class FriendshipController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route(path="/friendrequest/send/{user}", name="send_friendrequest")
     * @param User $user
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"user": "email"}})
     * @return RedirectResponse
     */
    public function sendRequest(User $user)
    {
        $relationship = new Relationship();
        $notification = new Notification();

        $relationship
            ->setUserOne($this->getUser())
            ->setUserTwo($user)
            ->setStatus(0);

        $notification
            ->setSender($this->getUser())
            ->setUser($user)
            ->setMessage('Sent a friend request')
            ->setIsRead(false)
            ->setCreatedAt(new \DateTime())
            ->setType('notification');

        $this->em->persist($notification);
        $this->em->persist($relationship);
        $this->em->flush();

        return $this->redirectToRoute('profile', [
            'user' => $user->getEmail(),
        ]);
    }

    /**
     * @Route(path="/friendrequest/add/{notification}", name="add_friend")
     * @param Relationship $relationship
     * @param Notification $notification
     */
    public function addFriend(Notification $notification)
    {
        $relationship = $this
                ->getDoctrine()
                ->getManager()
                ->getRepository(Relationship::class)
                ->findFriend($notification->getSender(), $this->getUser())
        ;

        $relationship
            ->setStatus(1)
        ;

        $this->em->remove($notification);
        $this->em->merge($relationship);
        $this->em->flush();

        return $this->redirectToRoute('notification', [
            'user' => $this->getUser()->getEmail(),
        ]);
    }
}
