class ShipmentManagement {
    init() {
        $(document).on('click', '.shipment-actions .applist-menu a', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);
            $('#confirm-change-shipment-status-button').data('target', _self.data('target')).data('status', _self.data('value'));
            let $modal = $('#confirm-change-status-modal');
            $modal.find('.shipment-status-label').text(_self.text().toLowerCase());
            $modal.modal('show');
        });

        $(document).on('click', '#confirm-change-shipment-status-button', event => {
            event.preventDefault();
            let _self = $(event.currentTarget);

            _self.addClass('button-loading');

            $.ajax({
                type: 'POST',
                cache: false,
                url: _self.data('target'),
                data: {
                    status: _self.data('status')
                },
                success: res => {
                    if (!res.error) {
                        Botble.showSuccess(res.message);
                        $('.max-width-1200').load(window.location.href + ' .max-width-1200 > *', () => {
                            $('#confirm-change-status-modal').modal('hide');
                            _self.removeClass('button-loading');
                        });
                    } else {
                        Botble.showError(res.message);
                        _self.removeClass('button-loading');
                    }
                },
                error: res => {
                    Botble.handleError(res);
                    _self.removeClass('button-loading');
                }
            });
        });
    }
}

$(document).ready(() => {
    new ShipmentManagement().init();


    $(document).scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 100ms
        preventDefault: false,
        endChar: [13],
        onComplete: function(barcode, qty){
            validScan = true;
            $('#scannerInput').val(barcode);
            $('#scannerInput').trigger('keyup');
        },
        onError: function(string, qty) {
            console.log('Something went wrong. Try again!');
        }
    });

    $(document).ready(function () {
        var input = document.getElementById("scannerInput");
        // Execute a function when the user releases a key on the keyboard
        input.addEventListener("keyup", function(event) {
            // Number 13 is the "Enter" key on the keyboard
            if (event.keyCode === 13) {
                // Cancel the default action, if needed
                event.preventDefault();
                var $t = $(event.currentTarget);
                $t.addClass('loading');
                $('#product-error').hide();
                $('#scannerInput').removeClass('is-invalid');
                get_product_by_barcode(this.value, $t);
            }
        });
        var form = document.getElementsByTagName("form");
        form[0].addEventListener("keydown", function(event){
            if(event.keyCode === 13){
                event.preventDefault();
            }
        })
    });

});
