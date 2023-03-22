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
        url: "/api/v1/insurance/table",
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
        {data: 'link', name: 'link'},
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
        content: {
            required: true,
            minlength: 2
        }
    },
    messages: {
        name: {
            required: "Hãy nhập tên nhóm sản phẩm",
            minlength: "Tên nhóm sản phẩm không đúng"
        },
        content: {
            required: "Nội dung chính sach bảo hành",
            minlength: "Nội dung chính sach bảo hành"
        },
    },
    submitHandler: function (form) {
        var formData = new FormData(form);
        if ($('#eid').val() ==='') {
            formData.delete('id');
        }

        formData.set('content-ckeditor',
            CKEDITOR.instances['content-ckeditor'].getData())
        $.ajax({
            url: form.action,
            type: form.method,
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                $("#add-modal").modal('toggle');
                dataTable.ajax.reload(null, false);
                console.log(response)
                setTimeout(function () {
                    if ($('#eid').val() == '') {
                        toastr.success('Thêm mới thành công!');
                    } else {
                        toastr.success('Cập nhật thành công!');
                    }
                }, 1000);
            }, error: function (xhr, ajaxOptions, thrownError) {
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


// get data for form update
function getInfo(id) {
    console.log(id);
    clearForm()
    // $('#editPost').modal('show');
    $.ajax({
        type: "GET",
        url: "/insurance/" + id,
        success: function (response) {
            $('#name').val(response.name);
            $('#link').val(response.link);
            if (response.type === 0) {
                $('#type').prop('checked', false);
                $(".insurance-content").hide()
            }else
            {
                $('#type').prop('checked', true);
                $(".insurance-content").show()
            }
            CKEDITOR.instances['content-ckeditor'].setData(response.content);
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
                $.ajax({
                    type: "delete",
                    url: "/insurance/" + id,
                    success: function (res) {
                        if (!res.error) {
                            toastr.success('Thành công!');
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
    $(".insurance-content").hide()
}


$("#type").change(function () {
    if (this.checked) {
        $(".insurance-content").show()
    } else $(".insurance-content").hide()
});
