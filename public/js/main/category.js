$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
$(function () {
    $("#sortable").sortable({
        placeholder: "ui-state-highlight",
        items: "li:not(.disable-sort-item)"
    });
    $("#sortable").disableSelection();
    $(".portlet-toggle").on("click", function () {
        var icon = $(this);
        icon.toggleClass("ui-icon-minusthick ui-icon-plusthick");
        icon.closest(".portlet").find(".portlet-content").toggle();
    });

});
var users = [];
var user_add = [];
var user_update = [];
var user_find = [];
$('#staff_find').on('click', function () {
    searchStaff();
})
$('#staff_find_text').on('keyup', function (event) {
    if (event.key === "Enter") {
        searchStaff()
    }
})

function searchStaff() {
    page.show()
    var userQuery = '';
    if (users.length > 0) {
        userQuery = '?users[]=' + users.join('&users[]=');
    }
    $.ajax({
        type: "GET",
        url: "/api/v1/users/search/" + $('#staff_find_text').val() + userQuery,
        success: function (response) {
            var html = "";
            for (var i = 0; i < response.length; i++) {
                html = html + '<option value="' + response[i]['id'] + '">' + response[i]['name'] + '</option>';
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
            user_add.push(parseInt(element));
            $("#multiple_staff_select option[value='"+element+"']").remove();
            staff_table = staff_table + `<tr>
                    <td>` + user_find[element]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="user_role_` + user_find[element]['id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" selected>Cho phép</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" >Chặn</option>
                        </select>
                    </td>
                </tr>`;

        });
        $('#staff_role_table').append(staff_table);

    }
})


$('#apartment_find').on('click', function () {
    searchApartment();
})
$('#apartment_find_text').on('keyup', function (event) {
    if (event.key === "Enter") {
        searchApartment()
    }
})
var apartment_find = [];
var apartment_add = [];
var apartment_update = [];
var apartments = [];

function searchApartment() {
    page.show()
    var apartmentQuery = '';
    if (apartments.length > 0) {
        apartmentQuery = '?apartments[]=' + apartments.join('&apartments[]=');
    }
    $.ajax({
        type: "GET",
        url: "/api/v1/apartments/search/" + $('#apartment_find_text').val() + apartmentQuery,
        success: function (response) {
            var html = "";
            for (var i = 0; i < response.length; i++) {
                html = html + '<option value="' + response[i]['id'] + '">' + response[i]['name'] + '</option>';
                apartment_find[response[i]['id']] = response[i];
            }
            $('#multiple_apartment_select').html(html);

            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
}

$("#apartment_select").on('click', function () {
    if ($("#multiple_apartment_select").val() != "") {
        var apartment_table = '';
        $("#multiple_apartment_select").val().forEach(function (element, index) {
            apartments.push(parseInt(element));
            apartment_add.push(parseInt(element));
            $("#multiple_apartment_select option[value='"+element+"']").remove();
            apartment_table = apartment_table + `<tr>
                    <td>` + apartment_find[element]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="apartment_role_` + apartment_find[element]['id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" selected>Cho phép</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" >Chặn</option>
                        </select>
                    </td>
                </tr>`;

        });
        $('#apartment_role_table').append(apartment_table);

    }
})

// get data for form update
function getInfo(id) {
    $('#nav_active')[0].click();
    apartments = []
    apartment_add = []
    apartment_update = []
    users = []
    groups = []
    user_add = []
    user_update = []
    apartment_find = []
    page.show()
    $.ajax({
        type: "GET",
        url: "/categories/" + id,
        success: function (response) {
            $('#title').val(response.title);
            $('#url').val(response.url);
            if (response.parent_id == 0) {
                $("#radio_" + response.type).prop("checked", true);
                $('#radio').show();
                $('#sub-radio').hide();
                $('#url_group').hide();
                $('#header_group').hide();
                $('#url').val('');
                $('#icon').val(response.icon);
            } else {
                $('#header_group').show();
                $('#radio').hide();
                $('#sub-radio').show();
                $('#url_group').show();
                $('#header').val(response.header);
                $('#icon').val(response.icon);
                $("#radio_" + response.type).prop("checked", true);

            }
            $('#role').val(response.role);
            $('#eid').val(response.id);
            $('#parent_id').val(response.parent_id);
            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
}

$('#apartment_toggle').on('click', function () {
    $('#multiple_staff_select').html('');
    $('#multiple_apartment_select').html('');
    $('#multiple_group_select').html('');
    if ($('#eid').val() == '') return
    if (apartments.length > 0) return
    page.show()
    $.ajax({
        type: "GET",
        url: "/api/v1/apartments/role/" + $('#eid').val(),
        success: function (response) {
            var apartment_table = `<tr>
                                                <th>Phòng ban</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;
            for (var i = 0; i < response.length; i++) {
                apartments.push(response[i]['id'])
                apartment_update.push(response[i]['ca_id'])
                apartment_table = apartment_table + `<tr>
                    <td>` + response[i]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="apartment_role_update_` + response[i]['ca_id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" `

                if (response[i]['role'] == 0) apartment_table = apartment_table + 'selected';

                apartment_table = apartment_table + `>cho phép</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" `

                if (response[i]['role'] == 2) apartment_table = apartment_table + 'selected';

                apartment_table = apartment_table + `>Chặn</option>
                        </select>
                    </td>
                </tr>`;
            }

            $('#apartment_role_table').html(apartment_table);

            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
})

$('#staff_toggle').on('click', function () {
    $('#multiple_staff_select').html('');
    $('#multiple_apartment_select').html('');
    $('#multiple_group_select').html('');
    if ($('#eid').val() == '') return
    if (users.length > 0) return
    page.show()
    $.ajax({
        type: "GET",
        url: "/api/v1/users/role/" + $('#eid').val(),
        success: function (response) {
            var users_table = `<tr>
                                                <th>Nhân viên</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;
            for (var i = 0; i < response.length; i++) {
                users.push(response[i]['id'])
                user_update.push(response[i]['ca_id'])
                users_table = users_table + `<tr>
                    <td>` + response[i]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="user_role_update_` + response[i]['ca_id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" `

                if (response[i]['role'] == 0) users_table = users_table + 'selected';

                users_table = users_table + `>cho phép</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" `

                if (response[i]['role'] == 2) users_table = users_table + 'selected';

                users_table = users_table + `>Chặn</option>
                        </select>
                    </td>
                </tr>`;
            }
            $('#staff_role_table').html(users_table);

            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
})
var group_find = [];
var groups = [];
$('#group_find').on('click', function () {
    searchGroup();
})
$('#group_find_text').on('keyup', function (event) {
    if (event.key === "Enter") {
        searchGroup()
    }
})

function searchGroup() {
    page.show()
    var apartmentQuery = '';
    if (groups.length > 0) {
        apartmentQuery = '?groups[]=' + groups.join('&groups[]=');
    }
    $.ajax({
        type: "GET",
        url: "/api/v1/groups/search/" + $('#group_find_text').val() + apartmentQuery,
        success: function (response) {
            var html = "";
            for (var i = 0; i < response.length; i++) {
                html = html + '<option value="' + response[i]['id'] + '">' + response[i]['name'] + '</option>';
                group_find[response[i]['id']] = response[i];
            }
            $('#multiple_group_select').html(html);
            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
}
$('#group_toggle').on('click', function () {
    $('#multiple_staff_select').html('');
    $('#multiple_apartment_select').html('');
    $('#multiple_group_select').html('');
    if ($('#eid').val() == '') return
    if (groups.length > 0) return
    page.show()
    $.ajax({
        type: "GET",
        url: "/api/v1/groups/role/" + $('#eid').val(),
        success: function (response) {
            var group_table = `<tr>
                                                <th>Nhóm</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;
            for (var i = 0; i < response.length; i++) {
                groups.push(response[i]['id'])
                group_table = group_table + `<tr>
                    <td>` + response[i]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="group_role_` + response[i]['ca_id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" selected>cho phép</option>
                            <option value="2" style="font-weight: 700; color: #AA0000">Xóa</option>
                        </select>
                    </td>
                </tr>`;
            }
            $('#group_role_table').html(group_table);

            page.hide()
        },
        error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        }
    });
})
$("#group_select").on('click', function () {
    if ($("#multiple_group_select").val() != "") {
        var group_table = '';
        $("#multiple_group_select").val().forEach(function (element, index) {
            groups.push(parseInt(element));
            $("#multiple_group_select option[value='"+element+"']").remove();
            group_table = group_table + `<tr>
                    <td>` + group_find[element]['name'] + `</td>
                    <td>
                        <select class="role-select" name="role" id="group_role_` + group_find[element]['id'] + `">
                            <option value="0" style="font-weight: 700; color: #3ED317" selected>Cho phép</option>
                            <option value="2" style="font-weight: 700; color: #AA0000" >Xóa</option>
                        </select>
                    </td>
                </tr>`;

        });
        $('#group_role_table').append(group_table);

    }
})


function add_new_sub(id) {
    $('#eid').val('');
    $('#title').val('');
    $('#url').val('');
    $("#radio_1").attr('checked', 'checked');
    $('#radio').hide();
    $('#sub-radio').show();
    $('#url_group').show();
    $('#header_group').show();
    $('#header').val('');
    $('#icon').val('fa fa-align-left');
    $('#role').val(0);
    $('#parent_id').val(id);
    $('#radio_1').prop("checked", true);
    apartments = [];
    apartment_add = [];
    apartment_update = [];
    users = [];
    groups = [];
    user_add = [];
    user_update = [];
    var users_table = `<tr>
                                                <th>Nhân viên</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;

    $('#staff_role_table').html(users_table);
    var group_table = `<tr>
                                                <th>Nhóm</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;

    $('#group_role_table').html(group_table);
    var apartment_table = `<tr>
                                                <th>Phòng ban</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;

    $('#apartment_role_table').html(apartment_table);

    $('#nav_active')[0].click();
}

function add_new() {
    $('#eid').val('');
    $('#title').val('');
    $('#url').val('');
    $('#header_group').hide();
    $('#header').val('');
    $('#icon').val('fa fa-align-left');
    $('#role').val(0);
    $("#radio_5").attr('checked', 'checked');
    $('#radio').show();
    $('#sub-radio').hide();
    $('#url_group').hide();
    $('#role').val(0);
    $('#parent_id').val(0);
    $('#radio_5').prop("checked", true);
    apartments = [];
    apartment_add = [];
    apartment_update = [];
    users = [];
    groups = [];
    user_add = [];
    user_update = [];
    var users_table = `<tr>
                                                <th>Nhân viên</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;

    $('#staff_role_table').html(users_table);
    var group_table = `<tr>
                                                <th>Nhóm</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;

    $('#group_role_table').html(group_table);
    var apartment_table = `<tr>
                                                <th>Phòng ban</th>
                                                <th>Quyền hạn</th>
                                            </tr>`;

    $('#apartment_role_table').html(apartment_table);

    $('#nav_active')[0].click();
}

$("#save").on('click', function () {
    if ($('#title').val() == '') {
        toastr.error('Tiêu đề không được phép rỗng!');
        return;
    }
    page.show();
    var formData = new FormData();
    if ($('#eid').val() != '') {
        formData.append('id', $('#eid').val());
    }

    formData.append('title', $('#title').val());
    formData.append('parent_id', $('#parent_id').val());
    formData.append('icon', $('#icon').val());

    if ($('#url').val() != '') {
        formData.append('url', $('#url').val());
    }
    if ($('#header').val() != '') {
        formData.append('header', $('#header').val());
    }

    formData.append('role', $('#role').val());
    formData.append('type', $('input[name=type]:checked').val());
    if (user_add.length > 0) {
        user_add.forEach(element => {
            formData.append('users[]', element + '_' + $("#user_role_" + element).val());
        })
    }
    if (user_update.length > 0) {
        user_update.forEach(element => {
            formData.append('user_update[]', element + '_' + $("#user_role_update_" + element).val());
        })
    }
    if (groups.length > 0) {
        groups.forEach(element => {
            formData.append('groups[]', element + '_' + $("#group_role_" + element).val());
        })
    }
    if (apartment_add.length > 0) {
        apartment_add.forEach(element => {
            formData.append('apartments[]', element + '_' + $("#apartment_role_" + element).val());
        })
    }
    if (apartment_update.length > 0) {
        apartment_update.forEach(element => {
            formData.append('apartment_update[]', element + '_' + $("#apartment_role_update_" + element).val());
        })
    }
    console.log(formData)
    $.ajax({
        url: "/categories",
        type: "POST",
        data: formData,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response)
            setTimeout(function () {
                if ($('#eid').val() == '') {
                    toastr.success('Thêm mới thành công!');
                } else {
                    toastr.success('Cập nhật thành công!');
                }
            }, 1000);
            $("#myModal").modal('toggle');
            var type = '';
            if (response['type'] == 10) type = 'style="background-color: #ccff99"';
            if ($('#eid').val() == '' && $('#parent_id').val() == 0) {
                var li = `<li class="ui-state-default" data-value="` + response['id'] + `">
                <div class="main-header"` + type + `>
                    <p class="main-title header" title="` + response['title'] + `">` + response['title'] + `</p>
                    <button class='btn menu-icon' data-toggle="modal" data-target="#myModal" onclick="getInfo(` + response['id'] + `)">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                    </button>
                </div>
                <div class="main-content">
                    <ul id="sortable_sub_` + response['id'] + `" class="sortable_sub">
                         <li class="ui-state-default disable-sub-sort-item" id="add_button_category_{{$category->id}}">
                                <div class="sub-header">
                                    <button onclick="add_new_sub(`+response['id']+`)" data-toggle="modal" data-target="#myModal">
                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </li>
                    </ul>
                </div>

            </li>`;
                $('#add_button_category').before(li);
            }
            if ($('#eid').val() == '' && $('#parent_id').val() > 0) {
                var li = `<li class="ui-state-default" data-value="`+response['id']+`">
                                <div class="sub-header">
                                    <p class="sub-title header" title="`+response['title']+`">`+response['title']+`</p>
                                    <button class='btn menu-icon' data-toggle="modal" data-target="#myModal" onclick="getInfo(`+response['id']+`)">
                                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </li>`;
                $('#add_button_category_'+$('#parent_id').val()).before(li);
            }
            if ($('#eid').val() != ''){
                if (response['type']==10){
                    $('#category_'+response['id']).find('div.container-header:first').addClass('main-header-color')
                }
                if (response['type']==5){
                    $('#category_'+response['id']).find('div.container-header:first').removeClass('main-header-color')
                }
                $('#category_'+response['id']).find('p.header:first').text(response['title'])
            }
            page.hide();
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });
});
var remove=[];
$('#save_change').on('click',function (){
    page.show();
    var formData = new FormData();
    $('#sortable').children('li').each(function (index, element) {
        if ($(element).data('value')==undefined) return ;
        formData.append('categories[]', index + '_' + $(element).data('value'));
        $(element).find('li').each(function (ind, ele) {
            if ($(ele).data('value')==undefined) return ;
            formData.append('categories[]', ind + '_' + $(ele).data('value'));
        })
    })
    if (remove.length>0){
        remove.forEach(elment=>formData.append('removes[]',elment))
    }
    $.ajax({
        url: "/api/status/categories/sort",
        type: "POST",
        data: formData,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response)
            toastr.success('Cập nhật thành công!');
            page.hide();
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });
})
$('#remove').on('click',function(){
        remove.push($('#eid').val());
        $("#myModal").modal('toggle');
        $('#category_'+$('#eid').val()).hide();
})
