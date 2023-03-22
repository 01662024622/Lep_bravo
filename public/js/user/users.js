$('#birth_day').datepicker({
    weekStart: 1,
    daysOfWeekHighlighted: "6,0",
    autoclose: true,
    todayHighlight: true,
});


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
        url: "/api/v1/users/table",
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
        {data: 'DT_RowIndex', name: 'DT_RowIndex'},
        {data: 'name', name: 'name'},
        {data: 'email', name: 'email'},
        {data: 'phone', name: 'phone'},
        {data: 'role', name: 'role'},
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
//____________________________________________________________________________________________________

//____________________________________________________________________________________________________
$("#add-form").submit(function (e) {
    e.preventDefault();
}).validate({
    rules: {
        name: {
            required: true,
            minlength: 5
        },
    },
    messages: {
        name: {
            required: "Hãy nhập họ và tên của bạn",
            minlength: "Họ và tên ít nhất phải có 5 kí tự"
        },

    },
    submitHandler: function (form) {

        $('#load_page').show()
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
                dataTable.ajax.reload(null, false);
                $("#add-modal").modal('toggle');
                $('#load_page').hide()
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
            },
        });
    }
});


// get data for form update
function getInfo(id) {
    // $('#editPost').modal('show');

    $('#load_page').show()
    $.ajax({
        type: "GET",
        url: "/users/" + id,
        success: function (response) {
            $('#name').val(response.name);
            $('#position').val(response.position);
            $('#apartment_id').val(response.apartment_id);
            $('#location').val(response.location);
            $('#skype').val(response.skype);
            $('#email_htauto').val(response.email_htauto);
            $('#phone').val(response.phone);
            $('#birth_day').val(response.birth_day);
            $('#eid').val(response.id);
            $('#load_page').hide()
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
            title: "Bạn muốn xóa bỏ người dùng?",
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
                $('#load_page').show()
                $.ajax({
                    type: "delete",
                    url: "users/" + id,
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Thành công!');
                            $('#data-' + id).remove();
                        }
                        $('#load_page').hide()
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
    $('#birth_day').datepicker("setDate", new Date());
}


function changeStatus(id) {
    swal({
            title: "Bạn thực sự muốn thay đổi quyền của người dùng?",
            // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            cancelButtonText: "Không",
            confirmButtonText: "Có",
            // closeOnConfirm: false,
        },
        function (isConfirm) {
            $('#load_page').show()
            if (isConfirm) {
                $.ajax({
                    type: "post",
                    url: "/api/status/users/" + id,
                    data: {
                        role: $('#role_' + id).val()
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Thay đổi thành công!');
                            $('#load_page').hide()
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        toastr.error(xhr.responseJSON.message);
                        dataTable.ajax.reload(null, false);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
            }
        });
};

function getAuthen(mess) {
    alert(mess)
}
