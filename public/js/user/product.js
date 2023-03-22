
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  function orderProduct(id) {
  	var arr =[];
  	$('.custom-control-input').each(function(index, el) {
  		if ($(this).prop('checked')) {
  			arr.push($(this).attr('staff-id'))
  		}
  	});

  	$.ajax({
          type: "POST",
          url: "/api/v1/product/orders/"+id,
          data:{
          	note:$('#note').val(),
            staffs:arr
          },
          success: function(response)
          {
          // location.reload();
          console.log(response);
           toastr.success('has been updated');
          },
          error: function (xhr, ajaxOptions, thrownError) {
            toastr.error(thrownError);
          }
        });
  }