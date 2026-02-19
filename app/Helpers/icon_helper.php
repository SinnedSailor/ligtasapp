<?php
if (!function_exists('svg_icon')) {
    /**
     * Return inline SVG for a named icon (keeps markup in PHP helper so view() issues are avoided)
     * Usage: <?= svg_icon('plus', 'w-4 h-4 text-indigo-600') ?>
     */
    function svg_icon(string $name, string $class = ''): string
    {
        $cls = $class ? 'class="' . esc($class) . '"' : 'class="w-5 h-5 inline-block"';

        // Prefer official Heroicons (installed via npm). Only keep mappings for
        // icons that are actually used in the codebase — unused fallbacks removed.
        $heroMap = [
            'plus'         => 'plus',
            'cloud-upload' => 'cloud-arrow-up',
            'chart'        => 'chart-bar',
            'alert'        => 'exclamation-triangle',
            'map-pin'      => 'map-pin',
            'users'        => 'users',
            'home'         => 'home',
            'search'       => 'magnifying-glass',
            'pencil'       => 'pencil',
            'files'        => 'document',
            'file'         => 'document-text',
            'trash'        => 'trash',
            'shield'       => 'shield-check',
            'check'        => 'check',
            // logout icon (Heroicons)
            'logout'       => 'arrow-right-on-rectangle',
        ];

        $heroName = $heroMap[$name] ?? null;
        if ($heroName) {
            $paths = [
                ROOTPATH . 'node_modules/heroicons/20/outline/' . $heroName . '.svg',
                ROOTPATH . 'node_modules/heroicons/24/outline/' . $heroName . '.svg',
                ROOTPATH . 'node_modules/heroicons/20/solid/' . $heroName . '.svg',
            ];

            foreach ($paths as $p) {
                if (file_exists($p)) {
                    $svg = file_get_contents($p);
                    // inject/replace class attribute on the <svg> element
                    if (preg_match('/<svg([^>]*)>/', $svg, $m)) {
                        $origAttrs = $m[1];
                        $origAttrs = preg_replace('/\sclass="[^"]*"/', '', $origAttrs);
                        $svg = preg_replace('/<svg([^>]*)>/', '<svg ' . trim($origAttrs) . ' ' . $cls . '>', $svg, 1);
                    } else {
                        $svg = preg_replace('/<svg/', '<svg ' . $cls, $svg, 1);
                    }

                    return $svg;
                }
            }
        }

        // No inline fallback SVGs retained — rely on Heroicons mapping above.
        // If a matching Heroicons SVG cannot be found, return an empty placeholder.
        return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' aria-hidden='true'></svg>";
    }
}
