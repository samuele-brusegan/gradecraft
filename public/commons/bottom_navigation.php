<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<div style="margin-top: 6rem;"></div>
<div class="b-navbar">
        <?php
    $navbar = [
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
//            "href" => "/today",
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
            "iconPath" => "https://brusegan.it/assets/placeholder.svg",
        ]

    ];
    foreach ($navbar as $navbarItem) {
        $navbarName = $navbarItem["name"];
        $navbarLink = $navbarItem["href"];
        $navbarIconPath = $navbarItem["iconPath"];
        $isActive = ($currentPath == $navbarLink);
        ?>
        <div class="nav-icon <?= $isActive ? 'active' : '' ?>" style="">
            <img src="<?=$navbarIconPath?>" alt="<?=$navbarName?>">
            <a  href="<?=$navbarLink?>" ><?=$navbarName?></a>
        </div>
        <?php
    }
    ?>
    <script>
        let navIcons = document.querySelectorAll(".nav-icon");
        navIcons.forEach (item => {
            let href = item.querySelector('a[href]').href;
            item.addEventListener('click', () => {
                window.location.href = href;
            })
        })
    </script>
</div>
