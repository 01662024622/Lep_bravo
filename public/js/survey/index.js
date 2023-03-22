
// $(document).on('keydown', 'input[pattern]', function(e){
//     var input = $(this);
//     var oldVal = input.val();
//     var regex = new RegExp(input.attr('pattern'), 'g');
//     setTimeout(function(){
//         var newVal = input.val();
//         if(!regex.test(newVal)){
//             input.val(oldVal);
//         }
//     }, 0);
// });

$().ready(function() {
    $("#add-form").submit(function(e) {
        e.preventDefault();
    }).validate({
        rules: {
            name_gara: {
                required: true,
            },
            name: {
                required: true,
            },
            phone: {
                required: true,
            },
            name_sale: {
                required: true,
            },
            phone_sale: {
                required: true,
            },
            address: {
                required: true,
            },
            province: {
                required: true,
            },
            birthday: {
                required: true,
            },
            city: {
                required: true,
            }
        },
        messages: {
            name_gara: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            name: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            phone: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            name_sale: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            phone_sale: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            address: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            province: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            birthday: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            },
            city: {
                required: 'Câu trả lời của bạn không hợp lệ?',
            }
        },
        submitHandler: function(form) {
            console.log(form)
            form.submit();
        }
    });
});
