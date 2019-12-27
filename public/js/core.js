let core = {
    logout() {

        modalConfirm.show({
            message: 'Are you sure you want to sign out?',
            confirmYes: function(){
                loading.show();
                window.location = '/auth/logout';
            }
        });
    }
};