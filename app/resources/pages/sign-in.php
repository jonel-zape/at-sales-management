<div id="main-wrapper">
   
    <div class="template-page-wrapper">
        <div class="form-horizontal templatemo-signin-form" role="form">
            <div class="form-group panel panel-default">
                <div class="panel-heading">
                    <h4>Sign In</h4>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12 margin-bottom-15">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-1">
                        </div>
                        <div class="col-md-10">
                            <?php component('alert.php') ?>
                        </div>
                        <div class="col-md-1"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="username" placeholder="Username">
                        </div>
                        <div class="col-md-2"></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8">
                            <input type="password" class="form-control" id="password" placeholder="Password">
                        </div>
                        <div class="col-md-2">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-30">
                        <div class="col-md-2">
                        </div>
                        <div class="col-md-8" align="right">
                             <button type="button" class="btn btn-default" onclick="auth.login()">
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

<script src="/js/modules/auth.js"></script>