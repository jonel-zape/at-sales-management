<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li class="active">Payment List</li>
        </ol>
        <div class="row">
            <div class="col-md-4 margin-bottom-15">
                <input type="text" class="form-control" id="invoice_number" placeholder="Search Invoice">
            </div>
            <div class="col-md-2 margin-bottom-15">
                <?php
                    component(
                        'dateInput.php',
                        [
                            'id'         => 'date_from',
                            'value'      => '',
                            'attributes' => 'placeholder="Date From"'
                        ]
                    );
                ?>
            </div>
            <div class="col-md-2 margin-bottom-15">
                <?php
                    component(
                        'dateInput.php',
                        [
                            'id'         => 'date_to',
                            'value'      => '',
                            'attributes' => 'placeholder="Date To"'
                        ]
                    );
                ?>
            </div>
            <div class="col-md-3 margin-bottom-15 inline-to-control">
                <select class="form-control" id="status">
                    <option value="0">All</option>
                    <option value="1">Paid</option>
                    <option value="2">Unpaid</option>
                    <option value="3">Incomplete Payment</option>
                    <option value="4" selected="selected">Unpaid / Incomplete Payment</option>
                    <option value="5">Excess</option>
                </select>
            </div>
            <div class="col-md-1 margin-bottom-15 inline-to-control">
                <button type="button" class="form-control btn btn-default" onclick="list.find()">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php component('alert.php') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-5">
                <div class="panel panel-info legends">
                    <div class="panel-heading">
                        <i class="fa fa-circle payment-paid" aria-hidden="true"></i>
                        <span>Paid</span>
                        &nbsp;&nbsp;
                        <i class="fa fa-circle payment-unpaid" aria-hidden="true"></i>
                        <span>Unpaid</span>
                        &nbsp;&nbsp;
                        <i class="fa fa-circle payment-incomplete" aria-hidden="true"></i>
                        <span>Incomplete Payment</span>
                        &nbsp;&nbsp;
                        <i class="fa fa-circle payment-excess" aria-hidden="true"></i>
                        <span>Excess</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row  margin-bottom-15">
            <div class="col-md-12">
                <?php component('dataTable.php'); ?>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/payment/list.js"></script>