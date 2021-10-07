<section class="breadcrumb_wrap">
    <div class="pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Home</li>
                <li class="breadcrumb-item active" aria-current="page">
                    <b>Contract</b>
                </li>
            </ol>
        </nav>
    </div>
</section>
<section class="shoplisting_wrap pl-5 pr-5 mbtb-pl-1 mbtb-pr-1">
    <div class="row">
        <div class="col-lg-2 mt-2"></div>
        <div class="col-lg-8 mt-2">
            <div class="row">
                <div class="col-lg-12 pl-3 pr-3">
                    <h4 class="text-center txt mb-5 mt-5">
                        TEXAS SALES AND USE TAX RESALE CERTIFICATE
                    </h4>
                    <form method="POST" action="{{ route('customer.edit-account-post', 'tax_certificate') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-7">
                                <p class="textbox-label">
                                    Name of purchaser, firm or agency as shown on permit *
                                </p>
                                <input class="input-textbox form-control @error('purchaser_name') is-invalid @enderror" type="text"  name="purchaser_name" value="{{ old('purchaser_name',@$user->taxCertificate->purchaser_name) }}"/>
                                @error('purchaser_name')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-5">
                                <p class="textbox-label">Phone (Area code and number) *</p>
                                <input class="input-textbox form-control @error('purchaser_phone') is-invalid @enderror" type="text"  name="purchaser_phone" value="{{ old('purchaser_phone',@$user->taxCertificate->purchaser_phone) }}"/>
                                @error('purchaser_phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">
                                    Address (Street & number, P.O. Box or Route number) *
                                </p>
                                <input class="input-textbox form-control @error('purchaser_address') is-invalid @enderror" type="text"  name="purchaser_address" value="{{ old('purchaser_address',@$user->taxCertificate->purchaser_address) }}"/>
                                @error('purchaser_address')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">City, State, ZIP Code *</p>
                                <input class="input-textbox form-control @error('purchaser_city') is-invalid @enderror" type="text"  name="purchaser_city" value="{{ old('purchaser_city',@$user->taxCertificate->purchaser_city) }}"/>
                                @error('purchaser_city')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">
                                    Texas Sales and Use Tax Permit Number (must contain 11
                                    digits) *
                                </p>
                                <input class="input-textbox form-control @error('permit_no') is-invalid @enderror" type="text"  name="permit_no" value="{{ old('permit_no',@$user->taxCertificate->permit_no) }}"/>
                                @error('permit_no')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">
                                    Out-of-state retailer's registration number or Federal
                                    Taxpayers Registry (RFC) number for retailers based in
                                    Mexico *
                                </p>
                                <input class="input-textbox form-control @error('registration_no') is-invalid @enderror" type="text"  name="registration_no" value="{{ old('registration_no',@$user->taxCertificate->registration_no) }}"/>
                                @error('registration_no')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12 mt-2 mb-5">
                                <p class="textbox-label">
                                    Retailers based in Mexico must also provide a copy of their
                                    Mexico registration form to the seller.)
                                </p>
                            </div>
                            <p class="textbox-label pl-2 pr-2">
                                <b>
                                    I, the purchaser named above, claim the right to make a
                                    non-taxable purchase (for resale of the taxable items
                                    described below or on the attached order or invoice) from:
                                </b>
                            </p>
                            <div class="col-lg-12">
                                <div class="row mt-3">
                                    <div class="col-lg-2 col-5">
                                        <p class="tax-label ">Seller:</p>
                                    </div>
                                    <div class="col-lg-3 col-7">
                                        <p class="tax-address">L&B</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-2 col-5">
                                        <p class="tax-label ">Street address:</p>
                                    </div>
                                    <div class="col-lg-3 col-7">
                                        <p class="tax-address">12801 N STEMMONS FWY STE 710</p>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-lg-2 col-5">
                                        <p class="tax-label ">City, State, ZIP code:</p>
                                    </div>
                                    <div class="col-lg-3 col-7">
                                        <p class="tax-address">FARMERS BRANCH, TX 75234</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">
                                    Description of items to be purchased on the attached order
                                    or invoice. *
                                </p>
                                <textarea rows="4" class="input-textbox form-control @error('items_description') is-invalid @enderror" name="items_description">{{ old('items_description',@$user->taxCertificate->items_description) }}</textarea>
                                @error('items_description')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <p class="textbox-label">
                                    Description of the type of business activity generally
                                    engaged in or type of items normally sold by the purchase. *
                                </p>
                                <textarea rows="4" class="input-textbox bg-white form-control @error('business_description') is-invalid @enderror" name="business_description">{{ old('business_description',@$user->taxCertificate->business_description) }}</textarea>
                                @error('business_description')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <input class="mt-1 mr-2 checkk" id="check1" type="checkbox" value=" ">
                                    <label class="mr-2 tax-checkbox-label" for="vehicle1"> The taxable items described above, or on the attached order or invoice, will be resold, rented or leased by me within the geographical limits of the United States of America, its territories and possessions or within the geographical limits of the United Mexican States, in their present form or attached to other taxable items to be sold.</label>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <input class="mt-1 mr-2 checkk"  id="check2" type="checkbox" value=" ">
                                    <label class="mr-2 tax-checkbox-label" for="vehicle1"> I understand that if I make any use of the items in other retention, demonstration or display while holding them for sale, lease or rental, I must pay sales tax on the the items at the time of use based upon either the purchase price or the fair market rental value for the period of time used.</label>
                                </div>

                            </div>
                            <div class="col-lg-12">
                                <div class="d-flex">
                                    <input class="mt-1 mr-2 checkk" id="check3" type="checkbox" value=" ">
                                    <label class="mr-2 tax-checkbox-label" for="vehicle1"> I understand that it is a criminal offense to give a resale certificate to the seller for taxable items that i know, at the time of purchase, are purchased for use rather than for the purpose of resale, lease or rental, and depending on the amount of tax evaded, the offence may range from a Class C misdemeanor to a felony of the second degree.</label>
                                </div>

                            </div>
                            <div class="col-lg-12 mt-2 mb-5 text-center">
                                <p class="textbox-label">
                                    The certificate should be furnished to the supplier. Do not
                                    send the completed certificate to the Comptroller of Public
                                    Accounts.
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="refer-area mt-4">
                                    <div class="row">
                                        <div class="col-lg-7">
                                            <p class="textbox-label">Title</p>
                                            <input class="input-textbox bg-white form-control @error('title') is-invalid @enderror" type="text"  name="title" value="{{ old('title',@$user->taxCertificate->title) }}"/>
                                            @error('title')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-5">
                                            <p class="textbox-label">Date</p>
                                            <input class="input-textbox bg-white form-control @error('date') is-invalid @enderror" type="date"  name="date" value="{{ old('date',@$user->taxCertificate->date) }}"/>
                                            @error('date')
                                            <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-lg-7">
                                            <p class="textbox-label">Purchaser Sign </p>
                                            @if(!empty(@$user->taxCertificate->purchaser_sign))
                                                @if(str_contains($user->taxCertificate->purchaser_sign, 'data:image/png;base64'))
                                                    <img class="img-responsive" src="{{ ($user->taxCertificate->purchaser_sign) }}" alt="Image" title="Image Not Available"  height="120px" width="130px"/>
                                                @else
                                                    <img class="img-responsive" src="{{ asset($user->taxCertificate->purchaser_sign) }}" alt="Image" title="Image Not Available"  height="120px" width="130px"/>
                                                @endif
                                            @else
                                                <span id="undo-sign" class="fa fa-undo"></span>
                                                <div id="signature"></div>
                                            @endif
                                        </div>
                                        <input type="hidden" value="" name="purchaser_sign">
                                        @error('purchaser_sign')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-4">
                        <div class="col-lg-5"></div>
                        <div class="col-lg-2">
                            <input type="submit" class="btn border-btn w-100 tax-submit-btn" value="Submit" disabled>
                        </div>
                        <div class="col-lg-5"></div>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-2 mt-2"></div>
    </div>
</section>
