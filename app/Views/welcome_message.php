<?= $this->extend('layouts/main_tailwind') ?>

<?= $this->section('pageStyles') ?>
<style>
    * {
        transition: background-color 300ms ease, color 300ms ease;
    }

    *:focus {
        background-color: rgba(221, 72, 20, 0.2);
        outline: none;
    }

    .ci-welcome {
        color: rgba(33, 37, 41, 1);
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji";
        font-size: 16px;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        text-rendering: optimizeLegibility;
    }

    .ci-welcome header {
        background-color: rgba(247, 248, 249, 1);
        padding: 0.4rem 0 0;
    }

    .ci-welcome .menu {
        padding: 0.4rem 2rem;
    }

    .ci-welcome header ul {
        border-bottom: 1px solid rgba(242, 242, 242, 1);
        list-style-type: none;
        margin: 0;
        overflow: hidden;
        padding: 0;
        text-align: right;
    }

    .ci-welcome header li {
        display: inline-block;
    }

    .ci-welcome header li a {
        border-radius: 5px;
        color: rgba(0, 0, 0, 0.5);
        display: block;
        height: 44px;
        text-decoration: none;
    }

    .ci-welcome header li.menu-item a {
        border-radius: 5px;
        margin: 5px 0;
        height: 38px;
        line-height: 36px;
        padding: 0.4rem 0.65rem;
        text-align: center;
    }

    .ci-welcome header li.menu-item a:hover,
    .ci-welcome header li.menu-item a:focus {
        background-color: rgba(221, 72, 20, 0.2);
        color: rgba(221, 72, 20, 1);
    }

    .ci-welcome header .logo {
        float: left;
        height: 44px;
        padding: 0.4rem 0.5rem;
    }

    .ci-welcome header .menu-toggle {
        display: none;
        float: right;
        font-size: 2rem;
        font-weight: bold;
    }

    .ci-welcome header .menu-toggle button {
        background-color: rgba(221, 72, 20, 0.6);
        border: none;
        border-radius: 3px;
        color: rgba(255, 255, 255, 1);
        cursor: pointer;
        font: inherit;
        font-size: 1.3rem;
        height: 36px;
        padding: 0;
        margin: 11px 0;
        overflow: visible;
        width: 40px;
    }

    .ci-welcome header .menu-toggle button:hover,
    .ci-welcome header .menu-toggle button:focus {
        background-color: rgba(221, 72, 20, 0.8);
        color: rgba(255, 255, 255, 0.8);
    }

    .ci-welcome header .heroe {
        margin: 0 auto;
        max-width: 1100px;
        padding: 1rem 1.75rem 1.75rem 1.75rem;
    }

    .ci-welcome header .heroe h1 {
        font-size: 2.5rem;
        font-weight: 500;
    }

    .ci-welcome header .heroe h2 {
        font-size: 1.5rem;
        font-weight: 300;
    }

    .ci-welcome section {
        margin: 0 auto;
        max-width: 1100px;
        padding: 2.5rem 1.75rem 3.5rem 1.75rem;
    }

    .ci-welcome section h1 {
        margin-bottom: 2.5rem;
    }

    .ci-welcome section h2 {
        font-size: 120%;
        line-height: 2.5rem;
        padding-top: 1.5rem;
    }

    .ci-welcome section pre {
        background-color: rgba(247, 248, 249, 1);
        border: 1px solid rgba(242, 242, 242, 1);
        display: block;
        font-size: 0.9rem;
        margin: 2rem 0;
        padding: 1rem 1.5rem;
        white-space: pre-wrap;
        word-break: break-all;
    }

    .ci-welcome section code {
        display: block;
    }

    .ci-welcome section a {
        color: rgba(221, 72, 20, 1);
    }

    .ci-welcome section svg {
        margin-bottom: -5px;
        margin-right: 5px;
        width: 25px;
    }

    .ci-welcome .further {
        background-color: rgba(247, 248, 249, 1);
        border-bottom: 1px solid rgba(242, 242, 242, 1);
        border-top: 1px solid rgba(242, 242, 242, 1);
    }

    .ci-welcome .further h2:first-of-type {
        padding-top: 0;
    }

    .ci-welcome .svg-stroke {
        fill: none;
        stroke: #000;
        stroke-width: 32px;
    }

    @media (max-width: 629px) {
        .ci-welcome header ul {
            padding: 0;
        }

        .ci-welcome header .menu-toggle {
            padding: 0 1rem;
            display: block;
        }

        .ci-welcome header .menu-item {
            background-color: rgba(244, 245, 246, 1);
            border-top: 1px solid rgba(242, 242, 242, 1);
            margin: 0 15px;
            width: calc(100% - 30px);
        }

        .ci-welcome header .hidden {
            display: none;
        }

        .ci-welcome header li.menu-item a {
            background-color: rgba(221, 72, 20, 0.1);
        }

        .ci-welcome header li.menu-item a:hover,
        .ci-welcome header li.menu-item a:focus {
            background-color: rgba(221, 72, 20, 0.7);
            color: rgba(255, 255, 255, 0.8);
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ci-welcome">
    <header>
        <div class="menu">
            <ul>
                <li class="logo">
                    <a href="https://codeigniter.com" target="_blank" rel="noopener">
                        <svg role="img" aria-label="Visit CodeIgniter.com official website!" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 2100 500" height="44"><path fill="#dd4814" d="M148.2 411c-20.53-9.07-34.48-28.61-36.31-50.99 1.2-23.02 13.36-44.06 32.67-56.61-3.17 7.73-2.4 16.53 2 23.6 5.01 7 13.63 10.36 22.07 8.61 12.02-3.38 19.06-15.86 15.68-27.89-1.2-4.21-3.6-8.03-6.88-10.91-13.6-11.06-20.43-28.44-18-45.81 2.33-9.2 7.42-17.52 14.61-23.8-5.4 14.4 9.83 28.61 20.05 35.6 18.14 10.88 35.6 22.84 52.32 35.81 18.27 14.4 28.23 36.94 26.67 60-4.11 24.54-21.47 44.8-45.13 52.4 47.33-10.53 96.13-48.13 97.06-101.46-.93-42.67-26.4-80.96-65.33-98.4h-1.73c.86 2.09 1.28 4.34 1.2 6.61.13-1.47.13-2.93 0-4.4.21 1.73.21 3.47 0 5.2-2.96 12.13-15.2 19.6-27.36 16.64-4.86-1.2-9.2-3.93-12.32-7.87-15.6-20 0-42.76 2.61-64.76 1.6-28.13-11.25-55.02-34.05-71.46 11.41 19.02-3.79 44-14.84 58.21-11.07 14.21-27.07 24.8-40.11 37.2-14.05 13.07-26.93 27.44-38.49 42.8-24.99 30.53-34.8 70.8-26.67 109.4 11.15 37.2 42.07 65.15 80.2 72.4h.21l-.13-.12Z"/></svg>
                    </a>
                </li>
                <li class="menu-toggle">
                    <button id="menuToggle">&#9776;</button>
                </li>
                <li class="menu-item hidden"><a href="#">Home</a></li>
                <li class="menu-item hidden"><a href="https://codeigniter.com/user_guide/" target="_blank" rel="noopener">Docs</a></li>
                <li class="menu-item hidden"><a href="https://forum.codeigniter.com/" target="_blank" rel="noopener">Community</a></li>
                <li class="menu-item hidden"><a href="https://codeigniter.com/contribute" target="_blank" rel="noopener">Contribute</a></li>
            </ul>
        </div>
    </header>

    
<?= $this->endSection() ?>

<?= $this->section('pageScripts') ?>
<script>
    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', toggleMenu);
    }

    function toggleMenu() {
        const menuItems = document.getElementsByClassName('menu-item');
        for (let i = 0; i < menuItems.length; i++) {
            menuItems[i].classList.toggle('hidden');
        }
    }
</script>
<?= $this->endSection() ?>
