<?php


namespace App\Service;

class BreadcrumbService
{
    private $breadcrumbs = [];

    public function add(string $title, string $url): void
    {
        $this->breadcrumbs[] = ['title' => $title, 'url' => $url];
    }

    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }
}
