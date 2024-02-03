<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    #[Route('/api/clients/{id}/users', name: 'app_client_users', methods: ['GET'])]
    public function index(Request $request, Client $client, UserRepository $userRepository, SerializerInterface $serializer): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $users = $userRepository->findAllByClientIdWithPagination($client, $page, $limit);
        $users = $serializer->serialize($users, 'json', ['groups' => 'index']);

        return new JsonResponse($users, 200, [], true);
    }

    #[Route('/api/clients/{client}/users/{user}', name: 'app_client_user', methods: ['GET'])]
    public function getClientUser(Client $client, User $user, SerializerInterface $serializer): JsonResponse
    {
        $user = $serializer->serialize($user, 'json', ['groups' => 'index']);

        return new JsonResponse($user, 200, [], true);
    }

    #[Route('/api/clients/{id}/users', name: 'app_client_user_create', methods: ['POST'])]
    public function createClientUser(Client $client, Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setClient($client);
        $entityManager->persist($user);
        $entityManager->flush();

        $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'index']);
        $location = $this->generateUrl('app_client_user', ['client' => $client->getId(), 'user' => $user->getId()]);

        return new JsonResponse($jsonUser, Response::HTTP_CREATED, ['location' => $location], true);
    }

    #[Route('/api/clients/{client}/users/{user}', name: 'app_client_user_delete', methods: ['DELETE'])]
    public function deleteClientUser(Client $client, User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
