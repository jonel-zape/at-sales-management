<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Sales Management</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="/css/templatemo_main.css">
        <link rel="stylesheet" href="/js/lib/tabulator/css/tabulator.css">
        <link rel="stylesheet" href="/js/lib/autocomplete/styles.css">
        <link rel="stylesheet" href="/js/lib/datepicker/datepicker.css">

        <link rel="stylesheet" href="/css/custom.css">
    </head>
    <body>

    <script src="/js/lib/jquery-3.4.1.min.js"></script>
    <script src="/js/lib/autocomplete/jquery.autocomplete.min.js"></script>
    <script src="/js/lib/tabulator/js/tabulator.js"></script>
    <script src="/js/template/bootstrap.min.js"></script>
    <script src="/js/template/Chart.min.js"></script>
    <script src="/js/template/templatemo_script.js"></script>
    <script src="/js/lib/datepicker/datepicker.js"></script>
    <script src="/js/core.js"></script>
    <script src="/js/http.js"></script>
    <script src="/js/el.js"></script>

    <?php component('loading.php'); ?>
    <?php component('modalConfirm.php'); ?>

    <?php if (isAuthenticated()) { ?>

        <div class="navbar navbar-inverse" role="navigation">
          <div class="navbar-header">
            <div class="logo"><h1>Sales Management</h1></div>
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          </div>
        </div>

        <?php
            $parentModule = getSegment(1);
            $controlClass = [
                'home'     => '',
                'product'  => '',
                'supplier' => '',
                'customer' => '',
                'purchase' => '',
                'sales'    => '',
                'payment'  => ''
            ];

            if (isset($controlClass[$parentModule])) {
                $controlClass[$parentModule] = 'active';
            }
        ?>

        <div class="template-page-wrapper">
            <div class="navbar-collapse collapse templatemo-sidebar">
                <ul class="templatemo-sidebar-menu">
                    <li class="<?php echo $controlClass['home'] ?>">
                        <a href="/home">
                            <i class="fa fa-home"></i>
                            Home
                        </a>
                    </li>
                    <li class="<?php echo $controlClass['product'] ?>">
                        <a href="/product"><i class="fa fa-cubes"></i>Product</a>
                    </li>
                    <li class="<?php echo $controlClass['purchase'] ?>">
                        <a href="/purchase">
                            <i class="fa fa-sitemap"></i>
                            Purchase
                        </a>
                    </li>
                    <li class="<?php echo $controlClass['sales'] ?>">
                        <a href="/sales">
                            <i class="fa fa-shopping-cart"></i>
                            Sales
                        </a>
                    </li>
                    <li class="<?php echo $controlClass['payment'] ?>">
                        <a href="/payment">
                            <i class="fa fa-money"></i>
                            Payment
                        </a>
                    </li>
                    <li style="cursor: pointer;">
                        <a onclick="core.logout();"><i class="fa fa-sign-out"></i>Sign Out</a>
                    </li>
                </ul>
            </div>
        </div>

    <?php } ?>
