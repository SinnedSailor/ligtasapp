<?php
if (!function_exists('svg_icon')) {
    /**
     * Return inline SVG for a named icon (keeps markup in PHP helper so view() issues are avoided)
     * Usage: <?= svg_icon('plus', 'w-4 h-4 text-indigo-600') ?>
     */
    function svg_icon(string $name, string $class = ''): string
    {
        $cls = $class ? 'class="' . esc($class) . '"' : 'class="w-5 h-5 inline-block"';

        // Prefer official Heroicons (installed via npm). If the SVG file exists in
        // node_modules/heroicons we'll load and return it (injecting the CSS class).
        // Otherwise fall back to the inline SVGs implemented below.
        $heroMap = [
            'upload' => 'arrow-up-on-square',
            'plus' => 'plus',
            'chart' => 'chart-bar',
            'alert' => 'exclamation-triangle',
            'map-pin' => 'map-pin',
            'users' => 'users',
            'pencil' => 'pencil',
            'search' => 'magnifying-glass',
            'home' => 'home',
            'cloud-upload' => 'cloud-arrow-up',
            'files' => 'document',
            'file' => 'document-text',
            'trash' => 'trash',
            'check' => 'check',
            'power' => 'power',
            'shield' => 'shield-check',
            'heart' => 'heart',
            'menu' => 'bars-3',
            'eye' => 'eye',
            'camera' => 'camera',
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

        switch ($name) {
            case 'upload':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5-5 5 5M12 5v12'/></svg>";
            case 'plus':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z' clip-rule='evenodd'/></svg>";
            case 'chart':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 3v18M4 6v12M18 10v8'/></svg>";
            case 'alert':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.516 9.808c.75 1.336-.213 2.993-1.742 2.993H4.483c-1.53 0-2.492-1.657-1.742-2.993L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-3a1 1 0 01-1-1V7a1 1 0 112 0v2a1 1 0 01-1 1z' clip-rule='evenodd'/></svg>";
            case 'map-pin':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' aria-hidden='true'><path d='M12 2a6 6 0 00-6 6c0 5.25 6 13 6 13s6-7.75 6-13a6 6 0 00-6-6zM12 11a2 2 0 110-4 2 2 0 010 4z'/></svg>";
            case 'users':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M13 7a3 3 0 11-6 0 3 3 0 016 0z'/><path fill-rule='evenodd' d='M5 14a5 5 0 1110 0v1H5v-1z' clip-rule='evenodd'/></svg>";
            case 'pencil':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5h6M4 7v10a2 2 0 002 2h10'/></svg>";
            case 'search':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M12.9 14.32a8 8 0 111.414-1.414l4.387 4.387a1 1 0 01-1.414 1.414l-4.387-4.387zM8 14a6 6 0 100-12 6 6 0 000 12z' clip-rule='evenodd'/></svg>";
            case 'home':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M10.707 1.293a1 1 0 00-1.414 0L2 8.586V17a1 1 0 001 1h5v-5a1 1 0 011-1h2a1 1 0 011 1v5h5a1 1 0 001-1V8.586l-7.293-7.293z'/></svg>";
            case 'cloud-upload':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' aria-hidden='true'><path d='M16.88 7.47A5 5 0 007 8.1a4 4 0 00-.5 7.97h10.38A3.62 3.62 0 0021 12.47 3.5 3.5 0 0016.88 7.47z'/><path d='M11 11V3h2v8h3l-4 4-4-4h3z'/></svg>";
            case 'files':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M4 3a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0013.586 6L10 2.414A2 2 0 008.586 2H4z'/></svg>";
            case 'file':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M8 2a2 2 0 00-2 2v12a2 2 0 002 2h6a2 2 0 002-2V7.414A2 2 0 0014.586 6L11 2.414A2 2 0 009.586 2H8z'/></svg>";
            case 'trash':
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M6 2a1 1 0 00-1 1v1H3a1 1 0 100 2h14a1 1 0 100-2h-2V3a1 1 0 00-1-1H6zm3 7a1 1 0 012 0v5a1 1 0 11-2 0V9zm-4 0a1 1 0 012 0v5a1 1 0 11-2 0V9zm8 0a1 1 0 012 0v5a1 1 0 11-2 0V9z' clip-rule='evenodd'/></svg>";
            default:
                return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' aria-hidden='true'></svg>";
        }
    }
}
