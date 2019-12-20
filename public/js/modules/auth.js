let auth = {

    login()
    {
        this.alertError();
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

    alertError()
    {
        let element = '';

        element += '<div class="alert alert-danger alert-dismissible" role="alert">';
        element += '    <button type="button" class="close" data-dismiss="alert">';
        element += '        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>';
        element += '    </button>';
        element += '    Invalid username or password.';
        element += '</div>';

        $("#alert-container").empty();
        $("#alert-container").append(element);
    }
};
