<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li><a href="/sales">Sales List</a></li>
            <li class="active">Sales Detail</li>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div role="form" id="templatemo-preferences-form">
                    <div class="row">
                        <div class="col-md-12">
                            <?php component('alert.php') ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" align="right">
                            <span class="badge draft" id="status">DRAFT</span>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" id="id" value="<?php echo $moduleParameter['id']; ?>">
                        <div class="col-md-4">
                            <label for="invoice_number" class="control-label">Invoice No.</label>
                            <input
                                type="text"
                                class="form-control"
                                <?php elementReadOnly($moduleParameter['id'] == 0); ?>
                                id="invoice_number"
                                value="<?php echo $moduleParameter['invoice_number'] ?>"
                            >
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="control-label">Transaction ID</label>
                            <input
                                type="text"
                                class="form-control"
                                id="transaction_id"
                                value="<?php echo $moduleParameter['transaction_id'] ?>"
                            >
                        </div>
                        <div class="col-md-4">
                            <label for="date" class="control-label">Date</label>
                            <?php
                                component(
                                    'dateInput.php',
                                    ['id' => 'transaction_date', 'value' => $moduleParameter['transaction_date']]
                                );
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-bottom-15">
                            <label for="memo">Memo</label>
                            <textarea class="form-control" rows="3" id="memo"><?php echo $moduleParameter['memo']; ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Products</label>
                        </div>
                        <div class="col-md-12 margin-bottom-15">
                            <?php component('dataTable.php'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tab-pane">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <label class="checkbox-inline">
                                                    <input
                                                        type="checkbox"
                                                        id="is_returned"
                                                        onchange="detail.toggleReturned()"
                                                        <?php tickCheckedBoxIfNotNull($moduleParameter['returned_at']); ?>
                                                    > Returned to Seller
                                                </label>
                                            </li>
                                            <li class="list-group-item" id="returned_at_container">
                                                <?php
                                                    component(
                                                        'dateInput.php',
                                                        [
                                                            'id'    => 'returned_at',
                                                            'value' => nullToEmpty($moduleParameter['returned_at']),
                                                            'attributes' => 'placeholder="Select a Date"'
                                                        ]
                                                    );
                                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tab-pane">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <span class="badge" id="total_quantity">500.00</span>
                                                Total Quantity
                                            </li>
                                            <li class="list-group-item">
                                                <span class="badge" id="total_amount">45,000.00</span>
                                                Total Amount
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row templatemo-form-buttons">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-primary" onclick="detail.save()">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/sales/detail.js"></script>