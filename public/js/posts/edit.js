var file=undefined;
var image = $('#image-preview').croppie({
    enableExif: true,
    viewport: {
        width: 400,
        height: 400,
        type: 'square'
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
                file = new File([blob], "avata.png",{ type: "image/png" })
            })
        $('#myModal').modal('hide');
        $('#avata').html('<img id="upload-data-avata" src="' + response + '" alt="avata" style="width:100%;height:auto"> ')
    })
})

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

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

function searchApartment() {
    var apartment = $('#apartment_find_text').val();

    page.show()
    var apartmentQuery = '';
    if (apartments.length > 0) {
        apartmentQuery = '?apartments[]=' + apartments.join('&apartments[]=');
    }
    $.ajax({
        type: "GET",
        url: "/api/v1/apartments/search/" + apartment + apartmentQuery,
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
            // $("#staff_option_" + element).hide(0);
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



$('#save-posts').on('click', function () {
    save($('#role').val())
})

function save(role) {
    page.show()
    var formData = new FormData();
    formData.append('title', $('#title').val());
    formData.append('id', $('#eid').val());
    formData.append('role', role);
    var content = CKEDITOR.instances.editor1.getData();
    if (content != '') {
        formData.append('content', content);
    }
    var embed = $('#embed').val();
    if (embed != '') {
        formData.append('embed', embed);
    }
    if (file != undefined) {
        formData.append('avata', file);
    }

    if (users.length > 0) {
        users.forEach(element => {
            formData.append('users[]', element + '_' + $("#user_role_" + element).val());
        })
    }
    if (apartments.length > 0) {
        apartments.forEach(element => {
            formData.append('apartments[]', element + '_' + $("#apartment_role_" + element).val());
        })
    }
    if (groups.length > 0) {
        groups.forEach(element => {
            formData.append('groups[]', element + '_' + $("#group_role_" + element).val());
        })
    }
    if (categories.length > 0) {
        categories.forEach(element => {
            var checked = '';
            if ($("#category-" + element).is(':checked')) {
                checked = '0';
            } else checked = '1';
            formData.append('categories[]', element + '_' + checked);
        })
    }
    $.ajax({
        url: "/posts",
        type: "POST",
        data: formData,
        dataType: 'json',
        async: false,
        processData: false,
        contentType: false,
        success: function (response) {
            toastr.success('Cập nhật thành Công');
            $('#date-save').val(response['updated_at'])
            page.hide();
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
        },
    });

}

$(".check-input").change(function () {
    if (this.checked) {
        if (!categories.includes($(this).val())) {
            categories.push($(this).val());
        }
    }
});
