<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

?>
<!--<div style="margin-top: 6rem;"></div>-->
<div class="b-navbar">
        <?php
    $navbar = [
        [
            "name" => "Home",
            "href" => "/",
            "iconPath" => "/assets/icons/home.svg",
        ],
        [
            "name" => "Registro",
            "href" => "/registr",
            "iconPath" => "/assets/icons/registro.svg",
        ],
        [
            "name" => "Voti",
            "href" => "/grades",
            "iconPath" => "/assets/icons/voti.svg",
        ],
        [
            "name" => "Oggi",
            "href" => "/today",
            "iconPath" => "/assets/icons/oggi.svg",
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
        ?>
        <div class="nav-icon" style="">
            <img src="<?=$navbarIconPath?>" alt="<?=$navbarName?>">
            <a  href="<?=$navbarLink?>" ><?=$navbarName?></a>
        </div>
        <?php
    }
    ?>
</div>
