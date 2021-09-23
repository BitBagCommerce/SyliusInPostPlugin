const $button = $("#bitbag-inpost-checkout-select-point");
const $buttonText = $("#bitbag-inpost-checkout-select-point-text");
const path = $button.data('path');

$button.click(function (event) {
    event.preventDefault();
    easyPack.modalMap(function(point, modal) {
        modal.closeModal();
        $.ajax({
            method: "GET",
            url: path + "?name=" + point.name
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