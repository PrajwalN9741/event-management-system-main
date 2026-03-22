<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #333; margin: 0; padding: 0; }
        .header { background: #1a237e; color: white; text-align: center; padding: 20px; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 5px 0 0; font-size: 12px; }
        .contact-bar { background: #1a237e; color: white; display: table; width: 100%; padding: 5px 20px; font-size: 10px; border-top: 1px solid rgba(255,255,255,0.2); }
        .contact-left { display: table-cell; text-align: left; }
        .contact-right { display: table-cell; text-align: right; }
        
        .section-title { background: #6c3ff3; color: white; padding: 10px; font-size: 14px; font-weight: bold; margin-top: 20px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th { background: #6c3ff3; color: white; padding: 8px; font-size: 11px; text-align: left; }
        table td { border: 1px solid #e5e7eb; padding: 8px; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .totals { margin-top: 20px; width: 300px; float: right; }
        .totals table { border: none; }
        .totals td { border: none; padding: 4px 8px; }
        .grand-total { background: #6c3ff3; color: white; font-weight: bold; font-size: 14px; }
        
        .footer { margin-top: 50px; font-size: 9px; color: #6b7280; text-align: center; }
        .signature-table { margin-top: 50px; width: 100%; }
        .signature-table td { border: none; text-align: center; padding-top: 40px; }
        .sig-line { border-top: 1px solid #333; width: 180px; margin: 0 auto 5px; }
    </style>
</head>
<body>
    <div class="header">
        <p>||Sri Seeti Byraveshwara Swamy Prasana||</p>
        <h1>MNNMP EVENTS</h1>
        <p>Amitiganahalli, Chintamani(T) Chikkabalapur(D)</p>
    </div>
    <div class="contact-bar">
        <div class="contact-left">Prop:- Mithun K</div>
        <div class="contact-right">Mob:- 9141840705</div>
    </div>

    <div style="margin: 20px;">
        <h2 style="color: #1e1b4b; border-bottom: 2px solid #6c3ff3; padding-bottom: 5px;">QUOTATION</h2>
        
        <table style="border: none;">
            <tr>
                <td style="border: none; width: 60%;">
                    <strong>Event:</strong> <?php echo htmlspecialchars($event['name']); ?><br>
                    <strong>Type:</strong> <?php echo htmlspecialchars($event['event_type']); ?><br>
                    <strong>Date:</strong> <?php echo date('d M Y', strtotime($event['event_date'])); ?><br>
                    <strong>Venue:</strong> <?php echo htmlspecialchars($event['venue']); ?>
                </td>
                <td style="border: none; text-align: right;">
                    <strong>Client:</strong> <?php echo htmlspecialchars($event['client_name']); ?><br>
                    <strong>Phone:</strong> <?php echo htmlspecialchars($event['client_phone']); ?><br>
                    <strong>No:</strong> QT-<?php echo str_pad($event['id'], 4, '0', STR_PAD_LEFT); ?>-<?php echo date('Y'); ?>
                </td>
            </tr>
        </table>

        <?php if (!empty($flowers)): ?>
        <div class="section-title">Flower Arrangements & Decor</div>
        <table>
            <thead>
                <tr>
                    <th>Item Type</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price (Rs.)</th>
                    <th class="text-right">Total (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($flowers as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f['type']); ?></td>
                    <td class="text-center"><?php echo $f['qty']; ?></td>
                    <td class="text-right"><?php echo number_format($f['price'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($f['price'] * $f['qty'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <?php if (!empty($inventory_usages)): ?>
        <div class="section-title">Inventory & Equipment</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price (Rs.)</th>
                    <th class="text-right">Total (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory_usages as $ui): ?>
                <tr>
                    <td><?php echo htmlspecialchars($ui['name']); ?></td>
                    <td class="text-center"><?php echo $ui['quantity_used']; ?> <?php echo htmlspecialchars($ui['unit']); ?></td>
                    <td class="text-right"><?php echo number_format($ui['price_per_unit'], 2); ?></td>
                    <td class="text-right"><?php echo number_format($ui['quantity_used'] * $ui['price_per_unit'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <div class="totals">
            <table>
                <?php if ($flower_total > 0): ?>
                <tr>
                    <td class="text-right">Flower Total:</td>
                    <td class="text-right">Rs. <?php echo number_format($flower_total, 2); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($inventory_total > 0): ?>
                <tr>
                    <td class="text-right">Inventory Total:</td>
                    <td class="text-right">Rs. <?php echo number_format($inventory_total, 2); ?></td>
                </tr>
                <?php endif; ?>
                <tr class="grand_total">
                    <td class="text-right grand-total">GRAND TOTAL:</td>
                    <td class="text-right grand-total">Rs. <?php echo number_format($grand_total, 2); ?></td>
                </tr>
            </table>
        </div>

        <div style="clear: both;"></div>

        <div class="section-title">Terms & Conditions</div>
        <p style="font-size: 9px; color: #666;">
            1. 50% advance payment required to confirm booking.<br>
            2. Cancellation within 7 days of event is non-refundable.<br>
            3. This quotation is valid for 30 days from the date of issue.
        </p>

        <table class="signature-table">
            <tr>
                <td>
                    <div class="sig-line"></div>
                    <div style="font-size: 10px; font-weight: bold;">Client Signature</div>
                    <div style="font-size: 9px;"><?php echo htmlspecialchars($event['client_name']); ?></div>
                </td>
                <td style="width: 100px;"></td>
                <td>
                    <div class="sig-line"></div>
                    <div style="font-size: 10px; font-weight: bold;">Authorised Signatory</div>
                    <div style="font-size: 9px;">MNNMP EVENTS</div>
                </td>
            </tr>
        </table>

        <div class="footer">
            Generated by MNNMP Events • <?php echo date('d M Y h:i A'); ?> • Contact: 9141840705
        </div>
    </div>
</body>
</html>
