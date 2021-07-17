 
@extends('core/base::layouts.master')
@section('content')
    <div class="max-width-1200" id="main-order">
        <create-order :currency="'{{ get_application_currency()->symbol }}'" :zip_code_enabled="{{ (int)EcommerceHelper::isZipCodeEnabled() }}"></create-order>
        <div class="flexbox-grid no-pd-none">
         <div class="flexbox-content">
            <div class="wrapper-content">
               <div class="pd-all-20"><label class="title-product-main text-no-bold">Order information</label></div>
               <div class="pd-all-10-20 border-top-title-main">
                  <div class="clearfix optionBox">
                     <div class="table-wrapper p-none mb20 ps-relative z-index-4">
                      
                     </div>
                     <div class="box-search-advance product">
                        <div><input type="text" id="search-order" placeholder="Search or create a new product" class="next-input textbox-advancesearch product"></div> 
                        <div  id="order-toggle" class="panel panel-default">
                           <div class="panel-body">
                              <div class="box-search-advance-head" role="button" tabindex="0"><img width="30" src="//hstatic.net/0/0/global/design/imgs/next-create-custom-line-item.svg" alt="icon"> <span data-bind="text:CustomFieldName" class="ml10">Create a new product</span></div>
                              <div class="list-search-data p-4">
                                 <div class="has-loading" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>
                                 <ul class="clearfix" style="">
                                    <li class="item-not-selectable d-flex">
                                       <div class="wrap-img inline_block vertical-align-t float-left"><img src="http://localhost/landb/public/storage/products/1-150x150.jpg" title="Dual Camera 20MP" alt="Dual Camera 20MP" class="thumb-image"></div>
                                       
                                       <div>
                                       <label class="inline_block ml10 mt10 ws-nm" style="width: calc(100% - 50px);">
                                          Dual Camera 20MP
                                          <!---->
                                       </label>
                                       <div class="text-left ml-3">
                                          <div class="clear"></div>
                                          <ul>
                                             <li class="clearfix product-variant">
                                                <a class="color_green float-left">
                                                   <span>
                                                   Green
                                                   <span>/</span></span>
                                                   <span>
                                                      S
                                                      <!---->
                                                   </span>
                                                </a>
                                                <!----> <span><small>&nbsp;(15 product(s) available)</small></span>
                                             </li>
                                             <li class="clearfix product-variant">
                                                <a class="color_green float-left">
                                                   <span>
                                                   Blue
                                                   <span>/</span></span>
                                                   <span>
                                                      S
                                                      <!---->
                                                   </span>
                                                </a>
                                                <!----> <span><small>&nbsp;(15 product(s) available)</small></span>
                                             </li>
                                             <li class="clearfix product-variant">
                                                <a class="color_green float-left">
                                                   <span>
                                                   Red
                                                   <span>/</span></span>
                                                   <span>
                                                      XXL
                                                      <!---->
                                                   </span>
                                                </a>
                                                <!----> <span><small>&nbsp;(15 product(s) available)</small></span>
                                             </li>
                                          </ul>
                                       </div>
                                       </div>
                                    </li> 
                                    <!---->
                                 </ul>
                              </div>
                           </div>
                           <div class="panel-footer">
                              <div class="btn-group float-right">
                                 <button type="button" class="btn btn-secondary disable" disabled="disabled">
                                    <svg role="img" class="svg-next-icon svg-next-icon-size-16 svg-next-icon-rotate-180">
                                       <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-chevron"></use>
                                    </svg>
                                 </button>
                                 <button type="button" class="btn btn-secondary">
                                    <svg role="img" class="svg-next-icon svg-next-icon-size-16">
                                       <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-chevron"></use>
                                    </svg>
                                 </button>
                              </div>
                              <div class="clearfix"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="pd-all-10-20 p-none-t">
                  <div class="row">
                     <div class="col-sm-6">
                        <div class="form-group"><label for="txt-note" class="text-title-field">Note</label> <textarea id="txt-note" name="notes" rows="2" placeholder="Note for order..." class="ui-text-area textarea-auto-height"></textarea></div>
                     </div>
                     <div class="col-sm-6">
                        <div class="table-wrap">
                           <table class="table-normal table-none-border table-color-gray-text text-right">
                              <tbody>
                                 <tr>
                                    <td class="color-subtext">Amount</td>
                                    <td class="pl10">131.00 $</td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <a href="#" class="hover-underline" role="button">
                                          <span><i class="fa fa-plus-circle"></i> Add discount</span> <!---->
                                       </a>
                                       <!---->
                                    </td>
                                    <td class="pl10">0.00 $</td>
                                 </tr>
                                 <tr>
                                    <td>
                                       <a href="#" class="hover-underline" role="button">
                                          <span><i class="fa fa-plus-circle"></i> Add shipping fee</span> <!---->
                                       </a>
                                       <p class="mb0 font-size-12px">Default</p>
                                    </td>
                                    <td class="pl10">0.00 $</td>
                                 </tr>
                                 <tr class="text-no-bold">
                                    <td>Total amount</td>
                                    <td class="pl10">131.00 $</td>
                                 </tr>
                              </tbody>
                           </table>
                        </div>
                     </div>
                  </div>
               </div>
               <div class="pd-all-10-20 border-top-color">
                  <div class="row">
                     <div class="col-12 col-sm-6 col-md-12 col-lg-6">
                        <div class="flexbox-grid-default mt5 mb5">
                           <div class="flexbox-auto-left p-r10"><i class="fa fa-credit-card fa-1-5 color-blue"></i></div>
                           <div class="flexbox-auto-content">
                              <div class="text-upper ws-nm">Confirm payment and create order</div>
                           </div>
                        </div>
                     </div>
                     <div class="col-12 col-sm-6 col-md-12 col-lg-6 text-right"><button class="btn btn-primary">Paid
                        </button> <button class="btn btn-primary ml15">Pay later
                        </button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="flexbox-content flexbox-right">
            <div class="wrapper-content mb20">
               <div>
                  <div class="next-card-section">
                     <div class="flexbox-grid-default mb15">
                        <div class="flexbox-auto-content"><label class="title-product-main">Customer information</label></div>
                     </div>
                     <div class="findcustomer">
                        <div class="box-search-advance customer">
                           <div><input type="text" placeholder="Search or create a new customer" class="next-input textbox-advancesearch customer"></div>
                           <div class="panel panel-default active hidden">
                              <div class="panel-body">
                                 <div class="box-search-advance-head" role="button" tabindex="0">
                                    <div class="flexbox-grid-default flexbox-align-items-center">
                                       <div class="flexbox-auto-40"><img width="30" src="//hstatic.net/0/0/global/design/imgs/next-create-customer.svg" alt="icon"></div>
                                       <div class="flexbox-auto-content-right"><span>Create new customer</span></div>
                                    </div>
                                 </div>
                                 <div class="list-search-data">
                                    <div class="has-loading" style="display: none;"><i class="fa fa-spinner fa-spin"></i></div>
                                    <ul class="clearfix" style="">
                                       <li><span>No customer found!</span></li>
                                    </ul>
                                 </div>
                              </div>
                              <!---->
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!---->
            </div>
         </div>
         <!----> <!----> <!----> <!----> <!----> <!----> <!----> <!---->
      </div>
    </div>
    <style>
    .box-search-advance-head {
    padding: 10px 15px;
    border-bottom: 1px solid #e0e6ec;
    margin-left: -15px;
    margin-right: -15px;
    cursor: pointer;
} 
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script type="text/javascript">
$(document).ready(function () {
    $("#search-order").click(function () {
        $("#order-toggle").toggle();
    });
    $('.color_green').click(function() {
        $('.table-wrapper').after('<table class="table-normal "><tbody><tr class="remove"><td class="width-60-px min-width-60-px"><div class="wrap-img vertical-align-m-i"><img src="http://localhost/landb/public/storage/products/4-150x150.jpg" alt="Red &amp; Black Headphone" class="thumb-image"></div></td><td class="pl5 p-r5 min-width-200-px"><a href="http://localhost/landb/public/admin/ecommerce/products/edit/33" target="_blank" class="hover-underline pre-line">Red &amp; Black Headphone</a> <p class="type-subdued"><span> Brown <span>/</span></span><span> M </span></p></td><td class="pl5 p-r5 width-100-px min-width-100-px text-center"><div class="dropup dropdown-priceOrderNew"><div class="inline_block dropdown"><a class="wordwrap hide-print">53 $</a></div></div></td><td class="pl5 p-r5 width-20-px min-width-20-px text-center"> x</td><td class="pl5 p-r5 width-100-px min-width-100-px"><input type="number" min="1" class="next-input p-none-r"></td><td class="pl5 p-r5 width-100-px min-width-100-px text-center">53 $ </td><td class="pl5 p-r5 text-right width-20-px min-width-20-px"><a><svg class="svg-next-icon svg-next-icon-size-12 "><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#next-remove"></use></svg></a></td></tr></tbody></table>');
     
});
$('.optionBox').on('click','.remove',function() {
 	$(this).parent().remove(); 
});
});
</script>
@stop

