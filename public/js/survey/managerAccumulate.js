$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var status= 0;
var dataTable = $('#users-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        type: "GET",
        url: "/api/v1/manager/accumulate/table?role_pt="+role,
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
        {data: 'code', name: 'code'},
        {data: 'name', name: 'name'},
        {data: 'role_pt', name: 'role_pt'},
        {data: 'phone', name: 'phone'},
        {data: 'birthday', name: 'birthday'},
        {data: 'level', name: 'level'},
        {data: 'wb', name: 'wb'},
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
function wb(id){
    swal({
            title: "Bạn chắc chắn sẽ gửi quà welcombox?",
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
                page.show()
                $.ajax({
                    type: "GET",
                    url: "/api/status/HT50/accumulate/"+id,
                    success: function(res)
                    {
                        if(!res.error) {
                            toastr.success('Thành công!');
                        }
                        dataTable.ajax.reload(null, false);
                        page.hide()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        toastr.error(thrownError);
                        page.hide()
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
                $('#'+id).prop('checked', false);
            }
        });
}
function checkeUpdate(id){
    swal({
            title: "Bạn chắc chắn đã cập nhật khách hàng lên Bravo?",
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
                page.show()
                $.ajax({
                    type: "GET",
                    url: "/api/status/HT50/accumulate/status/"+id,
                    success: function(res)
                    {
                        if(!res.error) {
                            toastr.success('Thành công!');
                        }
                        dataTable.ajax.reload(null, false);
                        page.hide()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        toastr.error(thrownError);
                        page.hide()
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
                $('#'+id).prop('checked', false);
            }
        });
}

function save(){
    status=1;
    $("#updated-customer").submit();
    status=0;
}
function show(code,type){
    if (type==0) $('.button-submit-data').show()
    else $('.button-submit-data').hide()
    page.show()
    $.ajax({
        type: "GET",
        url: "/HT50/manager/new/customer/"+code,
        success: function(response)
        {
            $('#code').val(response.code)
            $('#name_gara').val(response.name_gara)
            $('#name').val(response.name)
            $('#birthday').val(response.birthday)
            $('#email').val(response.email)
            $('#phone').val(response.phone)
            $('#name_sale').val(response.name_sale)
            $('#phone_sale').val(response.phone_sale)
            $('#name_accountant').val(response.name_accountant)
            $('#phone_accountant').val(response.phone_accountant)
            $('#address').val(response.address)
            $('#province').val(response.province)
            $('#city').val(response.city)
            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
            page.hide()
        }
    });

}

function showBG(code){
    page.show()
    $.ajax({
        type: "GET",
        url: "/HT50/manager/new/customer/"+code,
        success: function(response)
        {
            $('#code-bg').val(response.code)
            $('#name_gara-bg').val(response.name_gara)
            $('#birthday-bg').val(response.birthday)
            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
            page.hide()
        }
    });

}

$("#updated-customer").submit(function(e){
    e.preventDefault();
}).validate({
    rules: {
        code: {
            required: true,
        },
        name_gara: {
            required: true,
        },
        birthday: {
            required: true,
        },
        email: {
            required: true,
        },
        phone: {
            required: true,
        },
        name_sale: {
            required: true,
        },
        phone_sale: {
            required: true,
        },
        address: {
            required: true,
        },
        province: {
            required: true,
        },
        city: {
            required: true,
        },
    },
    messages: {
        code: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        name_gara: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        birthday: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        email: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        phone: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        name_sale: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        phone_sale: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        address: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        city: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        province: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
    },
    submitHandler: function(form) {
        page.show()
        var formData = new FormData(form);
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append('code',$('#code').val())
        console.log(formData)
        if (status==1) formData.append('status',1)
        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            dataType:'json',
            async:false,
            processData: false,
            contentType: false,
            success: function(response) {
                setTimeout(function () {
                    toastr.success('has been added');
                },1000);
                $("#edit").modal('toggle');
                dataTable.ajax.reload(null, false);
                page.hide()
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
                page.hide()
            },
        });
    }
});


$("#bg-customer").submit(function(e){
    e.preventDefault();
}).validate({
    rules: {
        code: {
            required: true,
        },
        name_gara: {
            required: true,
        },
        bg: {
            required: true,
        },
        value: {
            required: true,
        }
    },
    messages: {
        code: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        name_gara: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        bg: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        value: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
    },
    submitHandler: function(form) {
        page.show()
        var formData = new FormData(form);
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        formData.append('code',$('#code-bg').val())
        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            dataType:'json',
            async:false,
            processData: false,
            contentType: false,
            success: function(response) {
                setTimeout(function () {
                    toastr.success('has been added');
                },1000);
                $("#bg").modal('toggle');
                dataTable.ajax.reload(null, false);
                page.hide()
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
                page.hide()
            },
        });
    }
});
