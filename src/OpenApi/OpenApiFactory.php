<?php
// src/OpenApi/OpenApiFactory.php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model;

final class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        // Personnaliser les informations générales
        $openApi->getInfo()
            ->setTitle('Concert Connect API')
            ->setDescription('API pour accéder aux artistes et événements de Concert Connect')
            ->setVersion('1.0.0')
            ->setContact(
                new Model\Contact(
                    'Équipe Concert Connect',
                    'https://concert-connect.example.com',
                    'contact@concert-connect.example.com'
                )
            );

        // Ajouter des tags pour mieux organiser la documentation
        $openApi = $openApi->withTags([
            new Model\Tag('Artist', 'Ressources liées aux artistes'),
            new Model\Tag('Event', 'Ressources liées aux événements'),
        ]);

        // Vous pouvez également ajouter des chemins personnalisés
        $pathItem = new Model\PathItem(
            ref: 'Custom endpoint',
            get: new Model\Operation(
                operationId: 'getFeaturedArtists',
                tags: ['Artist'],
                responses: [
                    '200' => [
                        'description' => 'Liste des artistes mis en avant',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'array',
                                    'items' => [
                                        '$ref' => '#/components/schemas/Artist',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                summary: 'Récupère les artistes mis en avant',
                description: 'Récupère une liste d\'artistes mis en avant sur la plateforme'
            )
        );
        $openApi->getPaths()->addPath('/api/custom/artists/featured', $pathItem);

        return $openApi;
    }
}
