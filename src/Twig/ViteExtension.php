<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ViteExtension extends AbstractExtension
{
    private ?array $manifest = null;

    public function __construct(private readonly string $projectDir) {}

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_asset', $this->viteAsset(...)),
        ];
    }

    public function viteAsset(string $entry): string
    {
        if ($this->manifest === null) {
            $manifestPath = $this->projectDir . '/public/build/.vite/manifest.json';
            if (!file_exists($manifestPath)) {
                return '';
            }
            $this->manifest = json_decode(file_get_contents($manifestPath), true);
        }

        $asset = $this->manifest[$entry] ?? null;
        if (!$asset) return '';

        $tags = '';
        if (isset($asset['css'])) {
            foreach ($asset['css'] as $css) {
                $tags .= '<link rel="stylesheet" href="/build/' . $css . '">' . "\n";
            }
        }
        $tags .= '<script type="module" src="/build/' . $asset['file'] . '"></script>' . "\n";

        return $tags;
    }
}
