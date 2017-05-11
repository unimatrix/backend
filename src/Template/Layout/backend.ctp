<?php

// assets that make this template come alive
$project = [
    // styles
    'css' => [
        'Unimatrix/Backend.fonts/awesome.min.css',
        'Unimatrix/Backend.backend/foundation.min.css',
        'Unimatrix/Backend.backend.css'
    ],

    // javascript
    'js' => [
        'Unimatrix/Cake.jquery/jquery.min.js',
        'Unimatrix/Backend.backend/foundation.min.js',
        'Unimatrix/Backend.backend.js'
    ]
];

?>
<!DOCTYPE html>
<html>
<head><?php
    // charset
    echo $this->Html->charset();

    // title
    $title = str_replace('Backend', 'Admin', $this->fetch('title'));
    echo "<title>{$title}</title>";

    // icon and meta
    echo $this->Html->meta('icon');
    echo $this->fetch('meta');

    // viewport & theme
    echo $this->Html->meta('viewport', 'width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no');
    echo $this->Html->meta('theme-color', '#000000');

    // SEO
    echo $this->Html->meta('keywords', 'Admin, Administration, Backend, Control, Panel');
    echo $this->Html->meta('description', 'Administration - Backend Control Panel');
    echo $this->Html->meta(['rel' => 'canonical', 'link' => $this->Url->build(null, true)]);

    // css
    $this->Minify->style($project['css']);
    $this->Minify->fetch('style');
?></head>
<body>
    <?= $this->Flash->render() ?>
    <section class="container clearfix">
        <nav class="top-bar">
            <div class="top-bar-title large-3 medium-4 columns">
                <ul class="menu">
                    <li><?= $this->Html->link('<i class="fa fa-home" aria-hidden="true"></i>Administration', is_null($auth) ? ['controller' => 'Login', 'action' => 'index', 'plugin' => 'Unimatrix/Backend'] : ['controller' => 'Dashboard', 'action' => 'index'], ['escape' => false]) ?></li>
                </ul>
            </div>
            <div class="clearfix">
                <div class="top-bar-right">
                    <ul class="menu">
                        <?php if(is_null($auth)) { ?>
                            <li><?= $this->Html->link('<i class="fa fa-sign-in" aria-hidden="true"></i>Please login to continue', ['controller' => 'Login', 'action' => 'index', 'plugin' => 'Unimatrix/Backend'], ['escape' => false]) ?></li>
                        <?php } else { ?>
                            <li><?= $this->Html->link('<i class="fa fa-sign-out" aria-hidden="true"></i>Logout', ['controller' => 'Login', 'action' => 'logout', 'plugin' => 'Unimatrix/Backend'], ['escape' => false]) ?></li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </nav>
        <?= $this->fetch('content') ?>
    </section>
    <footer>
        <div class="large-3 medium-4 columns">Generated in: <?= $this->Number->precision($this->Debug->requestTime() * 1000, 0) ?> ms</div>
        <div class="large-9 medium-8 columns text-center">&copy; 2016-<?= date('Y') ?> <?php echo $this->Html->link('Unimatrix Venture Digital Platform System', 'http://venture.unimatrix.ro') ?></div>
    </footer>
    <?php
        // js
        $this->Minify->script($project['js']);
        $this->Minify->fetch('script');
    ?>
</body>
</html>
