<?php
    $alertId = 'alert';

    if (isset($params['id'])) {
        $alertId = $params['id'];
    }
?>

<div class="templatemo-alerts" id="<?php echo $alertId; ?>"></div>

<script type="text/javascript">
    let <?php echo $alertId; ?> = {

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
            $("#<?php echo $alertId; ?>").append(element);
            this.focus();
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
            $("#<?php echo $alertId; ?>").append(element);
            this.focus();
        },

        dismiss() {
            $("#<?php echo $alertId; ?>").empty();
        },

        focus() {
            $("div#<?php echo $alertId; ?>")[0].scrollIntoView();
        }
    };
</script>