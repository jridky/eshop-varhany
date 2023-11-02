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
    <title>Farnost Brno - Královo Pole</title>

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
    <script src="/js/matchHeight.js"></script>
    <link rel="stylesheet" href="/js/fancybox/jquery.fancybox.css">
    <script src="/js/fancybox/jquery.fancybox.pack.js"></script>
    <script src="/js/fancybox/jquery.mousewheel-3.0.6.pack.js"></script>

    <meta property="og:title" content="Farnost Brno - Královo Pole" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://farnost-krpole.cz" />
    <meta property="og:image" content="https://farnost-krpole.cz/img/logo-social.png" />
    <meta property="twitter:image" content="https://farnost-krpole.cz/img/logo-social.png" />

    <?= $this->Html->css('web.css') ?>
    <?= $this->Html->css('landing.css') ?>
    <?= $this->Html->css('cookie.css') ?>
    <?= $this->Html->css('backtop.css') ?>
    <?= $this->Html->css('print.css', array("media"=>"print")) ?>

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
    <a href="#" id="return-to-top"><i class="fa fa-chevron-up"></i></a>
    <header class="masthead">
            <nav class="mb-1 navbar navbar-expand-xl fixed-top navbar-light">
            <span class="nav-brand"><a href="/"><img src="/img/logo.svg"></a><a href="/" class="px-3">Farnost Brno - Královo Pole</a></span>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarMobile"
                aria-controls="navbarMobile" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMobile">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item px-2">
                        <a href="#aktuality" class="nav-link local">Aktuality</a>
                    </li>
                    <li class="nav-item dropdown px-2">
                        <a href="#" class="nav-link dropdown-toggle" id="zivotDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Informace z farnosti <i class="fa fa-caret-down"></i></a>
                        <div class="dropdown-menu" aria-labelledby="zivotDropdown">
                        <?php foreach($menuDropdown['zivot'] as $md){ ?>
                            <a class="dropdown-item" href="/<?= $md['sekce'] ?>/"><?= $md['nazev'] ?></a>
                        <?php } ?>
                        </div>
                    </li>
                    <li class="nav-item dropdown px-2">
                        <a href="#" class="nav-link dropdown-toggle" id="farnostDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Farnost a její aktivity <i class="fa fa-caret-down"></i></a>
                        <div class="dropdown-menu" aria-labelledby="farnostDropdown">
                        <?php foreach($menuDropdown['farnost'] as $md){ ?>
                            <a class="dropdown-item" href="/<?= $md['sekce'] ?>/"><?= $md['nazev'] ?></a>
                        <?php } ?>
                        </div>
                    </li>
                    <li class="nav-item dropdown px-2">
                        <a href="#" class="nav-link dropdown-toggle" id="spolecenstviDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Farní společenství <i class="fa fa-caret-down"></i></a>
                        <div class="dropdown-menu" aria-labelledby="spolecenstviDropdown">
                        <?php foreach($menuDropdown['spolecenstvi'] as $md){ ?>
                            <a class="dropdown-item" href="/<?= $md['sekce'] ?>/"><?= $md['nazev'] ?></a>
                        <?php } ?>
                        </div>
                    </li>
                    <li class="nav-item px-2">
                        <a href="<?= $internalLink ?>" class="nav-link">Interní sekce webu</a>
                    </li>
                    <?php if($internalLink != "/login/"){?>
                    <li class="nav-item px-2">
                        <a href="/logout/" class="nav-link">Odhlásit se</a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </nav>
        <div class="row mr-0 mt-5" style="height: 85%">
            <div class="col-lg-1 border-right text-center pr-0">
                <a href="https://www.facebook.com/FarnostKrpole" target="_blank" title="Facebook"><img src="/img/facebook.svg" alt="facebook" class="social-icon"></a>
                <a href="https://www.youtube.com/channel/UCU1msf2PkEwtcKKU6PNU7bw" target="_blank" title="YouTube"><img src="/img/youtube.svg" alt="youtube" class="social-icon"></a>
                <a href="mailto:cella.trinitatis@gmail.com" target="_blank" title="Mail"><img src="/img/mail.svg" alt="mail" class="social-icon"></a>
            </div>

            <div class="col-lg-10 pt-lg-5 h-100 px-0">
                <div style="padding: 5% 0 5% 0">
                    <img src="/img/logo.svg" alt="Logo farnosti" class="logo">
                    <div class="heading-text h1">
                        Římskokatolická farnost u&nbsp;kostela<br>
                        Nejsvětější Trojice, Brno - Královo Pole
                    </div>
                </div>
            </div>
        </div>
        <?php if($flashCount > 0){  ?>
        <div class="row mr-xl-0 mt-5">
             <div class="col-lg-8 h-100 px-0 mx-auto">
                <div class="row mx-auto">
                    <?= $this->Flash->render(); ?>
                </div>
             </div>
        </div>
        <?php } ?>
    </header>
    <?= $this->fetch('content') ?>
    <section class="col-12 px-0" style="height: 550px">
        <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d651.3995172082089!2d16.595522980619638!3d49.22714920668773!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x8e4a326beb4cc56e!2zRmFybsOtIMO6xZlhZCBmYXJub3N0aSB1IGtvc3RlbGEgTmVqc3bEm3TEm2rFocOtIFRyb2ppY2U!5e0!3m2!1scs!2scz!4v1589141318266!5m2!1scs!2scz" width="100%" height="550" frameborder="0" style="border:0;" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
    </section>
    <footer class="background-gray">
        <div class="row pt-2 pb-3 w-100 mx-auto">
            <div class="mx-auto col-lg-11">
                <div class="row px-5">
                    <div class="col-lg-9 py-4">
                        <?php foreach ($loga as $l){ ?>
                            <a href="<?= $l['adresa'] ?>" target="_blank"><img class="support-logo pr-2 py-3" src="/img/banner/<?= $l['cesta'] ?>"></a>
                        <?php } ?>
                    </div>
                    <div class="col-lg-3 py-4"> 
                       <div class="h5">Kontakt farnosti</div>
                        <p>Metodějova 13/2a<br>
                           Brno - Královo Pole, 612 00<br>
                           Telefon: 733 741 202<br>
                           č.ú. 178331229/0300<br>
                           IČ: 64327680</p>
                           <a href="https://www.facebook.com/FarnostKrpole" target="_blank" title="Facebook"><img src="/img/facebook.svg" alt="facebook" class="social-icon d-inline-block my-0"></a>
                           <a href="https://www.youtube.com/channel/UCU1msf2PkEwtcKKU6PNU7bw" target="_blank" title="YouTube"><img src="/img/youtube.svg" alt="youtube" class="social-icon d-inline-block my-0"></a>
                           <a href="mailto:cella.trinitatis@gmail.com" target="_blank" title="Mail"><img src="/img/mail.svg" alt="mail" class="social-icon d-inline-block my-0"></a>                          
                    </div>
                </div>
            </div>
        </div>
        <div class="row px-5 py-3 border-top w-100 mx-auto">
            <div class="col-lg-11 mx-auto copyright">
                Copyright <?= date("Y", time()); ?>, Farnost Brno - Královo Pole | <a href="/web/">Informace o webu</a>
            </div>
        </div>
    </footer>
    <div id="cookie">
        <div id="cookie-wrapper">
            <div id="cookie-text">
             Tyto webové stránky používají soubory cookies. Podrobnosti o tom, jak soubory cookies používáme a jak je můžete zakázat naleznete na stránce věnované <a href="/cookies" target="_blank">politice souborů cookies</a>.
             Návštěvou tohoto webu souhlasíte s jejich používáním.
            </div>
            <div id="cookie-close">&times;</div>
        </div>  
    </div>
    <script>
        var cookie = document.cookie.split('cwa=');
        if(1 < cookie.length){
            $("#cookie").remove();
        }

        $("#cookie-close").click(function(){
            var d = new Date();
            d.setTime(d.getTime() + (364*24*60*60*1000));
            var expires = "expires=" + d.toGMTString();
            document.cookie = "cwa=accept;" + expires + ";path=/";
            $("#cookie").fadeOut("slow", function (){
                $("#cookie").remove();
            });
        });
        $(document).ready(function(){
            $(".alert").matchHeight();
            $(".nav-link.local, .local-nav a").click(function(event){
                if(this.hash !== ""){
                    event.preventDefault();
                    var hash = this.hash;
                    $("html,body").animate({
                        scrollTop: $(hash).offset().top - $('.navbar.fixed-top').outerHeight()
                    }, 800, function(){
                        history.pushState(null, null, hash);
                    });
                }
            }); 
            $(".fancybox").fancybox({
                nextEffect: "fade",
                prevEffect: "fade",
                padding: 2,
                titlePosition: "outside"
            });
            $(window).scroll(function() {
                if ($(this).scrollTop() >= (window.innerHeight*0.15)) {
                    $('#return-to-top').fadeIn(500);
                    $('.masthead .navbar.fixed-top').addClass('background-yellow');
                    $('.masthead .navbar.fixed-top .nav-brand a').show();
                } else {
                    $('#return-to-top').fadeOut(300);
                    $('.masthead .navbar.fixed-top').removeClass('background-yellow');
                    $('.masthead .navbar.fixed-top .nav-brand a').hide();
                }
            }); 
            $('#return-to-top, .home').click(function() {
                $('body,html').animate({
                    scrollTop : 0    
                }, 500);
            }); 
        });
    </script>

</body>
</html>
