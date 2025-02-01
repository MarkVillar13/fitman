<!-- The Modal -->
<div class="modal fade" id="myModal2">
    <div class="modal-dialog" style="display:flex;align-items: center;min-height: calc(100% - 1rem);">
        <div class="modal-content w3-theme-l4">

            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title <?php echo isset($modalClass) ? $modalClass : ''; ?>">
                    <?php echo isset($modalTitle) ? $modalTitle : ''; ?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form class="" action="" method="get">
              <!-- Modal body -->
              <div class="modal-body">
                <div class="form-floating mb-3 mt-3">
                  <input type="text" class="form-control" placeholder="Username" name="email" required>
                  <label for="username" class="w3-text-black">Email</label>
                </div>
                  <?php echo isset($messageForgot) ? $messageForgot : ''; ?>
              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                  <button type="submit" name="ChangePassword" class="w3-btn w3-green">Yes</button>
                  <button type="button" class="w3-btn w3-red" data-bs-dismiss="modal">No</button>
              </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() {
    <?php if (!empty($messageForgot)) { ?>
        $('#myModal2').modal('show');
    <?php } ?>
});
</script>
