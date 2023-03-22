$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

$("#change-password").submit(function (e) {
    e.preventDefault();
}).validate({
    rules: {
        old_password: {
            required: true,
            minlength: 8
        },
        new_password: {
            required: true,
            minlength: 8
        },
        ver_password: {
            required: true,
            minlength: 8,
            equalTo: "#new-password"
        }
    },
    messages: {
        old_password: {
            required: "Hãy nhập mật khẩu cũ",
            minlength: "Ít nhất 8 kí tự"
        },
        new_password: {
            required: "Hãy nhập mật khẩu cũ",
            minlength: "Ít nhất 8 kí tự"
        },
        ver_password: {
            required: "Hãy nhập mật khẩu",
            minlength: "Ít nhất 8 kí tự",
            equalTo: "Nhập lại mật khẩu không chính xác"
        }
    },
    submitHandler: function (form) {
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
                    if ($('#eid').val() == '') {
                        toastr.success('Thêm mới thành công!');
                    } else {
                        toastr.success('Cập nhật thành công!');
                    }
                }, 1000);
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
            },
        });
    }
});
