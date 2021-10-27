import {GeoWidget} from '../../common/js/geowidget';

new GeoWidget().init();

const $button = $('bb-inpost-point-btn');
const path = $button.data('path');
const orderId = $button.data('order-id');
console.log(orderId);

// $button.click(function (event) {
//     event.preventDefault();
//     easyPack.modalMap(function(point, modal) {
//         modal.closeModal();
//         $.ajax({
//             method: "POST",
//             url: path + "?orderId=" + orderId + "&name=" + point.name,
//         })
//             .done(function () {
//                 // $buttonText.text(point.name);
//             })
//             .fail(function () {
//                 // $buttonText.text('Try again');
//             })
//         ;
//     }, { width: 500, height: 600 });
// });
