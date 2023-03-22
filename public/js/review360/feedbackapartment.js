$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


var dataTable = $('#users-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        type: "GET",
        url: "/api/v1/report/review/feedback/apartment/table",
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
        {data: 'DT_RowIndex', name: 'id'},
        {data: 'type', name: 'type'},
        {data: 'user', name: 'user'},
        {data: 'name', name: 'name'},
        {data: 'content', name: 'content'},
        {data: 'image', name: 'image'},
        {data: 'created_at', name: 'created_at'},
        {data: 'confirm', name: 'confirm'},
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
function getInfo(id) {
    $('#eid').val(id);
}

$("#add-form").submit(function (e) {
    e.preventDefault();
}).validate({
    rules: {
        confirm: {
            required: true,
            minlength: 2
        },
    },
    messages: {
        confirm: {
            required: "Hãy nhập thông tin phản hồi",
            minlength: "Thông tin phản hồi ít nhất phải có 5 kí tự"
        },

    },
    submitHandler: function (form) {
        var formData = new FormData(form);
        $.ajax({
            url: "/review/report/"+$('#eid').val()+"/edit?confirm="+$("#confirm").val(),
            type: form.method,
            data: formData,
            dataType: 'json',
            async: false,
            processData: false,
            contentType: false,
            success: function (response) {
                setTimeout(function () {
                    toastr.success('Phản hồi thành công!');
                }, 1000);
                $("#add-modal").modal('toggle');
                dataTable.ajax.reload(null, false);
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
            },
        });
    }
});
function setStatus(id) {
    $('#eid').val(id);
    var status = $("#feedback_"+id).val();
    console.log(id)
    console.log(status)
    if (status==1) {
        swal({
                title: "Bạn chắc chắn chấp thuận phản hồi?",
                // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                cancelButtonText: "Không",
                confirmButtonText: "Có",
                // closeOnConfirm: false,
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        type: "get",
                        url: "/review/report/"+id+"/edit",
                        success: function(res)
                        {
                            if(!res.error) {
                                toastr.success('Thành công!');
                            }
                            dataTable.ajax.reload(null, false);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            toastr.error(thrownError);
                        }
                    });
                } else {
                    toastr.error("Hủy bỏ thao tác!");
                    $("#feedback_" + id).val(0);
                }
            });
    }
    if (status==-1) {
        $('#add-modal').modal('show');
        $("#feedback_" + id).val(0);
    }
}
