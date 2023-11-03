<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @var \App\View\AppView $this
 */

?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Varhany pro Královo Pole</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">
    
    <link rel="icon" href="/favicon.ico">
    <meta name="theme-color" content="#3153b3">
    <link rel="apple-touch-icon" href="/logo192.png">
    <link rel="manifest" href="/manifest.json">
  

    <?= $this->Html->css('web.css') ?>

</head>
<body>
    <a href="#" id="return-to-top"><i class="fa fa-chevron-up"></i></a>
    <div class="root"><div class="app">
    <header class="header">
        <div class="header__content container">
            <a aria-current="page" class="header__logo-container active" href="/">
                <img class="header__logo" src="/img/logo.svg" alt="logo píšťal varhan">
                <div class="header__title-container">
                    <h3 class="header__title">VARHANY</h3>
                    <h4 class="header__subtitle">pro Královo Pole</h4>
                </div>
            </a>
            <nav class="header__nav">
                <ul class="header__nav-items ">
                    <li><a class="header__nav-item<?= ($active == "orders"?" header__nav-item--active\" aria-current=\"page":"") ?>" href="#/admin/objednavky">Objednávky</a></li>
                    <li><a class="header__nav-item<?= ($active == "pipes"?" header__nav-item--active\" aria-current=\"page":"") ?>" href="/admin/pistaly">Píšťaly</a></li>
                    <li><a class="header__nav-item<?= ($active == "data"?" header__nav-item--active\" aria-current=\"page":"") ?>" href="/admin/data">Data</a></li>
                    <li><a class="header__nav-item" href="/logout">Odhlásit se</a></li>
                    <li class="header__nav-item header__close-button">Zavřít</li>
                </ul>
            </nav>
            <div class="header__menu-icon">
                <svg class="MuiSvgIcon-root MuiSvgIcon-fontSizeLarge" focusable="false" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"></path></svg>
            </div>
        </div>
    </header>
    <?= $this->fetch('content') ?>
    </div></div>
</body>
</html>
