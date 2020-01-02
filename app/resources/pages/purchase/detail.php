<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li><a href="/purchase">Purchase List</a></li>
            <li class="active">Purchase Detail</li>
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
                        <input type="hidden" id="id" value="<?php echo $moduleParameter['id']; ?>">
                        <div class="col-md-6">
                            <label for="invoice_number" class="control-label">Invoice No.</label>
                            <input
                                type="text"
                                class="form-control"
                                <?php elementReadOnly($moduleParameter['id'] == 0); ?>
                                id="invoice_number"
                                value="<?php echo $moduleParameter['invoice_number'] ?>"
                            >
                        </div>
                        <div class="col-md-6">
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
                            <div class="tab-pane" id="messages">
                                <div class="list-group">
                                    <span class="list-group-item active">Sales</span>
                                    <a href="#" class="list-group-item">S00000000000001</a>
                                    <a href="#" class="list-group-item">S00000000000002</a>
                                    <a href="#" class="list-group-item">S00000000000003</a>
                                    <a href="#" class="list-group-item">S00000000000004</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="tab-pane">
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <label class="checkbox-inline">
                                                    <input
                                                        type="checkbox"
                                                        id="is_received"
                                                        onchange="detail.toggleReceived()"
                                                        <?php tickCheckedBoxIfNotNull($moduleParameter['received_at']); ?>
                                                    > Received
                                                </label>
                                            </li>
                                            <li class="list-group-item" id="received_at_container">
                                                <?php
                                                    component(
                                                        'dateInput.php',
                                                        [
                                                            'id'    => 'received_at',
                                                            'value' => nullToEmpty($moduleParameter['received_at'])
                                                        ]
                                                    );
                                                ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
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
                            <button type="button" class="btn btn-primary" onclick="detail.save()">Save</button>
                            <button type="button" class="btn btn-success" onclick="detail.save()">Sell</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/purchase/detail.js"></script>