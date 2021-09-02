const $button = $("#bitbag-inpost-admin-select-point");
const $buttonText = $("#bitbag-inpost-admin-select-point-text");
const path = $button.data('path');
const orderId = $button.data('order-id');

$button.click(function (event) {
    event.preventDefault();
    easyPack.modalMap(function(point, modal) {
        modal.closeModal();
        $.ajax({
            method: "POST",
            url: path + "?orderId=" + orderId + "&name=" + point.name,
        })
            .done(function () {
                $buttonText.text(point.name);
            })
            .fail(function () {
                $buttonText.text('Try again');
            })
        ;
    }, { width: 500, height: 600 });
});