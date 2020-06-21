<?php if (!isAuthenticated()) { ?>
    <style>
        .templatemo-content {
            margin: 0px;
            height: 100%;
        }
    </style>
<?php } ?>

<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <div class="alert alert-danger alert-dismissible" role="alert">
            404 Page not found.
        </div>
        <img src="/images/no.gif<?php noCache(); ?>" height="300" />
    </div>
</div>

