$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  }
});
$("#add-form").submit(function(e){
  e.preventDefault();
}).validate({
  rules: {
    code: {
      required: true,
      maxlength: 24,
      minlength: 2
    }
  },
  messages: {
    name: {
      required: "Hãy nhập mã khách hàng",
      maxlength: "Mã khách hàng không hợp lệ",
      minlength: "Mã khách hàng không hợp lệ"
    }
  },
  submitHandler: function(form) {
    $.ajax({
      url: form.action,
      type: form.method,
      data: {
      	code:$('#code').val()
      },
      dataType:'json',
      async:false,
      processData: false,
      contentType: false,
      success: function(response) {

     }, error: function (xhr, ajaxOptions, thrownError) {
      toastr.error(thrownError);
    },
  });
  }
});
