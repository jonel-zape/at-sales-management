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
                            <span class="badge badge-invoice-status" id="status">New</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 margin-bottom-15">
                            <label for="invoice_number">Invoice No.</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                <?php elementReadOnly($moduleParameter['id'] == 0); ?>
                                id="invoice_number"
                                value="<?php echo $moduleParameter['invoice_number'] ?>"
                            >
                        </div>
                        <div class="col-md-4 margin-bottom-15">
                            <label for="date">Transaction ID</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="transaction_id"
                                value="<?php echo $moduleParameter['transaction_id'] ?>"
                            >
                        </div>
                        <div class="col-md-4 margin-bottom-15">
                            <label for="date">Date</label>
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
                        <div class="col-md-12 margin-bottom-15">
                            <button type="button" class="btn btn-default" data-toggle="modal" data-target="#select-from-purchase">
                                <i class="fa fa-plus-square" aria-hidden="true"></i>
                                Add Items
                            </button>
                        </div>
                    </div>
                    <div class="row">
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

<div class="modal fade" id="select-from-purchase" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row">
                    <div class="col-md-12">
                        <?php component('alert.php', ['id' => 'alertModal']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-15">
                        <input type="text" class="form-control" placeholder="Search Purchase Invoice Number" id="autocomplete-purchase-number">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-15">
                        <div class="row">
                            <div class="col-md-12 margin-bottom-10">
                                <label class="control-label">Date Received:</label>
                                <span id="purchaseDateReceived"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 margin-bottom-10">
                                <label class="control-label">Memo:</label>
                                <span id="purchaseMemo"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 margin-bottom-10">
                        <?php component('dataTable.php', ['id' => 'purchaseTable']); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal" id="buttonCloseModal">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="detail.importSelected()">
                    <i class="fa fa-check-square-o " aria-hidden="true"></i>
                    Add Selected
                </button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let isReturned = "<?php echo isEmptyToStringBoolean($moduleParameter['returned_at']) ?>";

    let autocompletePurchaseNumberOriginalValue = '';
    let autocompletePurchaseNumberSelectedId = '';

    $("#autocomplete-purchase-number").on('focus', function() {
        $(this).autocomplete({
            serviceUrl: '/purchase/autonCompleteSearchInvoice',
            dataType: 'json',
            paramName: 'keyword',
            preserveInput: true,
            transformResult: function(response) {
                return {
                    suggestions: $.map(response.values, function(dataItem) {
                        return {
                            value: dataItem.invoice_number,
                            data: dataItem.id
                        };
                    })
                };
            },
            onSelect: function (suggestion) {
                if ($("#autocomplete-purchase-number").val() == autocompletePurchaseNumberOriginalValue) {
                    return
                }

                if (suggestion.data == 0) {
                    $("#autocomplete-purchase-number").val(autocompletePurchaseNumberOriginalValue);
                    return;
                }
                autocompletePurchaseNumberOriginalValue = suggestion.value;
                autocompletePurchaseNumberSelectedId = suggestion.data;
                $("#autocomplete-purchase-number").val(autocompletePurchaseNumberOriginalValue);
                detail.purchaseInvoiceSelected();
            }
        });
    });

    $("#autocomplete-purchase-number").blur(function(){
        if (autocompletePurchaseNumberSelectedId == 0) {
            return;
        }
        $(this).val(autocompletePurchaseNumberOriginalValue);
    });
</script>

<input type="hidden" id="id" value="<?php echo $moduleParameter['id']; ?>">

<script src="/js/modules/sales/detail.js"></script>