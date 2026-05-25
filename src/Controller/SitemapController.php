<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap.xml', name: 'app_sitemap', defaults: ['_format' => 'xml'])]
    public function sitemap(): Response
    {
        $urls = [
            ['loc' => '/',            'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/feeling',     'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => '/need',        'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => '/block',       'priority' => '0.9', 'changefreq' => 'monthly'],
            ['loc' => '/subscription','priority' => '0.8', 'changefreq' => 'monthly'],
            ['loc' => '/faq',         'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/about',       'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/cgu',         'priority' => '0.2', 'changefreq' => 'yearly'],
            ['loc' => '/privacy-policy', 'priority' => '0.2', 'changefreq' => 'yearly'],
            ['loc' => '/cookies',     'priority' => '0.1', 'changefreq' => 'yearly'],
        ];

        $response = new Response(
            $this->renderView('sitemap.xml.twig', [
                'urls'    => $urls,
                'baseUrl' => 'https://selfly.fr',
            ]),
            200,
            ['Content-Type' => 'application/xml']
        );

        $response->setSharedMaxAge(86400);

        return $response;
    }
}
