$('body').on('click', '.pevnev-statistic-widget-block .range-choice > a', function () {
    $('.pevnev-statistic-widget-block .range-choice').find('a').removeClass('active');
    $(this).addClass('active');
    $('#statisticwidgetform-datestart').val($(this).attr('data-start'));
    $('#statisticwidgetform-dateend').val($(this).attr('data-end'));
    $('#statisticWidgetForm').submit();
    return false;
});

var config = {
    type: 'line',
    data: {
        labels: [],
        datasets: [
            {
                index: 'all',
                label: "Уникальные (0)",
                backgroundColor: window.chartColors.blue,
                borderColor: window.chartColors.blue,
                data: [],
                fill: false,
            },
            {
                index: 'allOfAll',
                label: "Всего (0)",
                backgroundColor: window.chartColors.red,
                borderColor: window.chartColors.red,
                data: [],
                fill: false,
            }]
    },
    options: {
        responsive: true,
        title: {
            display: true,
            text: ''
        },
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                fontSize: 14,
                padding: 40
            }
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                    display: false,
                    scaleLabel: {
                        display: true,
                        labelString: 'Время'
                    },
                    type: 'time',
                    time: {
                        tooltipFormat: "YYYY-MM-DD HH:mm",
                    },
                    valueFormatString: "YYYY-MM-DD HH:mm"
                }],
            yAxes: [{
                    display: false,
                    scaleLabel: {
                        display: true,
                        labelString: 'Количество'
                    }
                }]
        }
    }
};

setTimeout(function () {
    $('body').find('.pevnev-statistic-widget-block').find('#StatisticsToday').trigger('click');
}, 100);

var counter = 0;
var blocker = false;
 
$('#statisticWidgetForm').on('beforeSubmit', function (e) {
    e.preventDefault();
    var waitingCircle = $('body').find('.pevnev-statistic-widget-block .fa-circle-o-notch');
    $.ajax({
        url: '/statistics/chart-line',
        type: 'POST',
        data: $(this).serialize(),
        success: function (data) {
            if ($(data.labels).length === 0) {
                $('body').find('.pevnev-statistic-widget-block .no-found').fadeIn();
            } else {
                $('body').find('.pevnev-statistic-widget-block .no-found').fadeOut();
            }
            $('.chartjs-hidden-iframe').remove();
            config.data.labels = data.labels;

            $.each(config.data.datasets, function (index, value) {
                if (data[value.index]) {
                    config.data.datasets[index].data = data[value.index];
                    config.data.datasets[index].label = config.data.datasets[index].label.replace(/\(\d+\)/, '(' + data.countData[value.index] + ')');
                }
            });

            if (counter === 0) {
                var ctx = document.getElementById("canvas-chart-order-form").getContext("2d");
                ctx.height = 200;
                window.orderFormLog = new Chart(ctx, config);
                counter = 1;
            } else {
                window.orderFormLog.update();
            }
        },
        beforeSend: function () {
            if (blocker) {
                return false;
            } else {
                blocker = true;
            }

            $('body').find('.pevnev-statistic-widget-block .range-choice').find('a').addClass('disabled');
            setTimeout(function () {
                if (blocker) {
                    $('body').find('.pevnev-statistic-widget-block .no-found').fadeOut();
                    $('body').find('.pevnev-statistic-widget-block').find('.canvas-chart').css('opacity', '.5').css('pointer-events', 'none');
                }
            }, 500);
            setTimeout(function () {
                if (blocker) {
                    waitingCircle.fadeIn();
                }
            }, 1500);
        },
        complete: function () {
            $('body').find('.pevnev-statistic-widget-block .range-choice').find('a').removeClass('disabled');
            $('body').find('.pevnev-statistic-widget-block').find('.canvas-chart').css('opacity', '').css('pointer-events', '');
            waitingCircle.fadeOut();
            blocker = false;
        },
        error: function () {
        }
    });
    return false;
});