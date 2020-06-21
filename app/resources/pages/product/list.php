<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li class="active">Product List</li>
            <li><a href="/product/create">New Product</a></li>
        </ol>
        <div class="row">
            <div class="col-md-6 margin-bottom-15">
                <input type="text" class="form-control" id="keyword" placeholder="Enter Keyword">
            </div>
            <div class="col-md-5 margin-bottom-15 inline-to-control">
                <select class="form-control" id="filterBy">
                    <option value="0">All</option>
                    <option value="1">Name</option>
                    <option value="2">Memo</option>
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
            <div class="col-md-12 margin-bottom-15">
                <?php component('dataTable.php'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 margin-bottom-15">
                <button type="button" class="btn btn-default" onclick="list.create()">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i>
                    New Product
                </button>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/product/list.js<?php noCache(); ?>"></script>