<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Sales Management</title>
        <meta name="keywords" content="" />
        <meta name="description" content="" />
        <meta name="viewport" content="width=device-width">
        <link rel="stylesheet" href="/css/templatemo_main.css">
        <link rel="stylesheet" href="/css/custom.css">
    </head>
    <body>

    <?php component('loading.php'); ?>

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

        <div class="template-page-wrapper">
        <div class="navbar-collapse collapse templatemo-sidebar">
            <ul class="templatemo-sidebar-menu">
                <li class="active"><a href="#"><i class="fa fa-home"></i>Home</a></li>
                <li><a href="/productlist"><i class="fa fa-cubes"></i></span>Product Page</a></li>
                <li><a href="/purchaselist"></span>Purchase Page</a></li>
                <li><a href="/saleslist"></span>Sales Page</a></li>
                <li><a href="/paymentlist"></i>Payment Page</a></li>
                <li><a href="javascript:;" data-toggle="modal" data-target="#confirmModal"><i class="fa fa-sign-out"></i>Sign Out</a></li>
            </ul>
        </div><!--/.navbar-collapse -->

      <!-- Modal -->
        <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" id="myModalLabel">Are you sure you want to sign out?</h4>
                    </div>
                <div class="modal-footer">
                    <a href="/sign-in" class="btn btn-primary">Yes</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
                </div>
                </div>
            </div>
        </div>

        </div>

    <?php } ?>