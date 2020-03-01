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

let number = {
    money(value) {
        return value.toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
}

$(document).ready(function() {
    loading.hide();
});