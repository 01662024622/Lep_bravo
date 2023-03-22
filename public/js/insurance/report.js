$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

//____________________________________________________________________________________________________
const dataTable = $('#users-table').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        type: "GET",
        url: "/api/v1/insurance/report/table",
        error(xhr, ajaxOptions, thrownError) {
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
        {data: 'phone', name: 'phone'},
        {data: 'group', name: 'group'},
        {data: 'product', name: 'product'},
        {data: 'bill', name: 'bill'},
        {data: 'amount', name: 'amount'},
        {data: 'insurance_date', name: 'insurance_date'},
        {data: 'created_at', name: 'created_at'},
        {data: 'description', name: 'description'},
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



// get data for form update
function getInfo(id) {
    
    $.ajax({
        type: "GET",
        url: `/insuranceReport/${id}`,
        success(response) {
            const data = response[0];
            const form = JSON.parse(data["form"]);
            let html = "";
            form.data.forEach( (element, index) => {
                html+=`<h5>${element.ask}</h5>
                <p>${data[element.number]}</p>
                <hr>`
            });
            $("#modal-body").html(html);
            $('#add-modal').modal()
        },
        error(xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
}


//____________________________________________________________________________________________________

