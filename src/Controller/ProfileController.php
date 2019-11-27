<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\ProfileComment;
use App\Entity\Relationship;
use App\Entity\User;
use App\Form\EditPasswordFormType;
use App\Form\EditUserFormType;
use App\Form\ProfileCommentFormType;
use App\Repository\RelationshipRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ProfileController extends AbstractController
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/profile/{user}", name="profile")
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"user": "email"}})
     * @param User $user
     * @return Response
     * @throws \Exception
     */
    public function index(User $user)
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

        if (isset($_POST['submit']) && !empty($_POST['profileComment'])) {
            $data = $_POST['profileComment'];
            $profileComment = new ProfileComment();

            $profileComment
                ->setContent($data)
                ->setUser($user)
                ->setAuthor($this->getUser())
                ->setCreatedAt(new \DateTime('now'));

            $this->em->persist($profileComment);
            $this->em->flush();
        }

        $friendRequest = $this
            ->getDoctrine()
            ->getManager()
            ->getRepository(Relationship::class)
            ->findFriend($this->getUser(), $user)
        ;

//        $friendRequest = $this
//            ->getDoctrine()
//            ->getManager()
//            ->getRepository(Notification::class)
//            ->findFriendRequest($user)
//        ;

        $profileComments = $this->getDoctrine()
            ->getManager()
            ->getRepository(ProfileComment::class)
            ->findAllProfileCommentsForUser($user)
        ;

        return $this->render('profile/index.html.twig', [
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
            'controller_name' => 'ProfileController',
            'user' => $user,
            'profileComments' => $profileComments,
            'friendRequest' => $friendRequest,
        ]);
    }

    /**
     * @Route(path="/profile/{user}/edit", name="edit_user")
     * @ParamConverter("user", class="App\Entity\User", options={"mapping": {"user": "email"}})
     * @param User $user
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function editUser(User $user, Request $request, UserPasswordEncoderInterface $passwordEncoder)
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

        $passForm = $this->createForm(EditPasswordFormType::class);
        $passForm->handleRequest($request);
        $form = $this->createForm(EditUserFormType::class);
        $form->handleRequest($request);

        if ($passForm->isSubmitted() && $passForm->isValid()) {
            $data = $passForm->getData();
            if (password_verify($data->password, $user->getPassword())) {
                $password = $passwordEncoder->encodePassword($user, $data->new_password);
                $user->setPassword($password);

                $this->em->merge($user);
                $this->em->flush();
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $birthday = new \DateTime($data['birthday']);
            $extraInformation = $user->getExtraInformationUser();
            $extraInformation
                ->setBirthday($birthday)
                ->setFullname($data['fullname'])
                ->setBio($data['bio'])
                ->setSocialUrl($data['socialUrl']);
            $user->setEmail($data['email']);

            $this->em->merge($extraInformation);
            $this->em->merge($user);
            $this->em->flush();
        }

        return $this->render('profile/edit.html.twig', [
            'notifications' => $notifications,
            'nonReadNotifications' => $nonReadNotifications,
            'user' => $user,
            'passForm' => $passForm->createView(),
            'form' => $form->createView(),
        ]);
    }
}
