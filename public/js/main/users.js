
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
//____________________________________________________________________________________________________
var dataTable = $('#users-table').DataTable({
  processing: true,
  serverSide: true,
  ajax:{ type: "GET",
  url: "/api/v1/users/table",
  error: function (xhr, ajaxOptions, thrownError) {
   if (xhr!=null) {
    if (xhr.responseJSON!=null) {
      if (xhr.responseJSON.errors!=null) {
        if (xhr.responseJSON.errors.permission!=null) {
          location.reload();
        }
      }
    }
  }
}},
columns: [
{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
{ data: 'name', name: 'name' },
{ data: 'email', name: 'email' },
{ data: 'phone', name: 'phone' },
{ data: 'role', name: 'role' },
{ data: 'action', name: 'action' },
],
oLanguage:{
    "sProcessing":   "Đang xử lý...",
    "sLengthMenu":   "Xem _MENU_ mục",
    "sZeroRecords":  "Không tìm thấy dòng nào phù hợp",
    "sInfo":         "Đang xem _START_ đến _END_ trong tổng số _TOTAL_ mục",
    "sInfoEmpty":    "Đang xem 0 đến 0 trong tổng số 0 mục",
    "sInfoFiltered": "(được lọc từ _MAX_ mục)",
    "sInfoPostFix":  "",
    "sSearch":       "Tìm Kiếm: ",
    "sUrl":          "",
    "oPaginate": {
        "sFirst":    " Đầu ",
        "sPrevious": " Trước ",
        "sNext":     " Tiếp ",
        "sLast":     " Cuối "
    }
}

});
//____________________________________________________________________________________________________

//____________________________________________________________________________________________________
$("#add-form").submit(function(e){
  e.preventDefault();
}).validate({
  rules: {
    name: {
      required: true,
      minlength: 5
    },
    phone:{
      required:true,
      minlength:10,
      maxlength:10,
    },
    email:{
      required:true,
      minlength:10,
    },
    room:{
      required:true,
    },
  },
  messages: {
    name: {
      required: "Hãy nhập họ và tên của bạn",
      minlength: "Họ và tên ít nhất phải có 5 kí tự"
    },
    phone:{
      required:"Hãy nhập SĐT",
      minlength:"Đây không phải số điện thoại",
      maxlength:"Đây không phải số điện thoại",
    },
    email:{
      required:"Hãy nhập email",
      minlength:"Đây không phải email",
    },
    password:{
      required:"Hãy nhập password",
      minlength:"Mật khẩu ít nhất phải có 8 kí tự",
    },

  },
  submitHandler: function(form) {
    var formData = new FormData(form);
    if ($('#eid').val()=='') {
      formData.delete('id');
    }

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
          if ($('#eid').val()=='') {
          toastr.success('Thêm mới thành công!');
        }else{
          toastr.success('Cập nhật thành công!');
        }
       },1000);
       $("#add-modal").modal('toggle');
       dataTable.ajax.reload();
     }, error: function (xhr, ajaxOptions, thrownError) {
      toastr.error(thrownError);
    },
  });
  }
});


  // get data for form update
  function getInfo(id) {
    console.log(id);
        // $('#editPost').modal('show');
        $.ajax({
          type: "GET",
          url: "/users/"+id,
          success: function(response)
          {
           $('#name').val(response.name);
           $('#email').val(response.email);
           $('#phone').val(response.phone);
           $('#eid').val(response.id);
           $('.tag_pass').remove();
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
      function alDelete(id){
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
      function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            type: "delete",
            url: "users/"+id,
            success: function(res)
            {
              if(!res.error) {
                toastr.success('Thành công!');
                $('#product-'+id).remove();
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


      function clearForm(){
        $('#add-form')[0].reset();

        $('.tag_pass').remove();
        $('#eid').val('');
        $('.modal-body').append(`<div class="form-group tag_pass">
          <label for="name">Password</label>
          <input type="password" class="form-control" id="password" name="password"  placeholder="Enter password">
          </div>
          <div class="form-group tag_pass">
          <label for="name">Re-Password</label>
          <input type="password" class="form-control" id="repassword" name="repassword"  placeholder="Enter password">
          </div>`);
      }



      function changeStatus(id){
        swal({
          title: "Bạn thực sự muốn thay đổi quyền của người dùng?",
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
            url: "/api/status/users/"+id,
            data: {
              role:$('#role_'+id).val()
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
        }
      });
      };
