$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//____________________________________________________________________________________________________
var dataTable = $('#users-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        type: "GET",
        url: "/api/v1/report/market/table",
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
        }
    },
    columns: [
        {data: 'date_work', name: 'date_work'},
        {data: 'name_follow', name: 'name_follow'},
        {data: 'advisory', name: 'advisory'},
        {data: 'feedback', name: 'feedback'},
        {data: 'dev_plan', name: 'dev_plan'},
        {data: 'type', name: 'type'},
        {data: 'scale', name: 'scale'},
        {data: 'service', name: 'service'},
        {data: 'type_market', name: 'type_market'},
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
});

$("#add-form").submit(function (e) {
    e.preventDefault();
}).validate({
    rules: {
        advisory: {
            required: true,
        }
    },
    messages: {
        advisory: {
            required: "Hãy nhập thông tin",
        }
    },
    submitHandler: function (form) {
        var formData = new FormData(form);
        if ($('#eid').val() == '') {
            formData.delete('id');
        }

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
                    if ($('#eid').val() == '') {
                        toastr.success('Thêm mới thành công!');
                    } else {
                        toastr.success('Cập nhật thành công!');
                    }
                }, 1000);
                $("#add-modal").modal('toggle');
                dataTable.ajax.reload(null, false);
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(xhr.responseJSON.message);
            },
        });
    }
});


// get data for form update
function getInfo(id) {
    console.log(id);
    // $('#editPost').modal('show');
    $.ajax({
        type: "GET",
        url: "/report/market/" + id,
        success: function (response) {
            $('#date_work').val(response.date_work);
            $('#customer_id').val(response.customer_id);
            $('#advisory').val(response.advisory);
            $('#feedback').val(response.feedback);
            $('#feedback_other').val(response.feedback_other);
            $('#dev_plan').val(response.dev_plan);
            $('#type').val(response.type);
            $('#scale').val(response.scale);
            $('#service').val(response.service);
            $('#type_market').val(response.type_market);
            if (response.image_1 != "" && response.image_1 != null) {
                $('.labelimage_1').css("display", "inline-block");
            }
            if (response.image_2 != "" && response.image_2 != null) {
                $('.labelimage_2').css("display", "inline-block");
            }
            if (response.image_3 != "" && response.image_3 != null) {
                $('.labelimage_3').css("display", "inline-block");
            }
            $('#eid').val(response.id);
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
}


//____________________________________________________________________________________________________

//____________________________________________________________________________________________________
// Update function
// Delete function
function alDelete(id) {
    swal({
            title: "Bạn chắc muốn xóa bỏ?",
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
                    url: "/report/market/" + id,
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Đã xóa!');
                            $('#data-' + id).remove();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        toastr.error(thrownError);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
            }
        });
};


function clearForm() {
    $('#add-form')[0].reset();
    $('#eid').val('');
    $('#date_work').datepicker("setDate", new Date());
}

$("#labelfile").click(function () {
    $("#image_1").trigger('click');
});
