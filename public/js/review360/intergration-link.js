$("#code-link").on("keyup", function () {
    if ($(this).val().length > 2) {
        $("#submit-link").prop('disabled', false);
    } else $("#submit-link").prop('disabled', true);
})

$("#submit-link").on("click", function () {
    $.ajax({
        url: '/customer/feedback/report/' + $("#code-link").val(),
        type: 'GET',
        success: function (response) {
            $("#link-text").val("http://crm.htauto.vn/customer/feedback/link/" + $("#code-link").val())
            $("#link-modal").modal('show');
        }, error: function (xhr, ajaxOptions, thrownError) {
            toastr.error("Mã khách hàng không hợp lệ");
        },
    });
})
$("#coppy-link").on("click", function () {
    var copyText = document.getElementById("link-text");
    copyText.select();
    copyText.setSelectionRange(0, 99999)
    document.execCommand("copy");
})
