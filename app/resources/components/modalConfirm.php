<div class="modal fade" id="confirmModal" style="z-index: 99999" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">Are you sure you want to sign out?</h4>
            </div>
            <div class="modal-footer">
                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="modalConfirm.onConfirmYes()"
                    data-dismiss="modal"
                >Yes</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
            </div>
        </div>
    </div>
</div>

<button id="toggle-confirm-modal" data-toggle="modal" data-target="#confirmModal" style="display: none"></button>

<script type="text/javascript">
    let modalConfirm = {
        userDefinedYes: {},

        show(params = {message: '', confirmYes: {}}) {
            this.userDefinedYes = params.confirmYes;
            $("#myModalLabel").html(params.message);
            $("#toggle-confirm-modal").click();
        },

        onConfirmYes() {
            this.userDefinedYes();
        }
    };
</script>