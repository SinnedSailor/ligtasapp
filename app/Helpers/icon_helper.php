<?php
if (!function_exists('svg_icon')) {
    /**
     * Return inline SVG for a named icon (keeps markup in PHP helper so view() issues are avoided)
     * Usage: <?= svg_icon('plus', 'w-4 h-4 text-indigo-600') ?>
     */
    function svg_icon(string $name, string $class = ''): string
    {
        // `esc()` is a CI helper; if it's not loaded (e.g. when running via CLI unit
        // tests or scripts) fall back to a simple htmlspecialchars wrapper.
        if (!function_exists('esc')) {
            function esc($str) {
                return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
            }
        }

        $cls = $class ? 'class="' . esc($class) . '"' : 'class="w-5 h-5 inline-block"';

        // Prefer official Heroicons (installed via npm). Only keep mappings for
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
            'x'            => 'x-mark',
            'x-circle'     => 'x-circle',
            'logout'       => 'arrow-right-on-rectangle',
            // backup/restore page icon
            'archive'      => 'archive-box',
        ];

        $heroName = $heroMap[$name] ?? null;
        if ($heroName && defined('ROOTPATH')) {
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

        // Some deployments (e.g. CI or environments without npm) may not have
        // the heroicons package installed, which causes the lookup above to
        // fail and return an empty <svg>.  To avoid blank buttons we provide a
        // tiny inline fallback for the handful of icons used throughout the
        // application.
        $fallbacks = [
            'pencil' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>',
            'users'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-3-3.87"/><path d="M7 21v-2a4 4 0 0 1 3-3.87"/><circle cx="12" cy="7" r="4"/><path d="M5 7a4 4 0 0 1 4-4"/><path d="M19 7a4 4 0 0 0-4-4"/></svg>',
            'trash'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>',
            'file'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>',
            'files'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21H9a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h5l5 5v11a2 2 0 0 1-2 2z"/><path d="M14 2v6h6"/></svg>',
            'check'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
            'x'      => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>',
            'home'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2V12H9v8a2 2 0 0 1-2 2H3z"/></svg>',
            'search' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
            'alert'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12" y2="17"/></svg>',
            'shield' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l7 4v6c0 5-3.6 9.74-7 11-3.4-1.26-7-6-7-11V6l7-4z"/><path d="M12 11v4"/><line x1="12" y1="17" x2="12" y2="17"/></svg>',
            'logout' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>',
            'menu'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
            'cloud-upload' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 16l-4-4-4 4"/><path d="M12 12v9"/><path d="M20 16.58A5 5 0 0 0 16 9h-1.26A8 8 0 1 0 4 16.29"/></svg>',
            'plus'  => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>',
        ];

        if (isset($fallbacks[$name])) {
            // inject requested class into fallback svg and return it
            $svg = $fallbacks[$name];
            if (preg_match('/<svg([^>]*)>/', $svg, $m)) {
                $origAttrs = $m[1];
                $origAttrs = preg_replace('/\sclass="[^"]*"/', '', $origAttrs);
                $svg = preg_replace('/<svg([^>]*)>/', '<svg ' . trim($origAttrs) . ' ' . $cls . '>', $svg, 1);
            } else {
                $svg = preg_replace('/<svg/', '<svg ' . $cls, $svg, 1);
            }
            return $svg;
        }

        // If we still don't have an icon, fall back to an empty placeholder
        return "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' aria-hidden='true'></svg>";
    }
}
