<div class="loading-wrapper" id="loading">
    <div class="progress">
        <div class="progress-bar progress-bar-striped active"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
        </div>
    </div>
</div>

<script type="text/javascript">
    let loading = {
        show() {
            $("#loading").css("visibility", "visible");
        },
        hide() {
            $("#loading").css("visibility", "hidden");
        }
    };
</script>