<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li><a href="/product">Product List</a></li>
            <li class="active">Product Detail</li>
        </ol>
        <div class="row">
            <div class="col-md-12">
                <div role="form" id="templatemo-preferences-form">
                    <div class="row">
                        <div class="col-md-12">
                            <?php component('alert.php') ?>
                        </div>
                    </div>
                    <input type="hidden" id="id" value="<?php echo $moduleParameter['id']; ?>">
                    <div class="row">
                        <div class="col-md-12 margin-bottom-15">
                            <h1>Basic Info</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 margin-bottom-15">
                            <label for="stock_no" class="control-label">Stock No.</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="stock_no"
                                value="<?php echo $moduleParameter['stock_no']; ?>"
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 margin-bottom-15">
                            <label for="name" class="control-label">Name</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="name"
                                value="<?php echo $moduleParameter['name']; ?>"
                            >
                        </div>
                        <div class="col-md-6 margin-bottom-15">
                            <label for="short_name" class="control-label">Short Name</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="short_name"
                                value="<?php echo $moduleParameter['short_name']; ?>"
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 margin-bottom-15">
                            <label for="memo">Memo</label>
                            <textarea class="form-control" rows="3" id="memo"><?php echo $moduleParameter['memo']; ?></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 margin-bottom-15">
                            <h1>Prices</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 margin-bottom-15">
                            <label for="cost_price" class="control-label">Cost</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="cost_price"
                                value="<?php echo $moduleParameter['cost_price']; ?>"
                            >
                        </div>
                        <div class="col-md-6 margin-bottom-15">
                            <label for="selling_price" class="control-label">Selling</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="selling_price"
                                value="<?php echo $moduleParameter['selling_price']; ?>"
                            >
                        </div>
                        <div class="col-md-6 margin-bottom-15">
                            <label for="wholesale_price" class="control-label">Wholesale</label>
                            <input
                                type="text"
                                class="form-control no-margin"
                                id="wholesale_price"
                                value="<?php echo $moduleParameter['wholesale_price']; ?>"
                            >
                        </div>
                    </div>
                    <div class="row templatemo-form-buttons">
                        <div class="col-md-12 margin-bottom-15">
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

<script src="/js/modules/product/detail.js"></script>