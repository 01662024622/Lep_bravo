// $().ready(function() {
//     $("#add-form").submit(function(e) {
//         e.preventDefault();
//     }).validate({
//         rules: {
//             name_gara: {
//                 required: true,
//             },
//             name: {
//                 required: true,
//             },
//             phone: {
//                 required: true,
//             },
//             name_sale: {
//                 required: true,
//             },
//             phone_sale: {
//                 required: true,
//             },
//             address: {
//                 required: true,
//             },
//             province: {
//                 required: true,
//             },
//             birthday: {
//                 required: true,
//             },
//             city: {
//                 required: true,
//             }
//         },
//         messages: {
//             name_gara: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             name: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             phone: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             name_sale: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             phone_sale: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             address: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             province: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             birthday: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             },
//             city: {
//                 required: 'Câu trả lời của bạn không hợp lệ?',
//             }
//         },
//         submitHandler: function(form) {
//             console.log(form)
//             // form.submit();
//         }
//     });
// });
$("#file").change(function(e) {
    $('#file-name').text(e.target.files[0].name);
});
