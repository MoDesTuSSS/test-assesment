<?php

namespace App\Controller;

use App\Entity\News;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/news', name: 'api_news_')]
class NewsController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NewsRepository $newsRepository,
        private ValidatorInterface $validator
    ) {
    }

    #[Route('', name: 'list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/news',
        summary: 'Get all news articles',
        tags: ['News'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of news articles',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(ref: '#/components/schemas/News')
                )
            )
        ]
    )]
    public function list(): JsonResponse
    {
        $newsList = $this->newsRepository->findAllOrdered();
        
        $data = array_map(fn(News $news) => $news->toArray(), $newsList);
        
        return $this->json($data);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[OA\Get(
        path: '/api/news/{id}',
        summary: 'Get a specific news article',
        tags: ['News'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'News article details',
                content: new OA\JsonContent(ref: '#/components/schemas/News')
            ),
            new OA\Response(response: 404, description: 'News not found')
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $news = $this->newsRepository->find($id);
        
        if (!$news) {
            return $this->json(
                ['error' => 'News not found'],
                Response::HTTP_NOT_FOUND
            );
        }
        
        return $this->json($news->toArray());
    }

    #[Route('', name: 'create', methods: ['POST'])]
    #[OA\Post(
        path: '/api/news',
        summary: 'Create a new news article',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateNews')
        ),
        tags: ['News'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'News article created',
                content: new OA\JsonContent(ref: '#/components/schemas/News')
            ),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $news = new News();
        $news->setTitle($data['title'] ?? '');
        $news->setBody($data['body'] ?? '');
        $news->setImage($data['image'] ?? null);

        $errors = $this->validator->validate($news);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->persist($news);
        $this->entityManager->flush();

        return $this->json(
            $news->toArray(),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    #[OA\Put(
        path: '/api/news/{id}',
        summary: 'Update a news article (full replacement)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateNews')
        ),
        tags: ['News'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'News article updated',
                content: new OA\JsonContent(ref: '#/components/schemas/News')
            ),
            new OA\Response(response: 404, description: 'News not found'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    #[OA\Patch(
        path: '/api/news/{id}',
        summary: 'Update a news article (partial update)',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/UpdateNews')
        ),
        tags: ['News'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'News article updated',
                content: new OA\JsonContent(ref: '#/components/schemas/News')
            ),
            new OA\Response(response: 404, description: 'News not found'),
            new OA\Response(response: 400, description: 'Validation error')
        ]
    )]
    public function update(int $id, Request $request): JsonResponse
    {
        $news = $this->newsRepository->find($id);
        
        if (!$news) {
            return $this->json(
                ['error' => 'News not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $data = json_decode($request->getContent(), true);
        
        if (!$data) {
            return $this->json(
                ['error' => 'Invalid JSON'],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (isset($data['title'])) {
            $news->setTitle($data['title']);
        }
        
        if (isset($data['body'])) {
            $news->setBody($data['body']);
        }
        
        if (isset($data['image'])) {
            $news->setImage($data['image']);
        }

        $errors = $this->validator->validate($news);
        
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            
            return $this->json(
                ['errors' => $errorMessages],
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->entityManager->flush();

        return $this->json($news->toArray());
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        path: '/api/news/{id}',
        summary: 'Delete a news article',
        tags: ['News'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'News article deleted'
            ),
            new OA\Response(response: 404, description: 'News not found')
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $news = $this->newsRepository->find($id);
        
        if (!$news) {
            return $this->json(
                ['error' => 'News not found'],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->entityManager->remove($news);
        $this->entityManager->flush();

        return $this->json(
            ['message' => 'News deleted successfully'],
            Response::HTTP_OK
        );
    }
}

