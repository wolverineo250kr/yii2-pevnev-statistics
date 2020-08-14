var canvas = document.getElementById("citiesPie");
var ctxQ = canvas.getContext('2d');

// Global Options:
Chart.defaults.global.defaultFontColor = 'black';
Chart.defaults.global.defaultFontSize = 16;

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

var data = {
    labels: ["She returns it ", "She keeps it"],

    datasets: [
        {
            fill: true,
            backgroundColor: [getRandomColor(), getRandomColor()],
            data: [5, 95],
            borderColor: ['black', 'black'],
            borderWidth: [0, 0]
        }
    ]
};

function hoverColorize(ctx) {
    return colorize(false, true, ctx);
}
// Notice the rotation from the documentation.

var options = {
    title: {
        display: false,
        text: 'Посещаемость по городам',
        position: 'top'
    },
    legend: {
        display: false,
    },
    animation: {
        animateScale: true,
        animateRotate: true
    },
    rotation: -0.5 * Math.PI
};

setTimeout(function () { 
    $('body').find('.pevnev-statistic-cities-widget-block').find('#CityStatisticsToday').trigger('click'); 
}, 1000);

function splitTo(arr, n) {
    var plen = Math.ceil(arr.length / n);

    return arr.reduce(function (p, c, i, a) {
        if (i % plen === 0)
            p.push({});
        p[p.length - 1][i] = c;
        return p;
    }, []);
}

$('body').on('click', '.pevnev-statistic-cities-widget-block .range-choice > a', function () {
    $('.pevnev-statistic-cities-widget-block .range-choice').find('a').removeClass('active');
    $(this).addClass('active');
    $('#statisticcitywidgetform-datestart').val($(this).attr('data-start'));
    $('#statisticcitywidgetform-dateend').val($(this).attr('data-end'));
    $('#citiesStatisticWidgetForm').submit();
    return false;
});

var counterCities = 0;
var blockerCities = false;

$('#citiesStatisticWidgetForm').on('beforeSubmit', function (e) {
    e.preventDefault();
    var waitingCircle = $('body').find('.pevnev-statistic-cities-widget-block .fa-circle-o-notch');
    $.ajax({
        url: '/statistics/cities-pie',
        type: 'POST',
        data: $(this).serialize(),
        success: function (data) {
            var datasetz = [];
            var cityNames = [];

            var step = 1;

     // if (data.length > 20) {
    //        step = 2;
     //     }
       //     if (data.length > 200) {
       //         step = 4;
         //   }
       //     if (data.length > 500) {
         //       step = 8;
        //    }

            var dataSliced = splitTo(data, step);

            $.each(dataSliced, function (dataSlicedIndex, dataSlicedItem) {
                var visits = [];
                var colorz = [];
                $.each(dataSlicedItem, function (index, item) {
                    cityNames.push(item.cityName);


                    visits.push(item.visits);
                    colorz.push(getRandomColor());
                });

                datasetz.push({
                    fill: true,
                    backgroundColor: colorz,
                    data: visits,
                    borderWidth: 0
                });
            });

            var data = {
                labels: cityNames,

                datasets: datasetz
            };

            if (data.datasets.length === 0) {
                $('body').find('.pevnev-statistic-cities-widget-block .no-found').fadeIn();
                if (counterCities > 0) {
                    window.orderCityFormLog.destroy();
                    counterCities = 0;
                }
            } else {
                $('body').find('.pevnev-statistic-cities-widget-block .no-found').fadeOut();
                if (counterCities === 0) {
                    window.orderCityFormLog = new Chart(ctxQ, {
                        type: 'doughnut',
                        data: data,
                        options: options
                    });
                    counterCities = 1;
                } else {
                    window.orderCityFormLog.update();
                }
            }

        },
        beforeSend: function () {
            if (blockerCities) {
                return false;
            } else {
                blockerCities = true;
            }

            $('body').find('.pevnev-statistic-cities-widget-block .range-choice').find('a').addClass('disabled');
            setTimeout(function () {
                if (blockerCities) {
                    $('body').find('.pevnev-statistic-cities-widget-block .no-found').fadeOut();
                    $('body').find('.pevnev-statistic-cities-widget-block').find('canvas').css('opacity', '.5').css('pointer-events', 'none');
                }
            }, 500);
            setTimeout(function () {
                if (blockerCities) {
                    waitingCircle.fadeIn();
                }
            }, 1500);
        },
        complete: function () {
            $('body').find('.pevnev-statistic-cities-widget-block .range-choice').find('a').removeClass('disabled');
            $('body').find('.pevnev-statistic-cities-widget-block').find('canvas').css('opacity', '').css('pointer-events', '');
            waitingCircle.fadeOut();
            blockerCities = false;
        },
        error: function () {
        }
    });
    return false;
});