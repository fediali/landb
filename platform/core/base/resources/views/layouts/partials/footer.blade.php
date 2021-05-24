<div class="page-footer">
    <div class="page-footer-inner">
        <div class="row">
            <div class="col-md-6">
                {!! clean(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) !!}
            </div>
            <div class="col-md-6 text-right">
                @if (defined('LARAVEL_START')) {{ trans('core/base::layouts.page_loaded_time') }} {{ round((microtime(true) - LARAVEL_START), 2) }}
                @endif
            </div>
        </div>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up-circle"></i>
    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="allotment-modal" role="dialog">
    <div class="modal-dialog modal-sm">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex w-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                    <h4 class="modal-title text-center w-100 thread-pop-head">Share Quantity <span
                            class="variation-name"></span></h4>
                    <div></div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label for="name" class="control-label required" aria-required="true"> <label for="name"
                                                                                                          class="control-label"
                                                                                                          aria-required="true">Quantity</label>
                            </label>
                            <input class="form-control is-valid" placeholder="Quantity" data-counter="120" name="name"
                                   type="text" value="asdas" id="name" aria-invalid="false"
                                   aria-describedby="name-error">
                        </div>
                        <button type="submit" name="submit" value="save" class="btn btn-info w-100 mb-4">
                            <i class="fa fa-save"></i> Save
                        </button>
                    </div>

                </div>
            </div>
        </div>


    </div>
</div>


<script>
    var address = '';
    var customer = '';
    $(document).ready(function () {
        $('.notification_read').on('click', function () {
            console.log('working');
            var not_id = $(this).data('id');
            $.ajax({
                url: '{{ route('thread.readNotification') }}',
                type: 'post',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'notification_id': not_id,
                },
                success: function (data) {

                },
                error: function (request, status, error) {
                    toastr['warning']('Notification Unreadable', 'Reading Error');
                }
            });
            console.log(not_id);
        })

        $('.card_fields').hide();
        var card = $("select.card_list option:selected").val();
        console.log(card)
        $('.payment_id').val(card);
        if (card == 0) {
            $('.add_card').show();
        } else {
            $('.add_card').hide();
        }
        $('.card_list').on('change', function () {
            var card = $("select.card_list option:selected").val();
            $('.payment_id').val(card);
            if (card == 0) {
                $('.add_card').show();
            } else {
                $('.add_card').hide();
            }
        });
    });

    // var payButton = document.querySelector('#paybutton');
    var tokenizeButton = document.querySelector('#tokenizebutton');

    // Init FattMerchant API
    var fattJs = new FattJs('LandB-Apparel-c03e1af6c561', {
        number: {
            id: 'fattjs-number',
            placeholder: '0000 0000 0000 0000',
            style: 'width: 90%; height:90%; border-radius: 3px; border: 1px solid #ccc; padding: .5em .5em; font-size: 91%;'
        },
        cvv: {
            id: 'fattjs-cvv',
            placeholder: '000',
            style: 'width: 30px; height:90%; border-radius: 3px; border: 1px solid #ccc; padding: .5em .5em; font-size: 91%;'
        }
    });

    // tell fattJs to load in the card fields
    fattJs.showCardForm().then(handler => {
        console.log('form loaded');
        // var form = document.querySelector('form');
        // form.querySelector('input[name=month]').value = 11;
        // form.querySelector('input[name=year]').value = 2025;
        // form.querySelector('input[name=cardholder-first-name]').value = 'Jon';
        // form.querySelector('input[name=cardholder-last-name]').value = 'Doe';
        // form.querySelector('input[name=phone]').value = '+1111111111111111';
    }).catch(err => {
        console.log('error init form ' + err);
        // reinit form
    });

    fattJs.on('card_form_complete', (message) => {
        // activate pay button
        //payButton.disabled = false;
        var billing = $("#billing_address option:selected").text();

        tokenizeButton.disabled = billing === 'Select Address';
        console.log(message);
    });
    $(document).on('click', '.credit_card', function () {
        $('select#billing_address').empty();
        var baddress = [];
        var customer_id = $('#customer_id').val();
        if (typeof customer_id === "undefined") {
            $('.card_customer_select').show();
            $('.card_fields').hide();
        } else {
            $('.card_customer_select').hide();
            $('.card_fields').show();
            $.ajax({
                url: "{{ route('customers.get-customer-addresses','') }}" + "/" + customer_id,
                type: 'get',
                success: function (data) {
                    $.each(data.data, function (addressID, address) {
                        if (address.type === 'billing') {
                            var data = {
                                id: address.id,
                                text: address.address
                            };
                            console.log(address);
                            html = `<option value="${address.id}">
                                ${address.address}
                            </option>`
                            $('select#billing_address').append(html);
                        }

                    });
                },
                error: function (request, status, error) {
                    toastr['warning']('No Address', 'Reading Error');
                }
            });
            setTimeout(function () {
                getbillingadress();
                getCustomer();
            }, 2000);
        }

    });

    function getbillingadress() {
        var billing = $("#billing_address option:selected").text();
        tokenizeButton.disabled = billing === 'Select Address';
        $.ajax({
            url: "{{ route('customers.get-addresses','') }}" + "/" + $("#billing_address option:selected").val(),
            type: 'get',
            success: function (data) {
                address = data;
            },
            error: function (request, status, error) {
                toastr['warning']('No Address', 'Reading Error');
            }
        });
    }

    function getCustomer() {
        $.ajax({
            url: "{{ route('customers.get-customer','') }}" + "/" + $('#customer_id').val(),
            type: 'get',
            success: function (data) {
                customer = data;
            },
            error: function (request, status, error) {
                toastr['warning']('No Address', 'Reading Error');
            }
        });
    }

    fattJs.on('card_form_incomplete', (message) => {
        // deactivate pay button
        //payButton.disabled = true;
        tokenizeButton.disabled = true;
        console.log(message);
    });

    $('button#tokenizebutton').on('click', () => {
        console.log('working')
        var month = $('.month').val();
        var year = $('.year').val();
        successElement = document.querySelector('.success');
        var errorElement = document.querySelector('.error');
        var loaderElement = document.querySelector('.loader');

        successElement.classList.remove('visible');
        errorElement.classList.remove('visible');
        loaderElement.classList.add('visible');
        var form = document.querySelector('form');
        console.log('customer data', customer.data)
        var extraDetails = {
            firstname: customer.data.detail.first_name,
            lastname: customer.data.detail.last_name,
            email: customer.data.email,
            method: "card",
            month: month,
            year: year,
            phone: customer.data.detail.phone,
            address_1: address.data.address,
            address_city: address.data.city,
            address_state: address.data.state,
            address_zip: address.data.zip_code,
            address_country: address.data.country,
            url: "https://omni.fattmerchant.com/#/bill/",
            validate: false,
        };
        console.log(extraDetails)
        // call tokenize api
        fattJs.tokenize(extraDetails).then((result) => {
            console.log(result);
            if (result) {
                successElement.querySelector('.token').textContent = result.id;
                successElement.classList.add('visible');
                functionAddCard(result, customer.data.id);

            }
            loaderElement.classList.remove('visible');
        })
            .catch(err => {
                console.log(err)
                errorElement.textContent = err.message;
                errorElement.classList.add('visible');
                loaderElement.classList.remove('visible');
            });
    });

    function functionAddCard(result, customer_id) {
        console.log('addingCard', result);
        $.ajax({
            url: '{{ route('customers.create-customer-payment') }}',
            type: 'post',
            data: {
                '_token': '{{ csrf_token() }}',
                'customer_data': result,
                'customer_id': customer_id,
            },
            success: function (data) {
                console.log(data)
            },
            error: function (request, status, error) {
                toastr['warning']('Notification Unreadable', 'Reading Error');
            }
        });
        //console.log(not_id);
    }

</script>
