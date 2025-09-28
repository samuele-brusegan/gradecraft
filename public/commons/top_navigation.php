<?php
/*
 * Copyright (c) 2025. Brusegan Samuele, Davanzo Andrea
 * Questo file fa parte di GradeCraft ed è rilasciato
 * sotto la licenza MIT. Vedere il file LICENSE per i dettagli.
 */

//global $navSelected;
if (!isset($navSelected)) { $navSelected = ""; }
?>

<div style="overflow: auto; display: flex;" id="t-navbar">
    <div id="t-navbar_1" <?=($navSelected=="t-navbar_1")?"data-selected":''?> data-href="/grades"> materie </div>
    <div id="t-navbar_2" <?=($navSelected=="t-navbar_2")?"data-selected":''?> data-href="/grades/period?id=1"> 1° quadrimestre </div>
    <div id="t-navbar_3" <?=($navSelected=="t-navbar_3")?"data-selected":''?> data-href="/grades/period?id=3"> 2° quadrimestre </div>
</div>
<script>
    let tNavbarBnts = document.querySelectorAll("#t-navbar>div");

    refresh_tNavbar();

    function refresh_tNavbar() {
        tNavbarBnts.forEach(btn => {
            if (btn.hasAttribute("data-selected")) {
                btn.style.backgroundColor = "#4170D7";
                btn.style.color = "white";
            } else {
                btn.style.backgroundColor = "";
                btn.style.color = "";
            }
        })
    }

    tNavbarBnts.forEach(btn => {
        btn.addEventListener("click", () => {
            if (btn.hasAttribute("data-selected")) {
                refresh_tNavbar();
            } else {
                window.location.href = btn.getAttribute("data-href");

                tNavbarBnts.forEach(btn => {
                    btn.removeAttribute("data-selected");
                })
                btn.setAttribute("data-selected", "");
                refresh_tNavbar();
            }
        })
    })
</script>