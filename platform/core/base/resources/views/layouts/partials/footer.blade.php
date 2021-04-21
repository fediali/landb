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
    var payButton = document.querySelector('#paybutton');
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

        // for testing!
        handler.setTestPan('4111111111111111');
        handler.setTestCvv('123');
        var form = document.querySelector('form');
        form.querySelector('input[name=month]').value = 11;
        form.querySelector('input[name=year]').value = 2025;
        form.querySelector('input[name=cardholder-first-name]').value = 'Jon';
        form.querySelector('input[name=cardholder-last-name]').value = 'Doe';
        form.querySelector('input[name=phone]').value = '+1111111111111111';
    })
        .catch(err => {
            console.log('error init form ' + err);
            // reinit form
        });

    fattJs.on('card_form_complete', (message) => {
        // activate pay button
        payButton.disabled = false;
        tokenizeButton.disabled = false;
        console.log(message);
    });

    fattJs.on('card_form_uncomplete', (message) => {
        // deactivate pay button
        payButton.disabled = true;
        tokenizeButton.disabled = true;
        console.log(message);
    });

    document.querySelector('#paybutton').onclick = () => {
        var successElement = document.querySelector('.success');
        var errorElement = document.querySelector('.error');
        var loaderElement = document.querySelector('.loader');

        successElement.classList.remove('visible');
        errorElement.classList.remove('visible');
        loaderElement.classList.add('visible');

        var form = document.querySelector('form');
        var extraDetails = {
            total: 1, // 1$
            firstname: "John",
            lastname: "Doe",
            method: "card",
            month: "10",
            year: "2022",
            phone: "5555555555",
            address_1: "100 S Orange Ave",
            address_2: "Suite 400",
            address_city: "Orlando",
            address_state: "FL",
            address_zip: "32811",
            address_country: "USA",
            validate: false,
            url: "https://omni.fattmerchant.com/#/bill/",

            // validate is optional and can be true or false.
            // determines whether or not fattmerchant.js does client-side validation.
            // the validation follows the sames rules as the api.
            // check the api documentation for more info:
            // https://fattmerchant.com/api-documentation/

            // meta is optional and each field within the POJO is optional also
            meta: {
                reference: 'invoice-reference-num',// optional - will show up in emailed receipts
                memo: 'notes about this transaction',// optional - will show up in emailed receipts
                otherField1: 'other-value-1', // optional - we don't care
                otherField2: 'other-value-2', // optional - we don't care
                subtotal: 1, // optional - will show up in emailed receipts
                tax: 0, // optional - will show up in emailed receipts
                lineItems: [ // optional - will show up in emailed receipts
                    {
                        "id": "optional-fm-catalog-item-id",
                        "item": "Demo Item",
                        "details": "this is a regular, demo item",
                        "quantity": 10,
                        "price": .1
                    }
                ]
            }
        };

        console.log(extraDetails);

        // call pay api
        fattJs.pay(extraDetails).then((result) => {
            console.log('pay:');
            console.log(result);
            if (result.id) {
                successElement.querySelector('.token').textContent = result.payment_method_id;
                successElement.classList.add('visible');
                loaderElement.classList.remove('visible');
            }
        })
            .catch(err => {
                errorElement.textContent = err.child_transactions[0].message;
                errorElement.classList.add('visible');
                loaderElement.classList.remove('visible');
            });
    }

    document.querySelector('#tokenizebutton').onclick = () => {
        var successElement = document.querySelector('.success');
        var errorElement = document.querySelector('.error');
        var loaderElement = document.querySelector('.loader');

        successElement.classList.remove('visible');
        errorElement.classList.remove('visible');
        loaderElement.classList.add('visible');

        var form = document.querySelector('form');
        var extraDetails = {
            total: 1, // 1$
            firstname: "John",
            lastname: "Doe",
            method: "card",
            month: "10",
            year: "2022",
            phone: "5555555555",
            address_1: "100 S Orange Ave",
            address_2: "Suite 400",
            address_city: "Orlando",
            address_state: "FL",
            address_zip: "32811",
            address_country: "USA",
            url: "https://omni.fattmerchant.com/#/bill/",

            // validate is optional and can be true or false.
            // determines whether or not fattmerchant.js does client-side validation.
            // the validation follows the sames rules as the api.
            // check the api documentation for more info:
            // https://fattmerchant.com/api-documentation/
            validate: false,
        };

        // call tokenize api
        fattJs.tokenize(extraDetails).then((result) => {
            console.log('tokenize:');
            console.log(result);
            if (result) {
                successElement.querySelector('.token').textContent = result.id;
                successElement.classList.add('visible');
            }
            loaderElement.classList.remove('visible');
        })
            .catch(err => {
                errorElement.textContent = err.message;
                errorElement.classList.add('visible');
                loaderElement.classList.remove('visible');
            });
    }


    /*var payButton = document.querySelector('#paybutton');
    var tokenizeButton = document.querySelector('#tokenizebutton');


    // Init FattMerchant API
    var fattjs = new FattJs("LandB-Apparel-c03e1af6c561", {
        number: {
            id: 'card-number',     // the html id of the div you want to contain the credit card number field
            placeholder: '0000 0000 0000 0000',    // the placeholder the field should contain
            style: 'height: 30px; width: 100%; font-size: 15px;',    // the style to apply to the field
            type: 'text',   // the input type (optional)
            format: 'prettyFormat'    // the formatting of the CC number (prettyFormat || plainFormat || maskedFormat)
        },
        cvv: {
            id: 'card-cvv',    // the html id of the div you want to contain the cvv field
            placeholder: 'CVV',    // the placeholder the field should contain
            style: 'height: 30px; width: 100%; font-size: 15px;',    // the style to apply to the field
            type: 'text'    // the input type (optional)
        }
    });

    fattjs.showCardForm().then(handler => {
        console.log('form loaded');

        // for testing!
        handler.setTestPan('4111111111111111');
        handler.setTestCvv('123');
        var form = document.querySelector('form');
        form.querySelector('input[name=month]').value = 11;
        form.querySelector('input[name=year]').value = 2025;
        form.querySelector('input[name=cardholder-first-name]').value = 'Jon';
        form.querySelector('input[name=cardholder-last-name]').value = 'Doe';
        form.querySelector('input[name=phone]').value = '+1111111111111111';
    })
        .catch(err => {
            console.log('error init form ' + err);
            // reinit form
        });


    fattjs.on('card_form_complete', (message) => {
        // activate pay button
        payButton.disabled = false;
        tokenizeButton.disabled = false;
        console.log(message);
    });

    fattjs.on('card_form_uncomplete', (message) => {
        // deactivate pay button
        payButton.disabled = true;
        tokenizeButton.disabled = true;
        console.log(message);
    });

    const extraDetails = {
        firstname: "John",
        lastname: "Doe",
        method: "card",
        month: "10",
        year: "2022",
        phone: "5555555555",
        address_1: "100 S Orange Ave",
        address_2: "Suite 400",
        address_city: "Orlando",
        address_state: "FL",
        address_zip: "32811",
        address_country: "USA",
        validate: true,
    };
    $('#tokenizebutton').on('click', function () {
        console.log(extraDetails);
        fattjs.tokenize(extraDetails).then((result) => {
            console.log('tokenize:');
            console.log(result);
            if (result) {
                successElement.querySelector('.token').textContent = result.id;
                successElement.classList.add('visible');
            }
            loaderElement.classList.remove('visible');
        })
            .catch(err => {
                console.log(err)
            });
    });*/

</script>
