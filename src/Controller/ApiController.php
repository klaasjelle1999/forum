<?php

namespace App\Controller;

use App\Entity\Message;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApiController extends FOSRestController
{
    /**
     * @Route("/api/messages/all")
     */
    public function findAllMessages()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = [$normalizer];

        $serializer = new Serializer($normalizers, $encoders);

        $messages = $this->getDoctrine()->getRepository(Message::class)->findAll();
        return new Response($serializer->serialize($messages, 'json'));
    }

    /**
     * @Route(path="/api/messages/{id}")
     * @param int $id
     * @return Response
     */
    public function findMessageById(int $id)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizer = new ObjectNormalizer();
        $normalizer->setCircularReferenceLimit(1);
        $normalizer->setCircularReferenceHandler(function ($object) {
            return $object->getId();
        });
        $normalizers = [$normalizer];

        $serializer = new Serializer($normalizers, $encoders);

        $message = $this->getDoctrine()->getRepository(Message::class)->find($id);
        return new Response($serializer->serialize($message, 'json'));
    }
}
