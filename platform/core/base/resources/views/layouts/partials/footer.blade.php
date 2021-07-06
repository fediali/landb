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

<div class="modal fade" id="merge_customer_modal" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex w-100">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">X</button>
                    <h4 class="modal-title text-center w-100 thread-pop-head color-white">Merge Customer <span
                            class="variation-name"></span></h4>
                    <div></div>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <form method="post" action="{{route('customers.merge-customer')}}">
                        @csrf
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="name" class="control-label" aria-required="true">Customer List
                                </label>
                                <input type="hidden" value="" name="user_id_one" id="user_id_one">
                                <select name="user_id_two" id="merge_customer_list"
                                        class="form-control is-valid select-search-full">
                                    <option disabled> Select Customer</option>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary btn-apply" type="submit">Merge</button>
                    </form>
                    <div class="col-lg-12">
                        <table class="merge_account">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Operation</th>
                            </tr>
                            </thead>
                            <tbody class="merge_account_body">

                            </tbody>

                        </table>
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
        $(document).on('click','.merge-customer', function () {
            $('.merge_account_body').empty();
            var customer_id = $(this).data('id');
            $('#user_id_one').val(customer_id);
            $.ajax({
                url: "{{ route('customers.get-list-customers-for-select','') }}" + "/" + customer_id,
                type: 'get',
                success: function (data) {
                    $.each(data.data.customer, function (customerID, customerName) {
                        console.log('asd', customerID, customerName);
                        html = `<option value="${customerName.id}">
                            ${customerName.name} - (${customerName.email})
                        </option>`
                        $('select#merge_customer_list').append(html);
                    });

                    $.each(data.data.merge, function (mergeID, mergeAccount) {

                        html = ` <tr>
                                    <td>${mergeAccount.id}</td>
                                    <td>${mergeAccount.name}</td>
                                    <td>${mergeAccount.email}</td>
                                    <td><a href="{{route('customers.merge-customer-delete','')}}/${mergeAccount.id}" <i class="fa fa-trash"></i></td>
</tr> `
                        $('.merge_account_body').append(html);
                    });
                },
                error: function (request, status, error) {
                    toastr['warning']('No Address', 'Reading Error');
                }
            });
            $('#merge_customer_modal').modal('toggle');
        });


        payment_method()

        $('.method').on('change', function () {
            payment_method();
        })
        var card = $("select.card_list option:selected").val();
        $('.payment_id').val(card);
        if (card == 0) {
            $('.add_card').show();
        } else {
            $('.add_card').hide();
        }

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

        function payment_method() {
            var payment_method = $("select.method option:selected").val();

            if (payment_method == 'omni-pay') {
                $('.card-area').show();
            } else {
                $('.card-area').hide();
            }
        }

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
                getCards();
            }, 500);
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
                toastr['warning']('No Customer', 'Reading Error');
            }
        });
    }

    function getCards() {
        var htmls = '';
        $('#card_id').empty();
        $.ajax({
            url: "{{ route('customers.get-cards','') }}" + "/" + $('#customer_id').val(),
            type: 'get',
            success: function (data) {
                if (data.data !== 0) {
                    card = data;
                    console.log(card)
                    $.each(card.data, function (index, element) {
                        htmls += "<option value='" + (element.id) + "'>" + element.nickname + "</option>";
                    });
                }
                htmls += "<option value='0'> Add New Card</option>";
                $('#card_id').html(htmls);
                var card = $("select.card_list option:selected").val();
                $('.payment_id').val(card);
                if (card == 0) {
                    $('.add_card').show();
                } else {
                    $('.add_card').hide();
                }
            },
            error: function (request, status, error) {
                toastr['warning']('No Cards', 'Reading Error');
            }
        });
    }

    $('.card_list').on('change', function () {
        var card = $("select.card_list option:selected").val();
        $('.payment_id').val(card);
        if (card == 0) {
            $('.add_card').show();
        } else {
            $('.add_card').hide();
        }
    })

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


{{--CUSTOMER ADDRESS MODAL--}}
<script>
    $(document).ready(function () {
        $('.threadSampletech').on('click', function () {
            var url = $('.threadSampletech').val();
            console.log(url);
            window.open(url);

        })

        $('.address-country').on('change', function () {
            // get_states($('select[name="address_state"]'), this.value, '{{--{{ route('ajax.getStates') }}--}}');
        });

        $('.delete_address').on('click', function (e) {
            var data = $(this).data('id');
            var r = confirm("Are you sure you want to delete this address?");
            if (r == true) {
                window.location.replace('{{ route('customers.delete-address') }}' + '?id=' + data);
            }

        });
        $('.toggle-edit-address').on('click', function (e) {
            var data = $(this).data('row');

            $('input[name=address_id]').val(data.id);
            $('input[name=address_first_name]').val(data.first_name);
            $('input[name=address_last_name]').val(data.last_name);
            $('input[name=address_company]').val(data.company);
            $('input[name=address_phone]').val(data.phone);
            $('input[name=address_address]').val(data.address);
            $('input[name=address_city]').val(data.city);
            $('input[name=address_zip_code]').val(data.zip_code);

            get_countries($('select[name="address_country"]'), data.country);
            get_states($('select[name="address_state"]'), data.country, data.state);


            $('#edit_address').modal('toggle');
            console.log(data);
        });


        $('#address_form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                type: 'post',
                url: '{{ route('customers.update-customer-address') }}',
                data: $(this).serialize(),
                success: function (result) {
                    toastr['success']('Success', 'Address updated successfully', result.message);
                    $('#edit_address').modal('toggle');
                    location.reload();
                },
                error: function () {
                    toastr['error']('Error!', 'Server Error');
                }
            });

        });
    });

    function get_states(thiss, country, old) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('ajax/get_states') }}',
            data: {
                'country': country
            },
            beforeSend: function () {
            },

            success: function (result) {
                thiss.find('option').remove();
                jQuery.each(result, function (index, state) {
                    if (index === old) {
                        thiss.append(new Option(state, index, null, true));
                    } else {
                        thiss.append(new Option(state, index));
                    }
                });
            },
            error: function (e) {
            }
        })
    }

    function get_countries(thiss, old) {
        $.ajax({
            type: 'GET',
            url: '{{ URL::to('ajax/get_countries') }}',
            async: false,
            success: function (result) {
                thiss.find('option').remove();
                jQuery.each(result, function (index, country) {
                    if (index === old) {
                        console.log('old: ' + old + " new: " + index)
                        thiss.append(new Option(country, index, null, true));
                    } else {
                        thiss.append(new Option(country, index));
                    }

                });
            },
            error: function (e) {
            }
        })
    }
</script>


<script>
    $(document).ready(function () {

        $('#main').on('click', '.remove', function () {
            $('.remove').closest('#main').find('.form-body').not(':first').last().remove();
        });
        $('#main').on('click', '.clone', function () {
            $('.clone').closest('#main').find('.form-body').first().clone().appendTo('.main-form');
        });

        $('.accordion-list-c > li > .answer').hide();

        $('.accordion-list-c h3').click(function () {
            if ($('.accordion-list-c > li').hasClass("active-h")) {
                $('.accordion-list-c > li').removeClass("active-h").find(".answer").slideUp();
            } else {
                $(".accordion-list > li.active-h .answer").slideUp();
                $(".accordion-list > li.active-h").removeClass("active-h");
                $('.accordion-list-c > li').addClass("active-h").find(".answer").slideDown();
            }
            return false;
        });

    });


</script>

<script src="{{ asset('landb/js/bootstrap.js') }}"></script>
