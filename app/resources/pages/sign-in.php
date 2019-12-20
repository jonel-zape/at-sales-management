<div id="main-wrapper">
    <div class="navbar navbar-inverse" role="navigation">
        <div class="navbar-header">
            <div class="logo"><h1>Sign In</h1></div>
        </div>
    </div>
    <div class="template-page-wrapper">
        <div class="form-horizontal templatemo-signin-form" role="form">
            <div class="form-group">
                <div class="col-md-12">
                  <div class="templatemo-alerts" id="alert-container">
                  </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <label for="username" class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="username" placeholder="Username">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <label for="password" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type="password" class="form-control" id="password" placeholder="Password">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12">
                    <div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Sign in" class="btn btn-default" onclick="auth.login()">
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="/js/modules/auth.js"></script>