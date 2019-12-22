<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li class="active">Product List</li>
        </ol>
        <div class="row">
            <div class="col-md-5 margin-bottom-15">
                <input type="text" class="form-control" placeholder="Enter Keyword">
            </div>
            <div class="col-md-5 margin-bottom-15">
                <select class="form-control" id="singleSelect">
                    <option>All</option>
                    <option>Name</option>
                    <option>Description</option>
                </select>
            </div>
            <div class="col-md-2 margin-bottom-15">
                <button type="button" class="btn btn-default" onclick="list.find()">Find</button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 margin-bottom-15">
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Description</th>
                                <th>Cost</th>
                                <th>Selling</th>
                                <th>Wholesale</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            for ($i = 0; $i < 10; $i++) {
                            ?>
                            <tr>
                                <td><?php echo ($i + 1); ?></td>
                                <td>Product <?php echo ($i + 1); ?></td>
                                <td>Best selling</td>
                                <td>40.00</td>
                                <td>50.00</td>
                                <td>45.00</td>
                                <td><a href="<?php echo '/product/edit/'.($i + 1); ?>" class="btn btn-link">View / Edit</a></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <ul class="pagination pull-right">
                    <li class="disabled"><a href="#">&laquo;</a></li>
                    <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">2 <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">3 <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">4 <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">5 <span class="sr-only">(current)</span></a></li>
                    <li><a href="#">&raquo;</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/product/list.js"></script>