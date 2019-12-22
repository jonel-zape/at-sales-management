<div class="templatemo-alerts" id="alert-container"></div>

<script type="text/javascript">
    let alert = {

        success(message)
        {
            let element = '';

            element += '<div class="alert alert-success alert-dismissible" role="alert">';
            element += '    <button type="button" class="close" data-dismiss="alert">';
            element += '        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>';
            element += '    </button>';
            element += message;
            element += '</div>';

            this.dismiss();
            $("#alert-container").append(element);
        },

        error(errors)
        {
            let element = '';

            let message = '';
            for (let key in errors) {
                message +=  errors[key] + '<br>';
            }

            element += '<div class="alert alert-danger alert-dismissible" role="alert">';
            element += '    <button type="button" class="close" data-dismiss="alert">';
            element += '        <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>';
            element += '    </button>';
            element += message;
            element += '</div>';

            this.dismiss();
            $("#alert-container").append(element);
        },

        dismiss() {
            $("#alert-container").empty();
        }
    };
</script>