$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    }
});

$('#apartment_id').on('change', function () {
    $('#custom').hide();
    $('#submit').prop('disabled', true);
    if ($(this).val() == 0) {
        $('#submit').prop('disabled', false);
        return
    }
    $.ajax({
        url: '/review/report/' + $(this).val(),
        type: 'GET',
        success: function (response) {
            var html = '<option value="0" checked>Ý kiến cải thiện chung</option>';
            for (var i = response.length - 1; i >= 0; i--) {
                html += `<option value="` + response[i]['id'] + `">` + response[i]['name'] + `</option>`
            }
            $('#user_id').html(html);
            $('#custom').show();
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(xhr.responseJSON.message);
        },
    });
})

$('#apartment_id_qc').on('change', function () {
    $('#custom_qc').hide();
    $('#submit_qc').prop('disabled', true);
    if ($(this).val() == 0) {
        $('#submit_qc').prop('disabled', false);
        return
    }
    $.ajax({
        url: '/review/report/' + $(this).val(),
        type: 'GET',
        success: function (response) {
            var html = '<option value="0" checked>Ý kiến cải thiện chung</option>';
            for (var i = response.length - 1; i >= 0; i--) {
                html += `<option value="` + response[i]['id'] + `">` + response[i]['name'] + `</option>`
            }
            $('#user_id_qc').html(html);
            $('#custom_qc').show();
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(xhr.responseJSON.message);
        },
    });
})


$('#submit').on('click', function () {
    var createForm = new FormData();
    createForm.append('apartment_id', $('#apartment_id').val());
    createForm.append('type', 'attitude');
    createForm.append('user_id', $('#user_id').val());
    createForm.append('content', $("#content_att").val());
    $.ajax({
        url: '/review/report',
        type: 'POST',
        data: createForm,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response, textStatus, request) {
             document.location.replace('/review/report');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });
})

$('#submit_qc').on('click', function () {
    var createForm = new FormData();
    createForm.append('apartment_id', $('#apartment_id_qc').val());
    createForm.append('type', 'procedure');
    createForm.append('user_id', $('#user_id_qc').val());
    createForm.append('content', $("#content_qc").val());
    createForm.append('name', $("#name_qc").val());
    createForm.append('name', $("#name_qc").val());
    if ($("#image_qc").val() !== "")
        createForm.append('image', $("#image_qc").get(0).files[0]);
    $.ajax({
        url: '/review/report',
        type: 'POST',
        data: createForm,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response, textStatus, request) {
            console.log(response)
            document.location.replace('/review/report');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });
})
//
// $('#new-checkbox-text').on('keyup',function(){
//   if($(this).val()!=""){
//     $('#new-checkbox').prop('checked', true);
//     $('#new-checkbox').val($(this).val());
//   }else{
//     $('#new-checkbox').prop('checked', false);
//     $('#new-checkbox').val($(this).val());
//   }
//   checkLength()
// })
// function checkLength(){
//   var length = $('input.form-check-input:checkbox:checked').length;
//   if (length>0) {
//     $('#submit').prop('disabled', false);
//   }else{
//     $('#submit').prop('disabled', true);
//   }
// }
$("#content_att").on('keyup', function () {
    if ($(this).val() == "") {
        $('#submit').prop('disabled', true);
    } else $('#submit').prop('disabled', false);
})
$(".text-qc").on('keyup', function () {
    var status = false;
    $(".text-qc").each(function (index) {
        console.log($(this).val())
        if ($(this).val() == "") status = true;
    });
    $('#submit_qc').prop('disabled', status);
})
$('#type').on('change', function () {
    $('.report').hide();
    $('#' + $(this).val()).show();
})

$('.validate-feedback').on('keyup', function () {
    var disable = false;
    $('.validate-feedback').each(function () {
        if ($(this).val() == "") {
            disable = true;
        }
    });
    $('#submit-feedback').prop('disabled', disable);
});

$('#submit-feedback').on('click', function () {
    var createForm = new FormData();
    createForm.append('order', $('#order-feedback').val());
    createForm.append('content', $('#content-feedback').val());
    createForm.append('note', $('#note-feedback').val());
    $.ajax({
        url: '/feedback/report',
        type: 'POST',
        data: createForm,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response, textStatus, request) {
            document.location.replace('/review/feedback/customer/report');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });
})

$('#new-checkbox-text-wh').on('keyup', function () {
    if ($(this).val() != "") {
        $('#new-checkbox-wh').prop('checked', true);
        $('#new-checkbox-wh').val($(this).val());
    } else {
        $('#new-checkbox-wh').prop('checked', false);
        $('#new-checkbox-wh').val($(this).val());
    }
    checkLengthWH()

})

$(':input[type="checkbox"]').change(function () {
    checkLength()
});
$('#amount-pr').on('keyup', function () {
    if ($(this).val() != "") {
        $('#submit-pr').prop('disabled', false);
    } else {
        $('#submit-pr').prop('disabled', true);
    }
})

$('#submit-pr').on('click', function () {
    var createForm = new FormData();
    createForm.append('amount', $('#amount-pr').val());
    $.ajax({
        url: '/feedback/PR',
        type: 'POST',
        data: createForm,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response, textStatus, request) {
            document.location.replace('/review/public/relationship/report');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });
})

$('#type-wh').on('change', function () {
    $('#submit-wh').prop('disabled', true);
    $('#new-checkbox-text-wh').val("");
    $('#new-checkbox-wh').val("");
    $('input:checkbox').prop('checked', false);
    $('#amount-wh').val("");
    $('#code-product').val("");
    $('.warehouse').hide();
    if ($(this).val() == 'QC') {
        $('#quality').show();
    } else {
        $('#amount').show();
    }
})

$('.wh-text').on('keyup', function () {
    if ($('#code-product').val() == "" || $('#amount-wh').val() == "") {
        $('#submit-wh').prop('disabled', true);
    } else {
        $('#submit-wh').prop('disabled', false);
    }
})

$('.check-box-wh').change(function () {
    checkLengthWH()
});

function checkLengthWH() {
    var length = $('input.form-check-input:checkbox:checked').length;
    if (length > 0) {
        $('#submit-wh').prop('disabled', false);
    } else {
        $('#submit-wh').prop('disabled', true);
    }
}


$('#submit-wh').on('click', function () {
    var createForm = new FormData();
    createForm.append('amount', $('#amount-wh').val());
    createForm.append('type', $('#type-wh').val());
    createForm.append('code_product', $('#code-product').val());
    if ($('input.check-box-wh:checkbox:checked').length > 0) {
        var checkbox = [];
        $('input.check-box-wh:checkbox:checked').each(function () {
            createForm.append('list[]', $(this).val());
        });

    }
    if ($("#new-checkbox-wh").val() != "") {
        createForm.append('newreport', $("#new-checkbox-wh").val());
    }

    $.ajax({
        url: '/feedback/warehouse',
        type: 'POST',
        data: createForm,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response, textStatus, request) {
            document.location.replace('/review/warehouse/report');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });

})
$("#amount-pr").on("input", function () {
    var nonNumReg = /[^0-9]/g
    $(this).val($(this).val().replace(nonNumReg, ''));
});

$("#code-link").on("keyup", function () {
    if ($(this).val().length > 2) {
        $("#submit-link").prop('disabled', false);
    } else $("#submit-link").prop('disabled', true);
})

$("#submit-link").on("click", function () {
    $.ajax({
        url: '/customer/feedback/report/' + $("#code-link").val(),
        type: 'GET',
        success: function (response) {
            $("#link-text").val("http://crm.htauto.vn/customer/feedback/link/" + $("#code-link").val())
            $("#link-modal").modal('show');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error("Mã khách hàng không hợp lệ");
        },
    });
})
$("#coppy-link").on("click", function () {
    var copyText = document.getElementById("link-text");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
})
