<?php
// Usage: <?= view('components/icon', ['name' => 'upload', 'class' => 'w-5 h-5 text-indigo-600']) ?>
$name = $name ?? '';
$cls  = isset($class) ? 'class="' . esc($class) . '"' : 'class="w-5 h-5 inline-block"';
switch ($name) {
    case 'upload':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1M7 10l5-5 5 5M12 5v12'/></svg>";
        break;
    case 'plus':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z' clip-rule='evenodd'/></svg>";
        break;
    case 'save':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M17 3H5a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V5a2 2 0 00-2-2zM9 12a3 3 0 116 0 3 3 0 01-6 0z'/></svg>";
        break;
    case 'chart':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 3v18M4 6v12M18 10v8'/></svg>";
        break;
    case 'alert':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M8.257 3.099c.765-1.36 2.72-1.36 3.485 0l5.516 9.808c.75 1.336-.213 2.993-1.742 2.993H4.483c-1.53 0-2.492-1.657-1.742-2.993L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-3a1 1 0 01-1-1V7a1 1 0 112 0v2a1 1 0 01-1 1z' clip-rule='evenodd'/></svg>";
        break;
    case 'map-pin':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' aria-hidden='true'><path d='M12 2a6 6 0 00-6 6c0 5.25 6 13 6 13s6-7.75 6-13a6 6 0 00-6-6zM12 11a2 2 0 110-4 2 2 0 010 4z'/></svg>";
        break;
    case 'users':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M13 7a3 3 0 11-6 0 3 3 0 016 0z'/><path fill-rule='evenodd' d='M5 14a5 5 0 1110 0v1H5v-1z' clip-rule='evenodd'/></svg>";
        break;
    case 'camera':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M4 5a2 2 0 00-2 2v7a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-3.172a2 2 0 01-1.414-.586l-.828-.828A2 2 0 008.172 3H6a2 2 0 00-2 2z'/><path d='M10 8a4 4 0 100 8 4 4 0 000-8z'/></svg>";
        break;
    case 'check':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path fill-rule='evenodd' d='M16.707 5.293a1 1 0 00-1.414 0L8 12.586 4.707 9.293a1 1 0 10-1.414 1.414l4 4a1 1 0 001.414 0l8-8a1 1 0 000-1.414z' clip-rule='evenodd'/></svg>";
        break;
    case 'power':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 3v9m4.24 6.24A9 9 0 1111.76 3.76'/></svg>";
        break;
    case 'pencil':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M11 5h6M4 7v10a2 2 0 002 2h10'/></svg>";
        break;
    case 'shield':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z'/></svg>";
        break;
    case 'heart':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='currentColor' aria-hidden='true'><path d='M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.657l-6.828-7.828a4 4 0 010-5.657z'/></svg>";
        break;
    case 'menu':
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='currentColor' aria-hidden='true'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 6h16M4 12h16M4 18h16'/></svg>";
        break;
    default:
        // fallback: empty spacer
        echo "<svg $cls xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' aria-hidden='true'></svg>";
        break;
}
