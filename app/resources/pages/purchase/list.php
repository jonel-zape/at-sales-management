<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li class="active">Purchase List</li>
            <li><a href="/purchase/create">Create New PO</a></li>
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
                            'value'      => getDateToday(),
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
                            'value'      => getDateToday(),
                            'attributes' => 'placeholder="Date To"'
                        ]
                    );
                ?>
            </div>
            <div class="col-md-3 margin-bottom-15 inline-to-control">
                <select class="form-control" id="status">
                    <option value="0">All</option>
                    <option value="1">Received</option>
                    <option value="2">Unreceived</option>
                </select>
            </div>
            <div class="col-md-1 margin-bottom-15 inline-to-control">
                <button type="button" class="form-control btn btn-default" onclick="list.find()">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-5">
                <?php component('alert.php') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-5">
                <div class="panel panel-info legends">
                    <div class="panel-heading">
                        <i class="fa fa-circle purchase-received" aria-hidden="true"></i>
                        <span>Received</span>
                        &nbsp;&nbsp;
                        <i class="fa fa-circle purchase-unreceived" aria-hidden="true"></i>
                        <span>Unreceived</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="row  margin-bottom-15">
            <div class="col-md-12">
                <?php component('dataTable.php'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 margin-bottom-15">
                <button type="button" class="btn btn-default" onclick="list.create()">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    Create New PO
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/purchase/list.js<?php noCache(); ?>"></script>