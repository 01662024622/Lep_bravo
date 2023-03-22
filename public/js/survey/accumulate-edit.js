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
        url: "/api/v1/accumulate/table?role_pt="+role,
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
        {data: 'phone', name: 'phone'},
        {data: 'coin', name: 'coin'},
        {data: 'used', name: 'used'},
        {data: 'availability', name: 'availability'},
        {data: 'level', name: 'level'},
        {data: 'process', name: 'process'},
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
function getInfo(url){
    var link=$('#copy-link-hidden');
    link.val(url);
    link.removeClass('hidden')
    var copyText = document.getElementById("copy-link-hidden");

    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    alert("Copied the text: " + copyText.value);

    link.addClass('hidden')
}
function getGift(code){
    page.show()
    $('#customer_code').val(code)
    $.ajax({
        type: "GET",
        url: "/HT50/gifts/"+code,
        success: function(response)
        {
            var html='';
            response['used'].forEach((element,index) => {
                html=html+`<tr id="`+element['id']+`"><td>`+(index+1)+`</td><td>`+element['code']+`</td><td>`+element['name']+`</td><td>`+element['coin']+`</td><td>`+element['date']+`</td></tr>`
            });
            var select='';
            response['gifts'].forEach((element,index) => {
                select=select+`<option value="`+element['id']+`">`+element['name']+`</option>`
            });
            $('#gift-body').html(html)
            $('#gift_id').html(select)
            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
            page.hide()
        }
    });

}

$("#gift-form").submit(function(e){
    e.preventDefault();
}).validate({
    rules: {
        code: {
            required: true,
        },
        gift: {
            required: true,
        },
    },
    messages: {
        code: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
        gift: {
            required: 'Câu trả lời của bạn không hợp lệ?',
        },
    },
    submitHandler: function(form) {
        var formData = new FormData(form);
        formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
        console.log(formData)
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
                getGift($('#customer_code').val())
                dataTable.ajax.reload(null, false);
            }, error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
            },
        });
    }
});
$general=$('#general-table').DataTable({
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
        {data: 'name_gara', name: 'name_gara'},
        {data: 'phone', name: 'phone'},
        {data: 'name', name: 'name'},
        {data: 'birthday', name: 'birthday'},
        {data: 'wb', name: 'wb'},
        {data: 'bg', name: 'bg'},
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
function showGeneral(role){
    page.show()
    $general.ajax.reload()
    page.hide()
}
