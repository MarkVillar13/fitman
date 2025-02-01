<?php if (!empty($message)): ?>
<div class="modal fade custom-modal" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title <?php echo $modalClass; ?>" id="messageModalLabel">
                    <?php echo $modalTitle; ?>
                </h5>
                <button type="button" class="btn-close custom-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <?php echo $message; ?>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary custom-btn" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal Base Styles */
.custom-modal .modal-content {
    border: none;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

/* Header Styles */
.custom-modal .modal-header {
    padding: 1.5rem 1.5rem 0.5rem;
}

.custom-modal .modal-title {
    font-size: 1.25rem;
    font-weight: 600;
}

/* Close Button */
.custom-modal .custom-close {
    background: none;
    padding: 0.5rem;
    margin: -0.5rem -0.5rem -0.5rem auto;
    transition: opacity 0.2s ease;
}

.custom-modal .custom-close:hover {
    opacity: 0.75;
}

/* Body Styles */
.custom-modal .modal-body {
    padding: 1.5rem;
    font-size: 1rem;
    color: #4a5568;
    line-height: 1.5;
}

/* Footer Styles */
.custom-modal .modal-footer {
    padding: 0.5rem 1.5rem 1.5rem;
}

/* Button Styles */
.custom-modal .custom-btn {
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.2s ease;
    background-color: #f1f5f9;
    border: none;
    color: #475569;
}

.custom-modal .custom-btn:hover {
    background-color: #e2e8f0;
    transform: translateY(-1px);
}

/* Animation and Backdrop */
.custom-modal.modal {
    background-color: rgba(0, 0, 0, 0.3);
}

.custom-modal.fade .modal-dialog {
    transform: scale(0.95);
    transition: transform 0.2s ease-out;
}

.custom-modal.show .modal-dialog {
    transform: scale(1);
}

/* Status Colors */
.custom-modal .text-danger {
    color: #ef4444 !important;
}

.custom-modal .text-success {
    color: #22c55e !important;
}

.custom-modal .text-warning {
    color: #f59e0b !important;
}

/* Responsive Adjustments */
@media (max-width: 576px) {
    .custom-modal .modal-dialog {
        margin: 1rem;
    }
    
    .custom-modal .modal-content {
        border-radius: 12px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('messageModal'), {
        backdrop: 'static',
        keyboard: false
    });
    modal.show();
});
</script>
<?php endif; ?>