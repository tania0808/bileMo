<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use JMS\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    /**
     * Retrieve the list of products.
     */
    #[OA\Response(
        response: 200,
        description: 'The list of products'
    )]
    #[OA\Get(
        path: '/api/products',
        tags: ['Product'],
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
    #[Route('/api/products', name: 'products_list', methods: ['GET'])]
    public function index(Request $request, SerializerInterface $serializer, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $cacheId = 'productsList-'.$page.'-'.$limit;

        $productsList = $tagAwareCache->get($cacheId, function (ItemInterface $item) use ($page, $limit) {
            $item->tag('productsCache');
            $item->expiresAfter(1);
            return $this->productRepository->getAllWithPagination($page, $limit);
        });

        $jsonProductsList = $serializer->serialize($productsList, 'json');

        return new JsonResponse($jsonProductsList, Response::HTTP_OK, [], true);
    }

    /**
     * Retrieve the details of a product.
     */
    #[OA\Response(
        response: 200,
        description: 'The product details'
    )]
    #[OA\Get(
        path: '/api/products/{id}',
        tags: ['Product'],
    )]
    #[OA\Parameter(
        name: 'id',
        description: 'The id of the product',
        in: 'path',
        required: true,
    )]
    #[Route('/api/products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product, SerializerInterface $serializer): JsonResponse
    {
        $jsonProduct = $serializer->serialize($product, 'json');

        return new JsonResponse($jsonProduct, Response::HTTP_OK, [], true);
    }
}
