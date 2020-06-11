let settings = {
    delays: {
        sellModal: null
    },
    loginAtttemptColumns: [
        {
            formatter: "rownum",
            align    : "center",
            width    : 40
        },
        {
            title    : "Username Used",
            field    : "username_used",
            formatter: "plaintext",
        },
        {
            title    : "Failure",
            field    : "other_info",
            formatter: "plaintext",
        },
        {
            title    : "Date Time",
            field    : "date_time",
            formatter: "plaintext",
        }
    ],
    getLoginAttemptCount() {
        loading.show();
        http.get(
            '/settings/loginAttemptCount'
        ).done(function(response){
            el.setContent('#login_attempt_count', `You have ${response.values.login_attempt_count} login attempt(s)`)
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },
    getLoginAttempt() {
        loading.show();
        alertModal.dismiss();
        http.get(
            '/settings/loginAttempt'
        ).done(function(response){
            settings.delays.sellModal = setInterval(function() {
                alertModal.dismiss();
                failedLoginTable.setColumns(settings.loginAtttemptColumns);
                failedLoginTable.setData(response.values.data);
                failedLoginTable.show();
                clearInterval(settings.delays.sellModal);
                loading.hide();
            }, 500);
        }).catch(function(response){
            alertModal.error(response.errors);
            failedLoginTable.hide();
            loading.hide();
        });
    },
    clearLoginAttempts() {
        loading.show();
        alert.dismiss();
        http.post(
            '/settings/clearLoginAttemptCount',
            {
                password: el.val("#password_reset_login_attempts")
            }
        ).done(function(response){
            el.setContent('#login_attempt_count', `You have 0 login attempt(s)`);
            $("#password_reset_login_attempts").val('');
            alert.success(response.values.message);
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },
    clearData() {
        loading.show();
        alert.dismiss();
        http.post(
            '/settings/clearData',
            {
                password: el.val("#password_clear_data")
            }
        ).done(function(response){
            $("#password_clear_data").val('');
            alert.success(response.values.message);
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },
    updateUsername() {
        alert.dismiss();
        loading.show();
        http.post(
            '/settings/updateUsername',
            {
                new_username: el.val("#new_username"),
                password: el.val("#current_password")
            }
        ).done(function(response){
            alert.success(response.values[0]);
            $("#current_password").val('');
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
        });
    },
    updatePassword() {
        alert.dismiss();
        loading.show();
        http.post(
            '/settings/updatePassword',
            {
                new_username: el.val("#new_password"),
                repeat_password: el.val("#repeat_password"),
                password: el.val("#current_password")
            }
        ).done(function(response){
            alert.success(response.values[0]);
            $("#current_password").val('');
            $("#new_password").val('');
            $("#repeat_password").val('');
            $("#new_username").val('');
            loading.hide();
        }).catch(function(response){
            alert.error(response.errors);
            loading.hide();
            $("#new_username").val('');
        });
    }
}

settings.getLoginAttemptCount();

let clearPassword = setInterval(function(){
    $("#new_username").val('');
    $("#new_password").val('');
    clearInterval(clearPassword);
}, 1000);