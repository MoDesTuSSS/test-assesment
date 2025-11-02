<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/api/news')]
class ImageUploadController extends AbstractController
{
    #[Route('/upload', name: 'api_news_upload', methods: ['POST'])]
    #[OA\Post(
        path: '/api/news/upload',
        summary: 'Upload an image for news article',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['image'],
                    properties: [
                        new OA\Property(
                            property: 'image',
                            type: 'string',
                            format: 'binary',
                            description: 'Image file (JPEG, PNG, GIF, WebP, max 5MB)'
                        )
                    ]
                )
            )
        ),
        tags: ['News'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Image uploaded successfully',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'imageUrl', type: 'string', example: '/uploads/image-abc123.jpg'),
                        new OA\Property(property: 'filename', type: 'string', example: 'image-abc123.jpg')
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'No image uploaded or invalid format')
        ]
    )]
    public function upload(Request $request, SluggerInterface $slugger): JsonResponse
    {
        /** @var UploadedFile $imageFile */
        $imageFile = $request->files->get('image');
        
        if (!$imageFile) {
            return $this->json(
                ['error' => 'No image file uploaded'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Validate file type
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($imageFile->getMimeType(), $allowedMimeTypes)) {
            return $this->json(
                ['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        // Validate file size (max 5MB)
        if ($imageFile->getSize() > 5 * 1024 * 1024) {
            return $this->json(
                ['error' => 'File too large. Maximum size is 5MB.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $extension = $imageFile->guessExtension();
        
        // Generate unique filename
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

        try {
            $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
            
            // Create uploads directory if it doesn't exist
            if (!is_dir($uploadsDirectory)) {
                mkdir($uploadsDirectory, 0755, true);
            }

            $imageFile->move($uploadsDirectory, $newFilename);
        } catch (FileException $e) {
            return $this->json(
                ['error' => 'Failed to upload image: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->json([
            'imageUrl' => '/uploads/' . $newFilename,
            'filename' => $newFilename
        ]);
    }
}

