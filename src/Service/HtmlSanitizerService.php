<?php
namespace App\Service;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class HtmlSanitizerService
{
    public function sanitize(string $html): string
    {
        $config = (new HtmlSanitizerConfig())
            ->allowElement('blockquote', ['class', 'style', 'data-instgrm-captioned', 'data-instgrm-permalink', 'data-instgrm-version'])
            ->allowElement('a', ['href', 'target', 'style'])
            ->allowElement('div', ['style'])
            ->allowElement('span', ['style'])
            ->allowElement('img', ['src', 'alt', 'title', 'width', 'height', 'class'])
            ->allowElement('script', ['src', 'async'])
            ->allowLinkHosts(['www.instagram.com'])
            ->allowMediaHosts(['www.instagram.com']);

    $sanitizer = new HtmlSanitizer($config);

    return $sanitizer->sanitize($html);
    }
}
