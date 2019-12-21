let auth = {

    login()
    {
        let that = this;

        http.post(
            '/guest/authenticate',
            {
                username: el.val("#username"),
                password: el.val("#password")
            }
        ).done(function(response){
            that.alertSuccess();
        }).catch(function(response){
            that.alertError(response.errors[0]);
        });
    },

    alertSuccess()
    {
        let element = '';

        element += '<div class="alert alert-success alert-dismissible" role="alert">';
        element += '    <button type="button" class="close" data-dismiss="alert">';
        element += '        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>';
        element += '    </button>';
        element += '    <strong>Success!</strong>';
        element += '</div>';

        $("#alert-container").empty();
        $("#alert-container").append(element);
    },

    alertError(message)
    {
        let element = '';

        element += '<div class="alert alert-danger alert-dismissible" role="alert">';
        element += '    <button type="button" class="close" data-dismiss="alert">';
        element += '        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>';
        element += '    </button>';
        element += message;
        element += '</div>';

        $("#alert-container").empty();
        $("#alert-container").append(element);
    }
};
