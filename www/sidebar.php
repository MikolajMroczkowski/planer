<?php

function renderNav($active)
{
    $nav = [["index.php", "Terminy zajęć", "event", false], ["events.php", "Zajęcia", "assignment", false], ["myUsers.php", "Lista uczęszczających", "people", false],
        ["myMoney.php", "Moje przychody", "payments", false],
        ["billing.php", "Rozliczenia", "credit_card", false], ["logout.php", "Wyloguj się", "logout", false]
    ];
    $nav[$active][3] = true;
    $elList = "";
    foreach ($nav as $item) {
        $elList .= navElement($item[0], $item[1], $item[2], $item[3]);
    }
    echo '<nav class="sidebar">' . $elList . '</nav>';
}

function navElement($url, $name, $icon, $isActive)
{
    $active = '';
    if ($isActive) {
        $active = 'active';
    }
    $el = '<a href="' . $url . '" class="' . $active . '"><i class="material-icons">' . $icon . '</i><p>' . $name . '</p></a><br>';
    return $el;
}