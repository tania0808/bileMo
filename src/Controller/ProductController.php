<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class ProductController extends AbstractController
{
    public function __construct(private ProductRepository $productRepository)
    {
    }

    #[Route('/api/products', name: 'app_products', methods: ['GET'])]
    public function index(Request $request, SerializerInterface $serializer, TagAwareCacheInterface $tagAwareCache): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);

        $cacheId = 'productsList-' . $page . '-' . $limit;

        $productsList = $tagAwareCache->get($cacheId, function (ItemInterface $item) use ($page, $limit) {
            echo 'not cached';
           $item->tag('productsCache');
           return $this->productRepository->getAllWithPagination($page, $limit);
        });

        $jsonProductsList = $serializer->serialize($productsList, 'json');

        return new JsonResponse($jsonProductsList, Response::HTTP_OK, [], true);
    }

    #[Route('/api/products/{id}', name: 'app_product', methods: ['GET'])]
    public function show(Product $product): JsonResponse
    {
        return $this->json($product);
    }

}
