<?php
    $elementId = $params['id'];
    $value = getDateToday();
    if (isset($params['value'])) {
        $value = $params['value'];
    }
    $attributes = '';
    if (isset($params['attributes'])) {
        $attributes = $params['attributes'];
    }
?>

<input
    type="text"
    class="form-control date"
    id="<?php echo $elementId; ?>"
    value="<?php echo $value; ?>"
    <?php echo $attributes; ?>
>

<script type="text/javascript">
    $("#<?php echo $elementId; ?>").datepicker({
        format: "yyyy-mm-dd",
        autoHide: true,
    });
</script>