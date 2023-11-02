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
    <meta name="google-site-verification" content="mvWEs7Jh-CtqK_PbYN0z5zwfdo3UAY8RMyWu_Z9t5Xc" />
    <title>Ajajaj, chyba!  - Farnost Brno - Královo Pole</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
   
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

    <?= $this->Html->css('web.css') ?>
    <?= $this->Html->css('landing.css') ?>
    <?= $this->Html->css('cookie.css') ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-EC7W1MV9GV"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-EC7W1MV9GV');
    </script>
</head>
<body>
    <header>
            <nav class="mb-1 navbar navbar-expand-xl fixed-top navbar-light">
            <span><a href="/"><img src="/img/logo.svg" style="max-height: 2rem;"></a><a href="/" class="px-3">Farnost Brno - Královo Pole</a></span>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMobile"
                aria-controls="navbarMobile" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMobile">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item px-2">
                        <a href="/" class="nav-link">Aktuality</a>
                    </li>
                    <li class="nav-item px-2">
                        <a href="/informace-z-farnosti/" class="nav-link">Informace z farnosti</a>
                    </li>
                    <li class="nav-item px-2">
                        <a href="/farnost-a-aktivity/" class="nav-link">Farnost a její aktivity</a>
                    </li>
                    <li class="nav-item px-2">
                        <a href="/farni-spolecenstvi/" class="nav-link">Farní společenství</a>
                    </li>
                    <li class="nav-item px-2">
                        <a href="/login/" class="nav-link">Interní sekce webu</a>
                    </li>
                </ul>
            </div>
        </nav>
        <div class="row mr-0 mt-5" style="height: 85%">
        <?= $this->fetch('content') ?>
        </div>
    </header>
</body>
</html>
