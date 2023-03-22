$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var date = new Date();
var today = date.getDate() + '/' + (date.getMonth() + 1) + '/' + date.getFullYear()
var year = date.getFullYear()
var targets = []
//save all level of target active for user group by td_id
var tdIdLevelBuffer = []
//save all level and result of kpi [month][td_id][level, result]
var kpiIdLevel = []
var yearActive=year.toString();
var monthActive = date.getMonth() + 1;
function firstLoad(year=0) {
    if (year>0) yearActive=year
    $.ajax({
        type: "GET",
        url: "/api/v1/user/targets/table?year=" + yearActive,
        success: function (json) {
            targets = []
            tdIdLevelBuffer = []
            kpiIdLevel = []
            var html = '';
            json.data.forEach(element => {
                targets.push(element['td_id'])
                if (tdIdLevelBuffer[element['td_id']] === undefined) tdIdLevelBuffer[element['td_id']] = []
                tdIdLevelBuffer[element['td_id']].push(element['level']);
                html = html + generateTarget(element);
            })
            $('#detail-table-all-body').html(html)
            loadResult(targets)
        },
        error: function (xhr, ajaxOptions, thrownError) {
            page.hide()
            toastr.error(thrownError);
        }
    });
}

firstLoad()

var loadResult = function (kpi, month = 0) {
    page.show()
    var kpiUrl = '';
    if (kpi.length > 0) {
        kpiUrl = '&kpis[]=' + kpi.join('&kpis[]=');
    }
    if (month > 0) {
        monthActive = month;
    }

    $.ajax({
        type: "get",
        url: "/api/v1/targets/kpis/table?year=" + yearActive + "&month=" + monthActive + kpiUrl,
        success: function (res) {
            $('.number-kpi-year').text('0');
            $('.total-kpi-month').text('0%');
            $('.kpi-moth-detail').removeClass('kpi-month-hover');
            $('.kpi-moth-detail').removeClass('kpi-active-month');
            $('.kpi-moth-detail').data('status','disabled');
            $('#kpi-year-'+yearActive).addClass('kpi-active-month')
            $('#kpi-month-detail-'+monthActive).addClass('kpi-active-month')
            if (!res) {
                page.hide();
                return
            }
            res.data.forEach(element => {
                generateKpiAllMonth(element)
            })

            res.detail.forEach(element => {
                $('#target-detail-container-'+element.td_id).removeClass('hidden')
                $('#list-kpi-'+element.td_id).append(generateKpi(element))

            })
            var totalYear = 0;
            var totalKpiYear = 0;
            var numberkpiLabel=[];
            var percentkpiLabel=[];
            for (var month = 1; month <= kpiIdLevel.length; month++) {
                if (kpiIdLevel[month] === undefined) continue;
                //total level of tagert on month
                var totalLevelTg = 0

                //total number kpi of month
                var sumKpi = kpiIdLevel[month].reduce(function (total, accumulator, currentIndex) {
                    totalLevelTg = totalLevelTg + floatParse(tdIdLevelBuffer[currentIndex]);
                    return total + accumulator.length;
                }, 0)
                totalKpiYear = totalKpiYear + sumKpi;
                $('#number-kpi-month-' + month).text(sumKpi)
                $('#kpi-month-detail-' + month).addClass('kpi-month-hover').data('status','active')
                numberkpiLabel.push(sumKpi)
                var generalMonth = []
                for (var td_id = 1; td_id <= kpiIdLevel[month].length; td_id++) {
                    if (kpiIdLevel[month][td_id] === undefined) continue;
                    var sumLevelTd = kpiIdLevel[month][td_id].reduce(function (total, accumulator) {
                        return total + floatParse(accumulator[0])+floatParse(accumulator[1]);
                    }, 0.00)
                    generalMonth[td_id] = kpiIdLevel[month][td_id].reduce(function (total, accumulator) {
                        return total + (floatParse(accumulator[0])+ floatParse(accumulator[1])) * floatParse(accumulator[2]) / sumLevelTd;
                    }, 0.00)
                }
                var resultMonth = generalMonth.reduce(function (total, accumulator, currentIndex) {
                    return total + floatParse(tdIdLevelBuffer[currentIndex]) * floatParse(accumulator) / totalLevelTg;
                }, 0.00)
                totalYear = totalYear + floatParse(resultMonth);
                $('#total-kpi-month-' + month).text(floatParse(resultMonth) + '%')
                percentkpiLabel.push(floatParse(resultMonth))

            }
            totalYear = floatParse(totalYear) / floatParse(kpiIdLevel.length - 1);
            $('#number-kpi-year-2021').text(totalKpiYear)
            $('#total-kpi-year-2021').text(totalYear + '%')
            var ctx = document.getElementById('myChart').getContext('2d');
            var chart = new Chart(ctx, {
                // The type of chart we want to create
                type: 'bar',

                // The data for our dataset
                data: {
                    labels: ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T0','T11','T12',],
                    datasets: [{
                        label: 'Số lượng kpi',
                        data: numberkpiLabel,
                        yAxisID: 'B',
                        type: 'line',
                        borderColor: "rgb(237, 142, 7)",
                        order: 0,
                    },{
                        label: 'Phần trăm đạt',
                        yAxisID: 'A',
                        data: percentkpiLabel,
                        backgroundColor: '#000066',
                        order: 1
                    }],
                },

                // Configuration options go here
                options: {
                    scales: {
                        yAxes: [{
                            id: 'A',
                            type: 'linear',
                            position: 'left',
                            ticks: {
                                suggestedMin: 0,
                                suggestedMax: 100
                            }
                        }, {
                            id: 'B',
                            type: 'linear',
                            position: 'right',
                            ticks: {
                                suggestedMin: 0,
                            }
                        }]
                    },
                    onClick: function (evt, item) {
                        console.log('legend onClick', evt);
                        console.log('legd item', item);
                    }
                }
            });
            page.hide()

        },
        error: function (xhr, ajaxOptions, thrownError) {
            page.hide()
            toastr.error(thrownError);
        }
    });
}

var generateTarget = function (element) {
    return `<tr id="target-detail-container-` + element.td_id + `" class="hidden">
                <td>` + element.name + `</td>
                <td>` + element.levelEdit + `</td>
                <td id="list-kpi-` + element.td_id + `" class="list-kpi">
                </td>
            </tr>`;
}
var generateKpi = function (element) {
    return `<div class="row collum-bottom">
                        <div class="col-8">
                            ` + element.name + `
                        </div>
                        <div class="col-1">
                           ` + element.levelEdit + `
                        </div>
                        <div class="col-1">
                           ` + element.timeEdit + `
                        </div>
                        <div class="col-2">
                            ` + element.resultEdit + `
                        </div>
                    </div>`;
}
function generateKpiAllMonth(element)
{
    element.results.forEach(ele => {
        if (kpiIdLevel[ele.month] === undefined) kpiIdLevel[ele.month] = []
        if (kpiIdLevel[ele.month][element.td_id] === undefined) kpiIdLevel[ele.month][element.td_id] = []
        kpiIdLevel[ele.month][element.td_id].push([element.level,element.time, ele.result]);

    })
}


function floatParse(float, fractionDigits = 2) {
    return parseFloat(parseFloat(float).toFixed(fractionDigits));
}


function setResultMothKpi(id) {
    page.show()
    $.ajax({
        type: "GET",
        url: "/results/" + id,
        success: function (res) {
            showDetailKpi(res)
            $('#eid-krs').val(res.id)
            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            page.hide()
            toastr.error(thrownError);
        }
    });
}

$('#user_id').on('change', function () {
    targetTable.ajax.url("/api/v1/targets/table?user_id=" + $('#user_id').val() + "&year=" + $('#date').val()).load()
})
$('#date').on('change', function () {
    targetTable.ajax.url("/api/v1/targets/table?user_id=" + $('#user_id').val() + "&year=" + $('#date').val()).load()
})

function showDetailKpi(res) {
    $('#name-kpi').text(res.name)
    $('#detail-kpi-show').html(`<b for="name">Điểm: </b>` + res.levelEdit +
        ` | <b for="name">Tháng: </b><span id="kpi-detail-month">` + res.month + `</span>` +
        ` | <b for="name">Loại: </b>` + res.typeEdit
    )
    $('#result-kpi-detail').val(res.result)
    if (res.type == 0) {
        $('#modal-set-width').css('max-width', '750px')
        $('#detail-container-modal').removeClass('col-4').addClass('col-12')
        $('#result-container-modal').hide()
        $('#result-kpi-detail').prop('disabled', false)
    } else {
        $('#modal-set-width').css('max-width', '1200px')
        $('#detail-container-modal').addClass('col-4').removeClass('col-12')
        $('#result-container-modal').show()
        $('#result-kpi-detail').prop('disabled', true)
        var html = '<thead><tr><th>ID</th><th>Ngày vi phạm</th><th>Mô tả</th><th>Số lần</th><th>Hành Động</th>' +
            '</tr></thead><tbody>';
        res.result_details.forEach(function (element, index) {
            html = html + `<tr id="result-detail-col-` + index + `" role="row" class="odd"><td>` + (index + 1) + `</td><td>` + element.date + `</td><td>` + element.description + `</td><td>` + element.number + `</td><td>
<button type="button" class="btn btn-xs btn-danger" onclick="alDeleteResult(` + element.id + `)">
<i class="fa fa-trash" aria-hidden="true"></i></button>
</td></tr>`
        })
        html = html + '</tbody>';
        $('#results-table').html(html)

    }
}

function changeMonth(month){
    if ($('#kpi-month-detail-'+month).data('status')==='disabled') return
    monthActive=month
    firstLoad()
}
function changeTypeShow(){
    $('.analytics').toggleClass('hidden')
}
