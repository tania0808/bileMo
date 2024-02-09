<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ClientController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClientRepository $clientRepository,
        private UserPasswordHasherInterface $passwordHasher,
        private SerializerInterface $serializer
    )
    {
    }

    #[Route('/api/register', name: 'app_client_register', methods: ['POST'])]
    public function register(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $data = $this->serializer->deserialize($request->getContent(), Client::class, 'json');

        $email = $data->getEmail();
        $password = $data->getPassword();

        $client_exists = $this->clientRepository->findOneByEmail($email);
        if ($client_exists) {
            return new JsonResponse([
                'message' => 'Client already exists'
            ], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return new JsonResponse($this->serializer->serialize($errors, 'json'), Response::HTTP_BAD_REQUEST, [], true);
        }
        $client = new Client();
        $client->setEmail($email);
        $client->setLibelle($data->getLibelle());
        $client->setAddress($data->getAddress());
        $client->setPassword(
            $this->passwordHasher->hashPassword($client, $password)
        );

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Client created successfully!'
        ], Response::HTTP_OK);
    }
}
