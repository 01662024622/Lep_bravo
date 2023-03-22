
$(function() {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
//____________________________________________________________________________________________________
var dataTable = $('#users-table').DataTable({
  processing: true,
  serverSide: true,
  ajax: "/api/v1/rating/table",
  columns: [
  { data: 'demeanor', name: 'demeanor' },
  { data: 'responsiveness', name: 'responsiveness' },
  { data: 'competence', name: 'competence' },
  { data: 'tangible', name: 'tangible' },
  { data: 'completeness', name: 'completeness' },
  { data: 'relevancy', name: 'relevancy' },
  { data: 'accuracy', name: 'accuracy' },
  { data: 'currency', name: 'currency' },
  { data: 'training_provider', name: 'training_provider' },
  { data: 'user_understanding', name: 'user_understanding' },
  { data: 'participation', name: 'participation' },
  { data: 'easier_to_the_job', name: 'easier_to_the_job' },
  { data: 'increase_productivity', name: 'increase_productivity' },
  { data: 'action', name: 'action' },
  ]
});
//____________________________________________________________________________________________________

//____________________________________________________________________________________________________
$("#add-form").submit(function(e){
  e.preventDefault();
}).validate({
  rules: {
    
  },
  submitHandler: function(form) {

    var formData = new FormData(form);
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
       $("#add-modal").modal('toggle');
       dataTable.ajax.reload();
     }, error: function (xhr, ajaxOptions, thrownError) {
      toastr.error(thrownError);
    },       
  });
  }
});

//____________________________________________________________________________________________________

//____________________________________________________________________________________________________
    // Update function
      // Delete function
      function alDelete(id){
        swal({
          title: "Are you sure to remove?",
        // text: "Bạn sẽ không thể khôi phục lại bản ghi này!!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",  
        cancelButtonText: "No",
        confirmButtonText: "Yes",
        // closeOnConfirm: false,
      },
      function(isConfirm) {
        if (isConfirm) {
          $.ajax({
            type: "delete",
            url: "rates/"+id,
            success: function(res)
            {
              if(!res.error) {
                toastr.success('Success!');
                $('#data-'+id).remove();
                  //setTimeout(function () {
                    //location.reload();
                  //}, 1000)
                }
              },
              error: function (xhr, ajaxOptions, thrownError) {
                toastr.error(thrownError);
              }
            });
        } else {
          toastr.error("Cancel!");
        }
      });
      };

      $(function(){
        var viewModel = {};
        viewModel.fileData = ko.observable({
          dataURL: ko.observable(),
    // base64String: ko.observable(),
  });
        viewModel.multiFileData = ko.observable({
          dataURLArray: ko.observableArray(),
        });
        viewModel.onClear = function(fileData){
          if(confirm('Are you sure?')){
            fileData.clear && fileData.clear();
          }                            
        };
        viewModel.debug = function(){
          window.viewModel = viewModel;
          console.log(ko.toJSON(viewModel));
          debugger; 
        };
        ko.applyBindings(viewModel);
      });




    })
//_________
