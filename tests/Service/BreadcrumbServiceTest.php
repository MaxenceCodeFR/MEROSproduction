<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\BreadcrumbService;

class BreadcrumbServiceTest extends TestCase
{
private $breadcrumbService;

protected function setUp(): void
{
$this->breadcrumbService = new BreadcrumbService();
}

    public function testAddBreadcrumbs()
    {
        // Ajouter un seul fil d'Ariane et vérifier
        $this->breadcrumbService->add('Accueil', '/home');
        $breadcrumbs = $this->breadcrumbService->getBreadcrumbs();

        $this->assertCount(1, $breadcrumbs);
        $this->assertEquals('Accueil', $breadcrumbs[0]['title']);
        $this->assertEquals('/home', $breadcrumbs[0]['url']);

        // Ajouter un autre fil d'Ariane et vérifier
        $this->breadcrumbService->add('Blog', '/blog');
        $breadcrumbs = $this->breadcrumbService->getBreadcrumbs();

        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals('Blog', $breadcrumbs[1]['title']);
        $this->assertEquals('/blog', $breadcrumbs[1]['url']);
    }

    public function testGetBreadcrumbs()
    {
        // Tester la récupération initiale sans ajouts
        $breadcrumbs = $this->breadcrumbService->getBreadcrumbs();
        $this->assertIsArray($breadcrumbs);
        $this->assertEmpty($breadcrumbs);

        // Tester après ajout
        $this->breadcrumbService->add('Accueil', '/home');
        $this->breadcrumbService->add('Blog', '/blog');

        $breadcrumbs = $this->breadcrumbService->getBreadcrumbs();
        $this->assertCount(2, $breadcrumbs);
        $this->assertIsArray($breadcrumbs[0]);
        $this->assertEquals(['title' => 'Accueil', 'url' => '/home'], $breadcrumbs[0]);
        $this->assertEquals(['title' => 'Blog', 'url' => '/blog'], $breadcrumbs[1]);
    }
}

