<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li class="active">Settings</li>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <?php component('alert.php') ?>
            </div>
        </div>
        <h1>User Account</h1>
        <p>Changes Username or Password</p>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <label for="new_username" class="control-label">New Username</label>
                <input type="text" class="form-control no-margin" id="new_username" value="">
            </div>
            <div class="col-md-3 margin-bottom-15">
                <label for="new_password" class="control-label">New Password</label>
                <input type="password" class="form-control no-margin" id="new_password" value="">
            </div>
            <div class="col-md-3 margin-bottom-15">
                <label for="repeat_password" class="control-label">Repeat Password</label>
                <input type="password" class="form-control no-margin" id="repeat_password" value="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <label for="current_password" class="control-label">Current Password</label>
                <input type="password" class="form-control no-margin" id="current_password" value="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-15">
                <button type="reset" class="btn btn-default" onclick="settings.updateUsername()">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    Update Username
                </button>
                <button type="reset" class="btn btn-default" onclick="settings.updatePassword()">
                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                    Update Password
                </button>
            </div>
        </div>
        <br>
        <p>Login Attempts</p>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <label class="control-label color-red" id="login_attempt_count">You have 0 login attempt(s)</label>
                <button type="reset" class="btn btn-default" data-toggle="modal" data-target="#login-failed-logs-modal" onclick="settings.getLoginAttempt()">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                    View Logs
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <label for="password_reset_login_attempts" class="control-label">Password</label>
                <input type="password" class="form-control no-margin" id="password_reset_login_attempts" value="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-15">
                <button type="reset" class="btn btn-default" onclick="settings.clearLoginAttempts()">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                    Clear Logs
                </button>
            </div>
        </div>
        <h1>System Data</h1>
        <p>Clear Data</p>
        <p class="help-block">Clear all data except user accounts.</p>
        <p class="help-block">* This feature will remove after user acceptance testing</p>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <label for="password_clear_data" class="control-label">Password</label>
                <input type="password" class="form-control no-margin" id="password_clear_data" value="">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-15">
                <button type="reset" class="btn btn-default color-red" onclick="settings.clearData()">
                    <i class="fa fa-warning" aria-hidden="true"></i>
                    Clear Data
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-15">
                <hr/>
                <p class="help-block">Sales Managment Version <?php printSystemVersion(); ?></p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="login-failed-logs-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Login Attempts</h4>
                <div class="row">
                    <div class="col-md-12 margin-bottom-15">
                        <?php component('alert.php', ['id' => 'alertModal']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-10">
                        <?php component('dataTable.php', ['id' => 'failedLoginTable']); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/settings.js<?php noCache(); ?>"></script>