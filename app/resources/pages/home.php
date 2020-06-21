<div class="templatemo-content-wrapper">
    <div class="templatemo-content">
        <ol class="breadcrumb">
            <li class="active">Home</li>
        </ol>
        <div class="row">
            <div class="col-md-6 margin-bottom-15">
            </div>
            <div class="col-md-6 margin-bottom-15">
                <div class="row">
                    <div class="col-md-5 margin-bottom-15">
                        <select class="form-control" id="month">
                            <?php
                                function isSelected($value) {
                                    $month = date('m') + 0;
                                    if ($value == $month) {
                                        echo 'selected="selected"';
                                    } else {
                                        echo '';
                                    }
                                }
                            ?>
                            <option value="1" <?php isSelected(1); ?>>January</option>
                            <option value="2" <?php isSelected(2); ?>>February</option>
                            <option value="3" <?php isSelected(3); ?>>March</option>
                            <option value="4" <?php isSelected(4); ?>>April</option>
                            <option value="5" <?php isSelected(5); ?>>May</option>
                            <option value="6" <?php isSelected(6); ?>>June</option>
                            <option value="7" <?php isSelected(7); ?>>July</option>
                            <option value="8" <?php isSelected(8); ?>>August</option>
                            <option value="9" <?php isSelected(9); ?>>September</option>
                            <option value="10" <?php isSelected(10); ?>>October</option>
                            <option value="11" <?php isSelected(11); ?>>November</option>
                            <option value="12" <?php isSelected(12); ?>>December</option>
                        </select>
                    </div>
                    <div class="col-md-5 margin-bottom-15">
                        <select class="form-control" id="year">
                            <?php
                                $year = date('Y');
                                for($i = $year - 5; $i < $year + 6; $i++) {
                                    $selected = '';
                                    if ($i == $year) {
                                        $selected = 'selected="selected"';
                                    }
                                    ?>
                                        <option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2 margin-bottom-15">
                        <button type="button" class="form-control btn btn-default" onclick="summary.load()">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 margin-bottom-15">
                <div class="widget widget-cost">
                    <div class="widget-counter" id="summary-cost">0.00</div>
                    <div class="widget-label">
                        Cost
                    </div>
                </div>
            </div>
            <div class="col-md-3 margin-bottom-15">
                <div class="widget widget-sales">
                    <div class="widget-counter" id="summary-sales">0.00</div>
                    <div class="widget-label">
                        Sales
                    </div>
                </div>
            </div>
            <div class="col-md-3 margin-bottom-15">
                <div class="widget widget-loss">
                    <div class="widget-counter" id="summary-loss">0.00</div>
                    <div class="widget-label">
                        <!-- <i class="fa fa-circle summary-loss" aria-hidden="true"></i>
                        &nbsp; -->
                        Loss
                    </div>
                </div>
            </div>
            <div class="col-md-3 margin-bottom-15">
                <div class="widget widget-net-income">
                    <div class="widget-counter" id="summary-net-income">0.00</div>
                    <div class="widget-label">
                        <!-- <i class="fa fa-circle summary-net-income" aria-hidden="true"></i>
                        &nbsp; -->
                        Net Income
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6 margin-bottom-15">
                <div class="panel panel-primary">
                    <div class="panel-heading">Past Months Comparison</div>
                    <div id="chart-holder">
                        <canvas id="templatemo-line-chart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/js/modules/home/summary.js<?php noCache(); ?>"></script>