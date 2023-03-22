
$('.type-radio input:radio[name=type]').change(function () {
    // console.log(this.value)
    // $('#minus-container').toggleClass('hidden')
})
$(document).on('keydown', 'input[pattern]', function(e){
    var input = $(this);
    var oldVal = input.val();
    var regex = new RegExp(input.attr('pattern'), 'g');

    setTimeout(function(){
        var newVal = input.val();
        if(!regex.test(newVal)){
            input.val(oldVal);
        }
    }, 0);
});
