<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class UserController extends AbstractController
{
    /**
     * Retrieve the list of users of a client.
     */
    #[OA\Get(
        path: '/api/clients/{id}/users',
        tags: ['User'],
    )]
    #[OA\Response(
        response: 200,
        description: 'The list of users of a client'
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The client id',
        in: 'path',
        required: true,
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'The page number',
        in: 'query',
        required: false,
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'The number of items per page',
        in: 'query',
        required: false,
    )]
    #[Route('/api/clients/{id}/users', name: 'client_users_list', methods: ['GET'])]
    public function index(
        Request $request,
        Client $client,
        UserRepository $userRepository,
        SerializerInterface $serializer,
        TagAwareCacheInterface $tagAwareCache
    ): JsonResponse {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $cacheId = 'usersList-'.$page.'-'.$limit;

        $jsonUsersList = $tagAwareCache->get($cacheId, function (ItemInterface $item) use ($page, $limit, $client, $userRepository, $serializer) {
            $item->tag('usersCache');
            $item->expiresAfter(60);
            $usersList = $userRepository->findAllByClientIdWithPagination($client, $page, $limit);
            $context = SerializationContext::create()->setGroups(['index']);

            return $serializer->serialize($usersList, 'json', $context);
        });

        return new JsonResponse($jsonUsersList, Response::HTTP_OK, [], true);
    }

    /**
     * Retrieve the details of a user of a client.
     */
    #[OA\Get(
        path: '/api/clients/{client}/users/{user}',
        tags: ['User'],
    )]
    #[OA\Parameter(
        name: 'client',
        description: 'The client id',
        in: 'path',
        required: true,
    )]
    #[OA\Parameter(
        name: 'user',
        description: 'The user id',
        in: 'path',
        required: true,
    )]
    #[OA\Response(
        response: 200,
        description: 'The user details'
    )]
    #[Route('/api/clients/{client}/users/{user}', name: 'client_user_detail', methods: ['GET'])]
    public function getClientUser(Client $client, User $user, SerializerInterface $serializer): JsonResponse
    {
        $context = SerializationContext::create()->setGroups(['index']);
        $user = $serializer->serialize($user, 'json', $context);

        return new JsonResponse($user, 200, [], true);
    }

    /**
     * Create a new user for a client.
     */
    #[OA\Post(
        path: '/api/clients/{id}/users',
        tags: ['User'],
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The client id',
        in: 'path',
        required: true,
    )]
    #[OA\Response(
        response: 201,
        description: 'User created successfully !'
    )]
    #[OA\Response(
        response: 400,
        description: 'User already exists'
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: 'email', type: 'string', default: 'tania08082000@gmail.com'),
                new OA\Property(property: 'first_name', type: 'string', default: 'Tania'),
                new OA\Property(property: 'last_name', type: 'string', default: 'His'),
            ]
        )
    )]
    #[Route('/api/clients/{id}/users', name: 'client_user_create', methods: ['POST'])]
    public function createClientUser(
        Client $client,
        Request $request,
        SerializerInterface $serializer,
        EntityManagerInterface $entityManager,
        TagAwareCacheInterface $tagAwareCache,
        UserRepository $userRepository,
        ValidatorInterface $validator
    ): JsonResponse {
        $user = $serializer->deserialize($request->getContent(), User::class, 'json');

        if ($userRepository->findOneBy(['email' => $user->getEmail()])) {
            return new JsonResponse(
                [
                'message' => 'User already exists'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            return new JsonResponse(
                $serializer->serialize($errors, 'json'),
                Response::HTTP_BAD_REQUEST,
                [],
                true
            );
        }

        $tagAwareCache->invalidateTags(['usersCache']);

        $user->setClient($client);
        $entityManager->persist($user);
        $entityManager->flush();

        $context = SerializationContext::create()->setGroups(['index']);
        $jsonUser = $serializer->serialize($user, 'json', $context);

        return new JsonResponse(
            $jsonUser,
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    /**
     * Delete a user of a client.
     */
    #[OA\Delete(
        path: '/api/clients/{client}/users/{user}',
        tags: ['User'],
    )]
    #[OA\Response(
        response: 204,
        description: 'User deleted successfully !'
    )]
    #[Route('/api/clients/{client}/users/{user}', name: 'client_user_delete', methods: ['DELETE'])]
    public function deleteClientUser(Client $client, User $user, EntityManagerInterface $entityManager, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $tagAwareCache->invalidateTags(['usersCache']);

        $entityManager->remove($user);
        $entityManager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
