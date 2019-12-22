<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li><a href="/home">Home</a></li>
            <li><a href="/purchase">Purchase List</a></li>
            <li class="active">Purchase Detail</li>
        </ol>

        <div class="row">
            <div class="col-md-12">
                <form role="form" id="templatemo-preferences-form">
                    <div class="row">
                        <div class="col-md-6 margin-bottom-15">
                            <label for="firstName" class="control-label">Date</label>
                            <input type="text" class="form-control" id="date" value="<?php echo date("Y-m-d");?>">                  
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 margin-bottom-15">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" rows="3" id="notes"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 margin-bottom-15">
                            <label for="notes">Products</label>
                        </div>
                    </div>

                    <div class="row templatemo-form-buttons">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="reset" class="btn btn-default">Reset</button>    
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>