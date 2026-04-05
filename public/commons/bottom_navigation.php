<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Tutte le sezioni dell'app
$allSections = [
    [
        "name" => "Home",
        "href" => "/",
        "iconPath" => "/assets/icons/home.svg",
    ],
    [
        "name" => "Assenze",
        "href" => "/absences",
        "iconPath" => "/assets/icons/assenze.svg",
    ],
    [
        "name" => "Voti",
        "href" => "/grades",
        "iconPath" => "/assets/icons/voti.svg",
    ],
    [
        "name" => "Oggi",
        "href" => "/agenda",
        "iconPath" => "/assets/icons/oggi.svg",
    ],
    [
        "name" => "Bacheca",
        "href" => "/noticeboard",
        "iconPath" => "/assets/icons/bacheca.svg",
    ],
    [
        "name" => "Profilo",
        "href" => "/account",
        "iconPath" => "/assets/icons/account.svg",
    ],
];

// Mobile: 3 icone principali + "Altro"
$mobileMain = [0, 2, 3]; // Home, Voti, Oggi
$mobileMore = [1, 4, 5]; // Assenze, Bacheca, Profilo
?>

<?php
// Icone con currentColor (nuove — riscritte per il tema dinamico)
$currentColorIcons = ['voti', 'account', 'more'];

// SVG inline per le icone — permette currentColor nel CSS per il tema
function svgIcon(string $name, array $currentColorIcons): string {
    $path = __DIR__ . '/../assets/icons/' . $name . '.svg';
    if (!file_exists($path)) return '';
    $svg = file_get_contents($path);
    if (in_array($name, $currentColorIcons, true)) {
        // Rimuove stroke hardcodato, usa currentColor
        $svg = preg_replace('/stroke\s*=\s*["\']#[^"\']+["\']/i', 'stroke="currentColor"', $svg);
    }
    return $svg;
}
?>

<div style="margin-top: 6rem;"></div>
<div class="b-navbar">
    <!-- Desktop: tutte le icone visibili -->
    <div class="nav-desktop">
        <?php foreach ($allSections as $i => $navbarItem): ?>
            <?php
            $isActive = ($currentPath == $navbarItem["href"]);
            $iconName = basename($navbarItem["iconPath"], '.svg');
            ?>
            <div class="nav-icon <?= $isActive ? 'active' : '' ?>">
                <?= svgIcon($iconName, $currentColorIcons) ?>
                <a href="<?= $navbarItem['href'] ?>"><?= $navbarItem['name'] ?></a>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Mobile: 3 icone + Altro -->
    <div class="nav-mobile">
        <?php foreach ($mobileMain as $idx):
            $item = $allSections[$idx];
            $isActive = ($currentPath == $item["href"]);
            $iconName = basename($item["iconPath"], '.svg');
        ?>
            <div class="nav-icon <?= $isActive ? 'active' : '' ?>" data-href="<?= $item['href'] ?>">
                <?= svgIcon($iconName, $currentColorIcons) ?>
                <a href="<?= $item['href'] ?>"><?= $item['name'] ?></a>
            </div>
        <?php endforeach; ?>
        <!-- Icona "Altro" -->
        <div class="nav-icon" id="more-toggle" data-href="#">
            <?= svgIcon('more', $currentColorIcons) ?>
        </div>
    </div>
</div>

<!-- Drawer "Altro" (overlay) -->
<div class="more-overlay" id="more-overlay">
    <div class="more-drawer">
        <div class="more-header">
            <span>Tutte le sezioni</span>
            <button class="more-close" id="more-close">&times;</button>
        </div>
        <?php foreach ($allSections as $item):
            $isActive = ($currentPath == $item["href"]);
        ?>
            <a href="<?= $item['href'] ?>" class="more-item <?= $isActive ? 'active' : '' ?>">
                <img src="<?= $item['iconPath'] ?>" alt="<?= $item['name'] ?>" onerror="this.style.display='none'">
                <span><?= $item['name'] ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<script>
    // Mobile nav icons click
    document.querySelectorAll('.nav-mobile .nav-icon, .nav-desktop .nav-icon').forEach(item => {
        item.addEventListener('click', () => {
            const href = item.querySelector('a')?.href || item.dataset.href;
            if (href && href !== '#') {
                window.location.href = href;
            }
        });
    });

    // Drawer toggle
    const moreToggle = document.getElementById('more-toggle');
    const moreOverlay = document.getElementById('more-overlay');
    const moreClose = document.getElementById('more-close');

    if (moreToggle && moreOverlay) {
        moreToggle.addEventListener('click', (e) => {
            e.preventDefault();
            moreOverlay.classList.add('open');
            document.body.classList.add('drawer-open');
        });
    }
    if (moreClose && moreOverlay) {
        moreClose.addEventListener('click', () => {
            moreOverlay.classList.remove('open');
            document.body.classList.remove('drawer-open');
        });
    }
    if (moreOverlay) {
        moreOverlay.addEventListener('click', (e) => {
            if (e.target === moreOverlay) {
                moreOverlay.classList.remove('open');
                document.body.classList.remove('drawer-open');
            }
        });
    }
</script>
