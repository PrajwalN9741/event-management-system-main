<?php
$title = "Quotation – " . htmlspecialchars($event['name']);
ob_start();
?>
<div class="page-header">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="index.php?page=events">Events</a></li>
                <li class="breadcrumb-item"><a href="index.php?page=events&action=view&id=<?php echo $event['id']; ?>"><?php echo htmlspecialchars($event['name']); ?></a></li>
                <li class="breadcrumb-item active">Quotation</li>
            </ol>
        </nav>
        <h1 class="page-title"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Event Quotation</h1>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?page=quotations&action=generate&event_id=<?php echo $event['id']; ?>" class="btn btn-outline-purple">
            <i class="bi bi-arrow-clockwise me-2"></i>Regenerate PDF
        </a>
        <a href="<?php echo htmlspecialchars($quotation['pdf_path']); ?>" download class="btn btn-primary">
            <i class="bi bi-download me-2"></i>Download PDF
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-panel p-0 overflow-hidden shadow" style="height: 800px;">
            <iframe src="<?php echo htmlspecialchars($quotation['pdf_path']); ?>#view=FitH" width="100%" height="100%" frameborder="0"></iframe>
        </div>
    </div>
    <div class="col-lg-4">
        <!-- Email Card -->
        <div class="card-panel mb-4 shadow-sm border-0">
            <h5 class="panel-title mb-4"><i class="bi bi-envelope-paper me-2 text-primary"></i>Send to Client</h5>
            <form action="index.php?page=quotations&action=email&event_id=<?php echo $event['id']; ?>" method="POST">
                <div class="mb-4">
                    <label class="form-label text-muted small">Recipient Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="email" name="recipient_email" class="form-control" 
                               value="<?php echo htmlspecialchars($event['client_email']); ?>" placeholder="client@example.com">
                    </div>
                    <div class="form-text mt-2">
                        <i class="bi bi-info-circle me-1"></i> A copy will be BCC'd to management.
                    </div>
                </div>
                <button type="submit" class="btn btn-purple btn-lg w-100 py-3">
                    <i class="bi bi-send me-2"></i>Email Quotation
                </button>
            </form>
        </div>

        <!-- History/Meta -->
        <div class="card-panel shadow-sm border-0">
            <h5 class="panel-title mb-3">Quotation Details</h5>
            <div class="mb-3">
                <label class="text-muted small d-block">Generated On</label>
                <p class="mb-0 fw-semibold text-dark"><?php echo date('d M Y, h:i A', strtotime($quotation['generated_at'])); ?></p>
            </div>
            <div class="mb-3">
                <label class="text-muted small d-block">Total Amount</label>
                <p class="mb-0 fs-4 fw-bold text-success">₹<?php echo number_format($quotation['total_amount'], 2); ?></p>
            </div>
            <hr class="my-3">
            <p class="small text-muted mb-0">
                <i class="bi bi-lightbulb me-1 text-warning"></i> 
                Any changes made to the event or inventory will require you to <strong>regenerate</strong> the quotation to reflect the latest costs.
            </p>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
include 'templates/base.php';
?>
