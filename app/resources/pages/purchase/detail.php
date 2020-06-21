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
                        <div class="col-md-12" align="right">
                            <span class="badge badge-invoice-status" id="status">New</span>
                        </div>
                    </div>
                    <div class="row">
                        <input type="hidden" id="id" value="<?php echo $moduleParameter['id']; ?>">
                        <div class="col-md-6 margin-bottom-15">
                            <label for="invoice_number" class="control-label">Invoice No.</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                <?php elementReadOnly($moduleParameter['id'] == 0); ?>
                                id="invoice_number"
                                value="<?php echo $moduleParameter['invoice_number'] ?>"
                            >
                        </div>
                        <div class="col-md-6 margin-bottom-15">
                            <label for="date" class="control-label">Date</label>
                            <?php
                                component(
                                    'dateInput.php',
                                    [
                                        'id'    => 'transaction_date',
                                        'value' => $moduleParameter['transaction_date'],
                                        'class' => 'no-margin'
                                    ]
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
                                                            'value' => nullToEmpty($moduleParameter['received_at']),
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
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#sell-modal" onclick="detail.loadSalesModal()">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                                Sell Item(s)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="sell-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-12 margin-bottom-15">
                        <?php component('alert.php', ['id' => 'alertModal']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 margin-bottom-15">
                        <label for="sales-transaction_id" class="control-label">Transaction ID</label>
                        <input type="text" class="form-control no-margin" id="sales_transaction_id">
                    </div>
                    <div class="col-md-6 margin-bottom-15">
                        <label for="sales_transaction_date" class="control-label">Date</label>
                        <?php
                            component(
                                'dateInput.php',
                                [
                                    'id'    => 'sales_transaction_date',
                                    'value' => getDateToday(),
                                    'class' => 'form-control no-margin'
                                ]
                            );
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-15">
                        <label for="sales_memo">Memo</label>
                        <textarea class="form-control" rows="3" id="sales_memo"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-10">
                        <?php component('dataTable.php', ['id' => 'salesTable']); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="buttonCloseModal">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-default" onclick="detail.loadSalesModal()">
                    <i class="fa fa-undo" aria-hidden="true"></i>
                    Reset Items
                </button>
                <button type="button" class="btn btn-primary" onclick="detail.sellItems()">
                    <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                    Sell
                </button>
            </div>
        </div>
    </div>
</div>

<button data-toggle="modal" data-target="#show-sales" style="display: none;" id="show-sales-button"></button>
<div class="modal fade" id="show-sales" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>Sales Transaction</h4>
                <div class="row">
                    <div class="col-md-12 margin-bottom-15">
                        <?php component('alert.php', ['id' => 'alertModalSalesDetail']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-5">
                        <div class="panel panel-info legends">
                            <div class="panel-heading">
                                <i class="fa fa-circle sales-sold" aria-hidden="true"></i>
                                <span>Sold</span>
                                &nbsp;&nbsp;
                                <i class="fa fa-circle sales-rts" aria-hidden="true"></i>
                                <span>Returned to Seller</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-10">
                        <?php component('dataTable.php', ['id' => 'salesDetailsTable']); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/purchase/detail.js<?php noCache(); ?>"></script>

