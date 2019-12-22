<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li class="active">Purchase List</li>
        </ol>
        <div class="row">
            <div class="col-md-12 margin-bottom-15">
                <?php component('alert.php') ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-30">
                <?php component('dataTable.php'); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 margin-bottom-15">
                <button type="button" class="btn btn-default" onclick="list.create()">Create New PO</button>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/purchase/list.js"></script>