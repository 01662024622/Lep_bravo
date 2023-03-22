$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

function addMessage(){
    console.log(1)
}

function showMessageDetail(id){
    var html=`<div class="message-detail" id="message-user-`+id+`">
        <div class="message-detail-header">
            <div class="dropdown-item d-flex align-items-center">
                <div class="dropdown-list-image mr-2">
                    <img class="rounded-circle message-detail-avata" src="https://source.unsplash.com/fn_BT9fwg_E/60x60"
                         alt="">
                    <div class="status-indicator bg-success"></div>
                </div>
                <div class="font-weight-bold">
                    <div class="text-truncate">Thangvm <i class="fa fa-chevron-down" aria-hidden="true"></i></div>
                    <div class="small text-gray-500">Đang hoạt động</div>
                </div>
            </div>
            <div class="message-header-action">
                <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                <i class="fa fa-minus" aria-hidden="true"></i>
                <i class="fa fa-times" aria-hidden="true" onclick="removeMessage(`+id+`)"></i>
            </div>
        </div>
        <div class="message-detail-body"></div>
        <div class="message-detail-bottom d-flex">
            <div class="message-detail-bottom-action">
                <i class="fa fa-file-image-o" aria-hidden="true"></i>\t&nbsp;
                <input class="message-text" placeholder="Aa">\t&nbsp;
                <i class="fa fa-paper-plane" aria-hidden="true"></i>
            </div>
        </div>
    </div>`
    $('#message-container').prepend(html)
}
function removeMessage(id){
    $('#message-user-'+id).remove();
}
