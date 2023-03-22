$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
var file = undefined;
var image = $('#image-preview').croppie({
    enableExif: true,
    viewport: {
        width: 400,
        height: 400,
        type: 'circle'
    },
    boundary: {
        width: 500,
        height: 500
    }
});
$('#upload-image').change(function () {
    var reader = new FileReader();
    reader.onload = function (even) {
        $('#myModal').modal('show');
        image.croppie('bind', {
            url: even.target.result
        }).then(function () {
            console.log('bind complier')
        });
    }
    reader.readAsDataURL(this.files[0])
});
$('#crop_image').click(function (e) {
    e.preventDefault()
    // page.show();
    image.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function (response) {
        fetch(response)
            .then(res => res.blob())
            .then(blob => {
                file = new File([blob], "avata.png", {type: "image/png"})
            })
        $('#myModal').modal('hide');
        $('#avata').html('<img id="upload-data-avata" src="' + response + '" alt="avata" style="width:100%;height:auto"> ')
    })
})
$(document).ready(function () {
    $('#birth_day').datepicker({
        weekStart: 1,
        daysOfWeekHighlighted: "6,0",
        autoclose: true,
        todayHighlight: true,
    });
});


$("#update-profile").submit(function (e) {
    e.preventDefault();
}).validate({
    rules: {
        phone: {
            required: true,
            minlength: 9
        },
    },
    messages: {
        phone: {
            required: "Hãy nhập họ và tên của bạn",
            minlength: "Số điện thoại không đúng"
        },

    },
    submitHandler: function (form) {
        var formData = new FormData(form);
        if (file != undefined) {
            formData.append('avata', file);
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
                toastr.success('Cập nhật thành công!');
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
            },
        });
    }
});
