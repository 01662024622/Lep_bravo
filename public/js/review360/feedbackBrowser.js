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
        url: "/api/v1/report/review/feedback/browser/table",
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
        {data: 'apartment', name: 'apartment'},
        {data: 'user', name: 'user'},
        {data: 'name', name: 'name'},
        {data: 'content', name: 'content'},
        {data: 'image', name: 'image'},
        {data: 'created_at', name: 'created_at'},
        {data: 'confirm', name: 'confirm'},
        {data: 'action', name: 'action'},
    ],
    "columnDefs": [
        { "width": "10%", "targets": 6 },
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

function changeStatus(id){
    swal({
            title: "Bạn thực sự muốn thay đổi trạng thái phản hồi?",
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
                    type: "post",
                    url: "/api/status/review/"+id,
                    data: {
                        status:$('#role_'+id).val()
                    },
                    dataType:'json',
                    success: function(res)
                    {
                        if(!res.error) {
                            toastr.success('Thay đổi thành công!');
                            dataTable.ajax.reload();
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        toastr.error(xhr.responseJSON.message);
                    }
                });
            } else {
                toastr.error("Hủy bỏ thao tác!");
                $('#role_'+id).val(0);
            }
        });
};
