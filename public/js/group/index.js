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
        url: "/api/v1/groups/table",
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
        {data: 'users', name: 'users'},
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
var page = $('#load_page')
var user_find = [];
var users = [];

$('#staff_find').on('click', function () {
    searchStaff();
})
$('#staff_find_text').on('keyup', function (event) {
    if (event.key === "Enter") {
        searchStaff()
    }
})

function searchStaff() {
    var staff = $('#staff_find_text').val()
    page.show()
    var userQuery = '';
    if (users.length > 0) {
        userQuery = '?users[]=' + users.join('&users[]=');
    }
    $.ajax({
        type: "GET",
        url: "/api/v1/users/search/" + staff + userQuery,
        success: function (response) {
            var html = "";
            for (var i = 0; i < response.length; i++) {
                html = html + '<option value="' + response[i]['id'] + '" id="staff_option_' + response[i]['id'] + '">' + response[i]['name'] + '</option>';
                user_find[response[i]['id']] = response[i];
            }
            $('#multiple_staff_select').html(html);

            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
}

$("#staff_select").on('click', function () {
    if ($("#multiple_staff_select").val() != "") {
        var staff_table = '';
        $("#multiple_staff_select").val().forEach(function (element, index) {
            users.push(parseInt(element));
            $("#staff_option_" + element).hide(0);
            staff_table = staff_table + `<tr>
                    <td>` + user_find[element]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="user_role_` + user_find[element]['id'] + `">
                            <option value="0" selected>thêm</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" >Loại bỏ</option>
                        </select>
                    </td>
                </tr>`;

        });
        $('#staff_role_table').append(staff_table);

    }
})

//____________________________________________________________________________________________________

//____________________________________________________________________________________________________

$('form input').keydown(function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});
$("#add-form").submit(function (e) {
    e.preventDefault();
}).validate({
    rules: {
        name: {
            required: true,
            minlength: 5
        }
    },
    messages: {
        name: {
            required: "Hãy nhập tên phòng ban",
            minlength: "Tên phòng ban ít nhất phải có 5 kí tự"
        }
    },
    submitHandler: function (form) {
        page.show()
        var formData = new FormData(form);
        if ($('#eid').val() == '') {
            formData.delete('id');
        }
        users.forEach(element=>{
            formData.append('users[]',element+'_'+$('#user_role_'+element).val())
        })
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
                $("#add-modal").modal('toggle');
                dataTable.ajax.reload(null, false);
                page.hide()
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
    page.show()
    console.log(id);
    // $('#editPost').modal('show');
    $.ajax({
        type: "GET",
        url: "/groups/" + id,
        success: function (response) {
            page.hide()
            $('#name').val(response.name);
            $('#description').val(response.description);
            $('#eid').val(response.id);
            var users_table = `<tr>
                                                <th>Nhân viên</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;
            for (var i = 0; i < response.users.length; i++) {
                users.push(response.users[i]['id'])
                users_table = users_table + `<tr>
                    <td>` + response.users[i]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="user_role_update_` + response.users[i]['ca_id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" `

                if (response.users[i]['role'] == 0) users_table = users_table + 'selected';

                users_table = users_table + `>thêm</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" `

                if (response.users[i]['role'] == 2) users_table = users_table + 'selected';

                users_table = users_table + `>Loại bỏ</option>
                        </select>
                    </td>
                </tr>`;
            }
            $('#staff_role_table').html(users_table);
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
            page.show()
            if (isConfirm) {
                $.ajax({
                    type: "delete",
                    url: "/groups/" + id,
                    success: function (res) {
                        page.hide()
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
    var users_table = `<tr>
                                                <th>Nhân viên</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;
    $('#staff_role_table').html(users_table);
    users=[];
}


