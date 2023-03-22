function changeLv(type, lv) {
    console.log('.emoji-' + type + '-' + lv)
    console.log('.check-emoji-' + type + '-' + lv)
    $('.checked-emoji-' + type).addClass('hidden')
    $('.emoji-' + type).addClass('opacity-7').removeClass('emoji-active')
    $('.emoji-' + type + '-' + lv).removeClass('opacity-7').addClass('emoji-active')
    $('.check-emoji-' + type + '-' + lv).removeClass('hidden')
    $('#submit').addClass('opacity-7')
    // $('input[name="improve[]"]:checked').attr('checked',false)
}

//Initialize with the list of symbols

//Find the input search box
let search = document.getElementById("searchCoin")

//Find every item inside the dropdown
let items = document.getElementsByClassName("dropdown-item")

function buildDropDown(values) {
    let contents = []
    for (let value of values) {
        contents.push('<input type="button" class="dropdown-item" type="button" data="' + value.id + '" value="' + value.name + '"/>')
    }
    $('#menuItems').append(contents.join(""))

    //Hide the row that shows no items were found
    $('#empty').hide()
}

//Capture the event when user types into the search box
window.addEventListener('input', function () {
    filter(search.value.trim().toLowerCase())
})

//For every word entered by the user, check if the symbol starts with that word
//If it does show the symbol, else hide it
function filter(word) {
    let length = items.length
    let collection = []
    let hidden = 0
    for (let i = 0; i < length; i++) {
        if (items[i].value.toLowerCase().includes(word)) {
            $(items[i]).show()
        } else {
            $(items[i]).hide()
            hidden++
        }
    }

    //If all items are hidden, show the empty view
    if (hidden === length) {
        $('#empty').show()
    } else {
        $('#empty').hide()
    }
}

//If the user clicks on any item, set the title of the button as the text of the item
$('#menuItems').on('click', '.dropdown-item', function () {
    var page_load = $('#load_page')
    $('#dropdown_coins').text($(this)[0].value)
    $('#dropdown_coins').attr("data", $($(this)[0]).attr("data"))
    $("#dropdown_coins").dropdown('toggle');
    page_load.show()
    $.ajax({
        type: "GET",
        url: "/HT11/" + $($(this)[0]).attr("data"),
        success: function (json) {
            page_load.hide()
            console.log(json)
            // page.hide()

            // let header = "<br><br><h2>Quy định về sản phẩm được hưởng bảo hành</h2>"
            let header = "<br><br>"

            let content = '<img class="img-fluid" src="/image/Chính sách bảo hảnh.jpg">'
            if (json.type === 0) {
                let footer = '<br><br><label for="card-holder" class="form-label-header">Anh chị chắc chắn sản phẩm thuộc chính sách được bảo hành?</label>' +
                    '<div class="form-check"><a target="_blank" href="https://forms.gle/4GwvPeP1XjEJuwYu7" class="btn btn-primary" role="button">Có</a>&nbsp;&nbsp;&nbsp;' +
                    '<a href="/insurance-end-point" class="btn btn-danger" role="button">Không</a></div>'
                $('#content-insurance').html(header + content + footer);
            } else {
                let footer = '<br><br><label for="card-holder" class="form-label-header">Anh chị chắc chắn sản phẩm thuộc chính sách được bảo hành?</label>' +
                    '<div class="form-check"><a target="_blank" href="/HT11/' + json.id + '/edit" class="btn btn-primary" role="button">Có</a>&nbsp;&nbsp;&nbsp;' +
                    '<a href="/insurance-end-point" class="btn btn-danger" role="button">Không</a></div>'
                $('#content-insurance').html(header + json.content + footer)
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            // page.hide()
            page_load.hide()
            toastr.error(thrownError);
        }
    });
})

buildDropDown(names)

$("#dropdown_coins").on("click", function () {
    if (!$("#dropdown_coins").attr("aria-expanded")) $("#searchCoin").focus();
})
