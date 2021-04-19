<div class="page-footer">
    <div class="page-footer-inner">
        <div class="row">
            <div class="col-md-6">
                {!! clean(trans('core/base::layouts.copyright', ['year' => now()->format('Y'), 'company' => setting('admin_title', config('core.base.general.base_name')), 'version' => get_cms_version()])) !!}
            </div>
            <div class="col-md-6 text-right">
                @if (defined('LARAVEL_START')) {{ trans('core/base::layouts.page_loaded_time') }} {{ round((microtime(true) - LARAVEL_START), 2) }}s @endif
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
                            <h4 class="modal-title text-center w-100 thread-pop-head">Share Quantity <span class="variation-name"></span></h4>
                            <div></div>
                        </div>
                    </div> 
                    <div class="modal-body"> 
                    <div class="row">
                        <div class="col-lg-12">
                        <div class="form-group">    
                            <label for="name" class="control-label required" aria-required="true">                            <label for="name" class="control-label" aria-required="true">Quantity</label>
</label>
                            <input class="form-control is-valid" placeholder="Quantity" data-counter="120" name="name" type="text" value="asdas" id="name" aria-invalid="false" aria-describedby="name-error"> 
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