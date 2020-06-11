let summary = {
    months: [
        null,
        "January",
        "February",
        "March",
        "April",
        "May",
        "June",
        "July",
        "August",
        "September",
        "October",
        "November",
        "December"
    ],
    lineChartData: {
        labels : ["January", "February","March","April","May"],
        datasets : [
            {
                label: "Cost",
                fillColor : "rgba(0,0,0,0)",
                strokeColor : "rgba(224,155,6,1)",
                pointColor : "rgba(224,155,6,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(224,155,6,1)",
                data : [0,0,0,0,0]
            },
            {
                label: "Sales",
                fillColor : "rgba(0,0,0,0)",
                strokeColor : "rgba(65,112,204,1)",
                pointColor : "rgba(65,112,204,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(65,112,204,1)",
                data : [0,0,0,0,0]
            },
            {
                label: "Loss",
                fillColor : "rgba(0,0,0,0)",
                strokeColor : "rgba(219,64,64,1)",
                pointColor : "rgba(219,64,64,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(219,64,64,1)",
                data : [0,0,0,0,0]
            },
            {
                label: "Net Income",
                fillColor : "rgba(0,0,0,0)",
                strokeColor : "rgba(5,201,48,1)",
                pointColor : "rgba(5,201,48,1)",
                pointStrokeColor : "#fff",
                pointHighlightFill : "#fff",
                pointHighlightStroke : "rgba(5,201,48,1)",
                data : [0,0,0,0,0]
            }
        ]
    },

    load() {
        loading.show();
        http.get(
            '/home/summary',
            {
                month: el.val("#month"),
                year: el.val("#year")
            }
        ).done(function(response){
            el.setContent("#summary-cost", number.money(parseFloat(response.values.widget.cost)));
            el.setContent("#summary-sales", number.money(parseFloat(response.values.widget.sales)));
            el.setContent("#summary-loss", number.money(parseFloat(response.values.widget.loss)));
            el.setContent("#summary-net-income", number.money(parseFloat(response.values.widget.net_income)));

            summary.lineChartData.labels = [];
            summary.lineChartData.datasets[0].data = [];
            summary.lineChartData.datasets[1].data = [];
            summary.lineChartData.datasets[2].data = [];
            summary.lineChartData.datasets[3].data = [];
            response.values.graph.forEach(function(item){
                let monthYear = item.month_year.split('-');
                summary.lineChartData.labels.push(summary.months[monthYear[0]]);
                summary.lineChartData.datasets[0].data.push(item.cost);
                summary.lineChartData.datasets[1].data.push(item.sales);
                summary.lineChartData.datasets[2].data.push(item.loss);
                summary.lineChartData.datasets[3].data.push(item.net_income);
            });

            el.setContent("#chart-holder", `<canvas id="templatemo-line-chart"></canvas>`);
            let context = document.getElementById("templatemo-line-chart").getContext("2d");
            window.chart = new Chart(context).Line(summary.lineChartData, {
                responsive: true
            });

            loading.hide();
        }).catch(function(response){
            loading.hide();
        });
    }
};

summary.load();