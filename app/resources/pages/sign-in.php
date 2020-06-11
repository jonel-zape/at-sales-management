<style>
    body {
        background-image: url(/images/background.png) !important;
        background-repeat: no-repeat;
    }

    #custom-footer {
        position: absolute;
        width: 100%;
        text-align: right;
        padding-right: 30px;
        top: 100%;
        margin-top: -70px;
        color: #414141;
        vertical-align: middle;
    }
</style>

<div id="main-wrapper">
    <div class="template-page-wrapper">
        <div class="form-horizontal templatemo-signin-form" role="form">
            <div class="form-group panel panel-primary">
                <div class="panel-heading">
                    <h4>
                    <i class="fa fa-lock" aria-hidden="true"></i>
                        Sign In
                    </h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12 margin-bottom-15">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">
                            <?php component('alert.php') ?>
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="username" placeholder="Username">
                        </div>
                        <div class="col-md-4">
                            <input type="password" class="form-control" id="password" placeholder="Password">
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-30">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8" align="right">
                             <button type="button" class="btn btn-primary" onclick="auth.login()">
                                <i class="fa fa-sign-in" aria-hidden="true"></i>
                                Sign In
                            </button>
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="custom-footer">
    <h4>
        <img src="/images/logo.png" />
        &nbsp;
        Sales Management for Online Selling Business
    </h4>
</div>

<script src="/js/modules/auth.js"></script>