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
use OpenApi\Attributes as OA;

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

    /**
     * Register a new client
     */
    #[OA\Response(
        response: 200,
        description: 'Client created successfully !'
    )]
    #[OA\Response(
        response: 400,
        description: 'Client already exists !'
    )]
    #[OA\Post(
        path: '/api/register',
        tags: ['Client'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', default: 'tania08082000@gmail.com'),
                new OA\Property(property: 'password', type: 'string', default: 'admin123'),
                new OA\Property(property: 'libelle', type: 'string', default: 'Verger de la colline SARL'),
                new OA\Property(property: 'address', type: 'string', default: 'Saudoy 51120'),
            ]
        )
    )]
    #[Route('/api/register', name: 'app_client_register', methods: ['POST'])]
    public function register(Request $request, ValidatorInterface $validator): JsonResponse
    {
        $client = $this->serializer->deserialize($request->getContent(), Client::class, 'json');

        $client_exists = $this->clientRepository->findOneByEmail($client->getEmail());

        if (null !== $client_exists) {
            return new JsonResponse([
                'message' => 'Client already exists !'
            ], Response::HTTP_BAD_REQUEST);
        }

        $errors = $validator->validate($client);

        if (count($errors) > 0) {
            return new JsonResponse(
                $this->serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST
            );
        }

        $client->setPassword(
            $this->passwordHasher->hashPassword($client, $client->getPassword())
        );

        $this->entityManager->persist($client);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Client created successfully !'
        ], Response::HTTP_OK);
    }
}
