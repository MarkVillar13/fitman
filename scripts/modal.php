<!-- The Modal -->
<div class="modal fade" id="myModal">
    <div class="modal-dialog" style="display:flex;align-items: center;min-height: calc(100% - 1rem);">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title <?php echo isset($modalClass) ? $modalClass : ''; ?>">
                    <?php echo isset($modalTitle) ? $modalTitle : ''; ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <?php echo isset($message) ? $message : ''; ?>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($message)) { ?>
        $('#myModal').modal('show');
    <?php } ?>
});
</script>
