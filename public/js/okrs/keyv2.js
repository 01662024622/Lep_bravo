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
$('#date').datepicker({
    startView: "years",
    minViewMode: "years",
    maxViewMode: "years",
    format: "yyyy",
    startDate: "2020",
    endDate: new Date(),
    autoClose: true,
}).on('changeMonth', function (e) {
    $(e.currentTarget).data('datepicker').hide();
});
$('#result-date').datepicker({
    format: "dd/mm/yyyy",
    startDate: "01/01/2021",
    endDate: new Date(),
    autoClose: true,
    todayHighlight: true,
}).datepicker('setDate', date);

$('#date').datepicker('setDate', year.toString())
$('#result-date').datepicker('setDate', today)

$('.collapsed').on('click', function () {
    $(this).toggleClass('fa-caret-right fa-caret-down')
})
var targetTable = $('#target-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: "GET",
            url: "/api/v1/targets/table?user_id=" + $('#user_id').val() + "&year=" + $('#date').val(),
            error: function (xhr, ajaxOptions, thrownError) {
                if (xhr != null) {
                    if (xhr.responseJSON != null) {
                        if (xhr.responseJSON.errors != null) {
                            if (xhr.responseJSON.errors.permission != null) {
                                location.reload();
                            }
                        }
                    }
                }
            }, dataSrc: function (json) {
                targets = []
                tdIdLevelBuffer = []
                kpiIdLevel = []
                $('#target-table').css('width', '100%')
                var html = '';
                var targetHtml = ''
                json.data.forEach(element => {
                    html = html + `<div class="row">
                            <div class="col-10">` + element['name'] + `</div>
                            <div class="col-2">`;
                    if (element['user_id'] == $('#user_id').val()) {
                        targetHtml = targetHtml + generateTarget(element)
                        targets.push(element['td_id'])
                        if (tdIdLevelBuffer[element['td_id']] === undefined) tdIdLevelBuffer[element['td_id']] = []
                        tdIdLevelBuffer[element['td_id']].push(element['level']);
                        html = html + `<input type="checkbox" name="data[]" class="form-check-input" value="` + element['id'] + `" checked></div>
            </div>`;
                    } else html = html + `<input type="checkbox" name="data[]" class="form-check-input" value="` + element['id'] + `"></div>
            </div>`;
                })
                $('#kpi-target').html(html)
                loadResult(targets)
                $('#kpi').html(targetHtml)
                $('.collapsed').on('click', function () {
                    $(this).toggleClass('fa-caret-right fa-caret-down')
                })
                $('input:radio[name=type]').change(function () {
                    $(this).parent().parent().find('.minus-container').toggleClass('hidden')
                })
                $('#user-detail-kpi').html('<i class="fa fa-caret-right" aria-hidden="true"></i> KPI năm '+$('#date').val()+' - '+json.user.name)
                return json.data;
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'name', name: 'name'},
            {data: 'levelEdit', name: 'levelEdit'},
            {data: 'action', name: 'action'},
        ],
        oLanguage: {
            "sProcessing": "Đang xử lý...",
            "sLengthMenu": "Xem _MENU_ mục",
            "sZeroRecords": "Không tìm thấy dòng nào phù hợp",
            "sInfo": "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
            "sInfoEmpty": "Đang xem 0 đến 0 trong tổng số 0 mục",
            "sInfoFiltered": "(được lọc từ _MAX_ mục)",
            "sInfoPostFix": "",
            "sSearch": "Tìm Kiếm: ",
            "sUrl": "",
            "oPaginate": {
                "sFirst": " Đầu ",
                "sPrevious": " Trước ",
                "sNext": " Tiếp ",
                "sLast": " Cuối "
            }
        }

    })
;

$("#target-form").submit(function (e) {
    e.preventDefault();
    page.show()
}).validate({
    rules: {
        level: {
            required: true
        },
        name: {
            required: true
        },
    },
    messages: {
        level: {
            required: "Dữ liệu không hợp lệ"
        },
        name: {
            required: 'Bạn chưa nhập dữ liệu'
        },
    },
    submitHandler: function (form) {
        var formData = new FormData(form);
        formData.append('year', $('#date').val());
        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            dataType: 'json',
            async: false,
            processData: false,
            contentType: false,
            success: function (response) {
                setTimeout(function () {
                    toastr.success('Thêm mới mục tiêu thành công!');
                }, 1000);
                $('#target-form')[0].reset();
                targetTable.ajax.reload()
            }, error: function (xhr, ajaxOptions, thrownError) {
                page.hide()
                if (xhr != null) {
                    if (xhr.responseJSON != null) {
                        if (xhr.responseJSON.message != null) {
                            toastr.error(xhr.responseJSON.message);
                        }
                    }
                }

            },
        });
    }
});

$("#target-kpi-form").submit(function (e) {
    e.preventDefault();
    page.show()
}).validate({
    submitHandler: function (form) {
        var formData = new FormData(form);
        formData.append('year', $('#date').val());
        formData.append('user_id', $('#user_id').val());
        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            dataType: 'json',
            async: false,
            processData: false,
            contentType: false,
            success: function (response) {
                targetTable.ajax.reload()
                setTimeout(function () {
                    toastr.success('Cập nhật danh sách mục tiêu thành công!');
                }, 1000);
                page.hide()
            }, error: function (xhr, ajaxOptions, thrownError) {
                page.hide()
                if (xhr != null) {
                    if (xhr.responseJSON != null) {
                        if (xhr.responseJSON.message != null) {
                            toastr.error(xhr.responseJSON.message);
                        }
                    }
                }

            },
        });
    }
});
$("#result-detail-form").submit(function (e) {
    e.preventDefault();
    page.show()
}).validate({
    rules: {
        number: {
            required: true,
            number: true,
            min: 0,
            max: 10
        },
    },
    messages: {
        number: {
            required: 'Bạn chưa nhập dữ liệu',
            number: 'Bạn chưa nhập dữ liệu',
            min: 'Dữ liệu không hợp lệ',
            max: 'Dữ liệu không hợp lệ'
        },
    },
    submitHandler: function (form) {
        page.show()
        var formData = new FormData(form);
        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            dataType: 'json',
            async: false,
            processData: false,
            contentType: false,
            success: function (response) {
                setTimeout(function () {
                    toastr.success('Thêm mới mục tiêu thành công!');
                }, 1000);
                $('#result-detail-form')[0].reset();
                $('#result-date').datepicker('setDate', today)
                showDetailKpi(response)
                targetTable.ajax.reload()
            }, error: function (xhr, ajaxOptions, thrownError) {
                page.hide()
                if (xhr != null) {
                    if (xhr.responseJSON != null) {
                        if (xhr.responseJSON.message != null) {
                            toastr.error(xhr.responseJSON.message);
                        }
                    }
                }

            },
        });
    }
});


// Delete function
function alDeleteTarget(id) {
    swal({
            title: "Bạn muốn xóa bỏ KRs này?",
            // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Không",
            confirmButtonText: "Có",
            // closeOnConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "delete",
                    url: "/targets/" + id,
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Thành công!');
                            $('#target-' + id).remove();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        page.hide()
                        toastr.error(thrownError);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
            }
        });
}

function alDeleteResult(id) {
    swal({
            title: "Bạn muốn xóa bỏ KRs này?",
            // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Không",
            confirmButtonText: "Có",
            // closeOnConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "delete",
                    url: "/results/" + id,
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Thành công!');
                        }
                        $('#result-detail-col-' + id).remove();
                        showDetailKpi(res)
                        targetTable.ajax.reload();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        page.hide()
                        toastr.error(thrownError);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
            }
        });
}

var loadResult = function (kpi) {
    page.show()
    var kpiUrl = '';
    if (kpi.length > 0) {
        kpiUrl = '&kpis[]=' + kpi.join('&kpis[]=');
    }
    $.ajax({
        type: "get",
        url: "/api/v1/targets/kpis/table?user_id=" + $('#user_id').val() + "&year=" + $('#date').val() + kpiUrl,
        success: function (res) {
            $('.number-kpi-year').text('0');
            $('.total-kpi-month').text('0%');
            if (!res) {
                page.hide();
                return
            }
            res.data.forEach(element => {
                $('#collapse-header-' + element['td_id']).after(genarateKpi(element))
            })
            var numberkpiLabel=[];
            var percentkpiLabel=[];
            for (var month = 1; month < kpiIdLevel.length; month++) {
                if (kpiIdLevel[month] === undefined) continue;
                //total level of tagert on month
                var totalLevelTg = 0

                //total number kpi of month
                var sumKpi = kpiIdLevel[month].reduce(function (total, accumulator, currentIndex) {
                    totalLevelTg = totalLevelTg + floatParse(tdIdLevelBuffer[currentIndex]);
                    return total + accumulator.length;
                }, 0)
                $('#number-kpi-month-' + month).text(sumKpi)
                numberkpiLabel.push(sumKpi)
                var generalMonth = []
                for (var td_id = 1; td_id <= kpiIdLevel[month].length; td_id++) {
                    if (kpiIdLevel[month][td_id] === undefined) continue;
                    var sumLevelTd = kpiIdLevel[month][td_id].reduce(function (total, accumulator) {
                        return total + floatParse(accumulator[0])+ floatParse(accumulator[1]);
                    }, 0.00)
                    generalMonth[td_id] = kpiIdLevel[month][td_id].reduce(function (total, accumulator) {
                        return total + (floatParse(accumulator[0])+ floatParse(accumulator[1])) * floatParse(accumulator[2]) / sumLevelTd;
                    }, 0.00)
                }
                var resultMonth = generalMonth.reduce(function (total, accumulator, currentIndex) {
                    return total + floatParse(tdIdLevelBuffer[currentIndex]) * floatParse(accumulator) / totalLevelTg;
                }, 0.00)
                $('#total-kpi-month-' + month).text(floatParse(resultMonth) + '%')
                percentkpiLabel.push(floatParse(resultMonth))

            }
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
    var months = []
    for (var i = 1; i <= date.getMonth() + 1; i++) {
        months[i] = `<span class="col kpi-month text-center">T` + i + `</span>`;
    }
    return `<div class="col-12 box-kpi">
            <div class="kpi-header row">
                <div class="col-10">
                    <i class="fa fa-caret-down collapsed" aria-hidden="true" data-toggle="collapse" data-target="#collapse-` + element['td_id'] + `"></i>
                    ` + element['name'] + ` - Mức độ quan trọng: <b>` + element['levelEdit'] + `</b>
                </div>
                <div class="col-2 text-right"><button type="button" class="btn btn-sm btn-link text-right" data-toggle="collapse" onclick="cancelKpi(` + element['id'] + `)" data-target="#collapse-action-` + element['id'] + `">Cấu hình <i class="fa fa-angle-down" aria-hidden="true"></i></button></div>

            </div>
            <div id="collapse-` + element['td_id'] + `" class="kpi-body collapse show">
                 <div id="add-kpi-container-` + element['td_id'] + `" class="hidden">
                    <form class="kpis-form row col-12" id="kpis-form-` + element['td_id'] + `" method="POST" action="/kpis">
                        <div class="form-check col-4">
                            <input type="text" class="form-control form-control-sm" name="name" placeholder="Tên kpi...">
                        </div>
                        <div class="form-check col-2">
                            <select id="level" class="form-control form-control-sm" name="level">
                                <option disabled selected value="">-- Điểm --</option>
                                <option value="2">5 Điểm</option>
                                <option value="4">10 Điểm</option>
                                <option value="6">15 Điểm</option>
                                <option value="8">20 Điểm</option>
                            </select>
                        </div>
                        <div class="form-check col-2">
                            <select id="time" class="form-control form-control-sm" name="time">
                                <option disabled selected value="">-- Time --</option>
                                <option value="2">5 Điểm</option>
                                <option value="4">10 Điểm</option>
                                <option value="6">15 Điểm</option>
                                <option value="8">20 Điểm</option>
                            </select>
                        </div>
                        <div class="col-4">
                            <div class="form-check form-check-inline">
                                <input id="type-default-` + element['td_id'] + `" class="form-check-input type-radio" type="radio" name="type" value="0" checked>
                                <label class="form-check-label" for="inlineRadio1">%đạt</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input type-radio" type="radio" name="type" value="1">
                                <label class="form-check-label" for="inlineRadio2">trừ theo lỗi</label>
                            </div>
                            <div id="minus-container-` + element['td_id'] + `" class="form-check form-check-inline hidden minus-container" >
                            <select id="level" class="form-control form-control-sm" name="minus">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                                <option value="25">25</option>
                                <option value="30">30</option>
                                <option value="40">40</option>
                                <option value="50">50</option>
                            </select>
                                <label class="form-check-label" for="inlineRadio2">%</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <button type="button" class="btn btn-sm btn-link" onclick="submitKpi(` + element['td_id'] + `)">Thêm</button>
                                <button type="button" class="btn btn-sm btn-link " onclick="cancelKpi(` + element['td_id'] + `,true)">Hủy </button>
                            </div>
                            <input type="hidden" name="td_id" value="` + element['td_id'] + `">
                        </div>

                    </form>
                </div>
                <form action="GET" method="/kpis/create" id="target-kpi-form-` + element['id'] + `">
                <div id="collapse-action-` + element['id'] + `" class="collapse">
                    <button type="button" class="btn btn-sm btn-link btn-kpi-action show" onclick="addKpi(` + element['td_id'] + `)" id="add-kpi-` + element['td_id'] + `" data-toggle="modal" data-target="#add-modal">Thêm mới</button>
                    <button type="button" class="btn btn-sm btn-link btn-kpi-action show" onclick="selectCheckboxKpi(` + element['td_id'] + `)" id="select-kpi-` + element['td_id'] + `">Chọn</button>
                    <button type="button" class="btn btn-sm btn-link btn-kpi-action" onclick="selectAllCheckboxKpi(` + element['td_id'] + `)" id="select-all-kpi-` + element['td_id'] + `">Chọn Tất cả</button>
                    <button type="button" class="btn btn-sm btn-link btn-kpi-action" onclick="unSelectAllCheckboxKpi(` + element['td_id'] + `)" id="un-select-all-kpi-` + element['td_id'] + `">Bỏ chọn Tất cả</button>
                    <button type="button" class="btn btn-sm btn-link btn-kpi-action" onclick="removeKpi(` + element['td_id'] + `)" id="remove-kpi-` + element['td_id'] + `">Xóa</button>
                    <button type="button" class="btn btn-sm btn-link btn-kpi-action" onclick="cancelKpi(` + element['td_id'] + `)" id="cancel-kpi-` + element['td_id'] + `">Hủy </button>
                </div>

                <div id="collapse-header-` + element['td_id'] + `"class="row kpi-detail kpi-header-title">
                    <div class="col-5 text-bold row">
                        <div class="col-10">KPI</div>
                        <div class="col-1  text-center">Điểm</div>
                        <div class="col-1  text-center">Time</div>
                    </div>
                    <div class="col-1 text-bold">Loại</div>
                    <div class="col-6 text-bold row">
                        ` + months.join('') + `
                    </div>
                </div>
        </form>
            </div>
    </div>`;
}
var genarateKpi = function (element) {
    var monthLoop = date.getMonth()
    if (parseInt($('#date').val()) < parseInt(year)) monthLoop = 11;
    var results = []
    for (var i = 1; i <= monthLoop + 1; i++) {
        results[i] = `<span class="col kpi-month kpi-hover text-center kpi-hover-item-`
            + element['td_id'] + `" onclick="activeResult(` + element['id'] + `,` + i + `)">--</span>`;
    }
    if (element.results.length > 0) {
        element.results.forEach(ele => {
            if (kpiIdLevel[ele.month] === undefined) kpiIdLevel[ele.month] = []
            if (kpiIdLevel[ele.month][element.td_id] === undefined) kpiIdLevel[ele.month][element.td_id] = []
            kpiIdLevel[ele.month][element.td_id].push([element.level,element.time, ele.result]);
            results[ele.month] = `<span class="col kpi-month kpi-hover text-center kpi-hover-item-`
                + element['td_id'] + `" onclick="setResultMothKpi(` + ele['id'] + `)"
data-toggle="modal" data-target="#set-result-month-modal"
>` + ele['result'] + `</span>`
        })
    }
    return `<div class="row kpi-detail" id="kpi-detail-` + element['id'] + `">

        <div class="col-5 row">
            <div class="col-10 title-kpi kpi-hover kpi-hover-item-` + element['td_id'] + `"
                 title="` + element['name'] + `">
                 <div class="form-check">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input checkbox-kpi checkbox-kpi-` + element['td_id'] + `" value="` + element['id'] + `" name="kpis[]" onchange="checkboxKpiChange(` + element['td_id'] + `)"> &nbsp;` + element['name'] + `
  </label>
</div>
                </div>
            <div class="col-1 text-center">
                ` + element['levelEdit'] + `
            </div>
            <div class="col-1 text-center">
                ` + element['timeEdit'] + `
            </div>
        </div>
        <div class="col-1">` + element['type'] + `</div>
        <div class="col-6 row">` + results.join('') + `
        </div>
    </div>`;
}

function selectCheckboxKpi(id) {
    $('.kpi-hover-item-' + id).removeClass('kpi-hover')
    $('#add-kpi-' + id).removeClass('show')
    $('#cancel-kpi-' + id).addClass('show')
    // $('#remove-kpi-' + id).removeClass('show')
    $('#select-kpi-' + id).removeClass('show')
    $('#select-all-kpi-' + id).addClass('show')
    $('#un-select-all-kpi-' + id).addClass('show')
    $('.checkbox-kpi-' + id).addClass('show').prop('checked', false)
}

function cancelKpi(id, check = false) {
    $('.kpi-hover-item-' + id).addClass('kpi-hover')
    $('#add-kpi-' + id).addClass('show')
    $('#cancel-kpi-' + id).removeClass('show')
    $('#remove-kpi-' + id).removeClass('show')
    $('#select-kpi-' + id).addClass('show')
    $('#select-all-kpi-' + id).removeClass('show')
    $('#un-select-all-kpi-' + id).removeClass('show')
    $('.checkbox-kpi-' + id).removeClass('show').prop('checked', false)
    $('#add-kpi-container-' + id).addClass('hidden')
    if (check) $('#collapse-action-' + id).addClass('show')
}

// select-kpi-
// select-all-kpi-

function selectAllCheckboxKpi(id) {
    $('.checkbox-kpi-' + id).prop('checked', true)
    checkboxKpiChange(id)
}

function unSelectAllCheckboxKpi(id) {
    $('.checkbox-kpi-' + id).prop('checked', false)
    checkboxKpiChange(id)
}

function checkboxKpiChange(id) {
    if ($('.checkbox-kpi-' + id + ':checkbox:checked').length > 0) {
        $('#remove-kpi-' + id).addClass('show')
    } else {
        $('#remove-kpi-' + id).removeClass('show')
    }
}

function removeKpi(id) {
    page.show()
    $.ajax({
        url: '/kpis/create/?' + $('#target-kpi-form-' + id).serialize(),
        type: 'GET',
        success: function (response) {
            targetTable.ajax.reload()
            setTimeout(function () {
                toastr.success('Xóa thành công!');
            }, 1000);
        }, error: function (xhr, ajaxOptions, thrownError) {
            page.hide()
            if (xhr != null) {
                if (xhr.responseJSON != null) {
                    if (xhr.responseJSON.message != null) {
                        toastr.error(xhr.responseJSON.message);
                    }
                }
            }

        },
    });
}

function activeResult(idKpi, month) {
    swal({
            title: "Bạn chắc chắn muốn kích hoạt kpi?",
            // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Không",
            confirmButtonText: "Có",
            // closeOnConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                page.show()
                $.ajax({
                    type: "POST",
                    url: "/kpi/results",
                    data: {
                        kpi_id: idKpi,
                        month: month,
                        year: $('#date').val(),
                    },
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Kích hoạt hành công!');
                        }
                        targetTable.ajax.reload()
                        // page.hide()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        page.hide()
                        toastr.error(thrownError);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
            }
        });
}


function floatParse(float, fractionDigits = 2) {
    return parseFloat(parseFloat(float).toFixed(fractionDigits));
}

function addKpi(id) {
    $('#minus-container-' + id).addClass('hidden')
    $('#kpis-form-' + id)[0].reset();
    $('#type-default' + id).attr('checked', true)
    $('#add-kpi-container-' + id).removeClass('hidden')
    $('#collapse-action-' + id).removeClass('show')
}

function setValidateFormKpi(id) {
    $("#kpis-form-" + id).submit(function (e) {
        e.preventDefault();
    }).validate({
        rules: {
            level: {
                required: true
            },
            name: {
                required: true
            },
            minus: {
                required: true
            },
        },
        messages: {
            level: {
                required: "Dữ liệu không hợp lệ"
            },
            name: {
                required: 'Bạn chưa nhập dữ liệu'
            },
            minus: {
                required: 'Bạn chưa nhập dữ liệu'
            },
        },
        submitHandler: function (form) {
            page.show()
            var formData = new FormData(form);
            $.ajax({
                url: form.action,
                type: form.method,
                data: formData,
                dataType: 'json',
                async: false,
                processData: false,
                contentType: false,
                success: function (response) {
                    setTimeout(function () {
                        toastr.success('Thêm mới mục tiêu thành công!');
                    }, 1000);
                    addKpi(id)
                    var results = []
                    for (var i = 1; i <= date.getMonth() + 1; i++) {
                        results[i] = `<span class="col kpi-month kpi-hover text-center kpi-hover-item-`
                            + response['td_id'] + `" onclick="activeResult(` + response['id'] + `,` + i + `)">--</span>`;
                    }
                    var html = `<div class="row kpi-detail" id="kpi-detail-` + response['id'] + `">

        <div class="col-5 row">
            <div class="col-10 title-kpi kpi-hover kpi-hover-item-` + response['td_id'] + `"
                 title="` + response['name'] + `">
                 <div class="form-check">
  <label class="form-check-label">
    <input type="checkbox" class="form-check-input checkbox-kpi checkbox-kpi-` + response['td_id'] + `" value="` + response['id'] + `" name="kpis[]" onchange="checkboxKpiChange(` + response['td_id'] + `)"> &nbsp;` + response['name'] + `
  </label>
</div>
                </div>
            <div class="col-1 text-center">
                ` + response['level_edit'] + `
            </div>

            <div class="col-1 text-center">
                ` + response['time_edit'] + `
            </div>
        </div>
        <div class="col-1">` + response['typeEdit'] + `</div>
        <div class="col-6 row">` + results.join('') + `
        </div>
    </div>`;
                    $('#collapse-header-' + id).after(html)
                    // targetTable.ajax.reload()
                    page.hide()
                }, error: function (xhr, ajaxOptions, thrownError) {
                    page.hide()
                    if (xhr != null) {
                        if (xhr.responseJSON != null) {
                            if (xhr.responseJSON.message != null) {
                                toastr.error(xhr.responseJSON.message);
                            }
                        }
                    }

                },
            });
        }
    })
}

function submitKpi(id) {
    setValidateFormKpi(id)
    $('#kpis-form-' + id).submit();
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

function saveResult() {
    $.ajax({
        type: "POST",
        url: "/kpi/results",
        data: {
            id: $('#eid-krs').val(),
            result: $('#result-kpi-detail').val()
        },
        success: function (res) {
            if (!res.error) {
                toastr.success('Thay đổi thành công!');
            }
            $('#set-result-month-modal').modal('hide');
            targetTable.ajax.reload()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            page.hide()
            toastr.error(thrownError);
        }
    })
}

function removeResult() {
    swal({
            title: "Bạn muốn xóa bỏ kết quả này?",
            // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Không",
            confirmButtonText: "Có",
            // closeOnConfirm: false,
        },
        function (isConfirm) {
            if (isConfirm) {
                $.ajax({
                    type: "delete",
                    url: "/kpi/results/" + $('#eid-krs').val(),
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Thành công!');
                            $('#set-result-month-modal').modal('hide');
                            targetTable.ajax.reload()
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        page.hide()
                        toastr.error(thrownError);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
            }
        });
}

function changeTypeShow(){
    $('.analytics').toggleClass('hidden')
}
function analytics(){
    page.show()
    $.ajax({
        type: "GET",
        url: "http://localhost/api/v1/analytic/table?year="+$('#date').val(),
        success: function (res) {
            var html = '';
            res.forEach(function (element,index) {
                html=html+generateAnalytic(index,element)
            });
            $('#analytic-body-all-month').html(html)
            console.log(html)

            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            page.hide()
            toastr.error(thrownError);
        }
    })
}
function generateAnalytic(index,element){
    if(element.results.length===0) return '';
    var result=[]
    for (var i=0; i<=date.getMonth();i++){
        result[i]='--';
    }
    element.results.forEach(value=> {
        result[(value.month-1)]=`<div class="progress">
                            <div class="progress-bar" role="progressbar" aria-valuenow="`+ value.result +`" aria-valuemin="0" aria-valuemax="100" style="width:`+ value.result +`%; background-color:rgba(0, 0, 102, `+(value.result/100)+`);">
                              `+ value.result +`%
                            </div>
                          </div>`;
        console.log(value)
    });
    var monthEle=''
    result.forEach(value=> {
        monthEle=monthEle+'<td>'+value+'</td>'
    });
    return `<tr>
    <td>`+index+`</td>
    <td>`+element.name+`</td>
    <td>`+element.target+`</td>
    <td>`+element.target_level_edit+`</td>
    <td>`+element.kpi+`</td>
    <td>`+element.kpi_level_edit+`</td>
    <td>`+element.kpi_time_edit+`</td>
    `+monthEle+`
    </tr>`
}
for (var i=0; i<=date.getMonth();i++){
    $('#analytic-all-month').append('<th>T'+(i+1)+'</th>')
}

