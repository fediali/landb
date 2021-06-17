@extends('core/base::layouts.master')

@section('content')
    <div class="">
        <div class="clearfix"></div>

        <div id="main">
            <form method="POST" action="{{route('timeline.store')}}" accept-charset="UTF-8"
                  enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-9">
                        <div class="main-form">
                            <div style="background: #f1f1f1; padding: 15px; margin-bottom: 15px;   border-radius: 10px;" class="form-body">
                                <div class="form-group">

                                    <label for="product_link" class="control-label required cloneItem"
                                           aria-required="true">Product Link</label>
                                    <input class="form-control" placeholder="Product Link" data-counter="120"
                                           name="product_link[]" type="text" id="product_link">
                                           <button
                                        style="position:absolute; top: -3px; right:0;background: #d64635;color: #fff;border: none;   border-radius: 3px;"
                                        class="remove remove-btn" data-toggle="tooltip" data-placement="top"
                                        title="Delete"><i clas="fa fa-trash-o" aria-hidden="true"></i>X
                                    </button>
                                </div>

                                <div class="form-group">
                                    <label for="product_desc" class="control-label required cloneItem"
                                           aria-required="true">Description</label>
                                    <textarea class="form-control" placeholder="Name"
                                              name="product_desc[]" id="product_desc">

                                    </textarea>

                                </div>

                                <div class="form-group">
                                    <label for="file" class="control-label cloneItem">Select File</label>
                                    @include('core/base::forms.partials.images', ['name' => 'product_image[]', 'values' => ''])
                                   
                                </div>

                                <div class="clearfix"></div>
                            </div>
                        </div>


                    </div>
                    <div class="col-md-3 right-sidebar">
                        <div class="widget meta-boxes form-actions form-actions-default action-horizontal">
                            <div class="widget-title">
                                <h4>
                                    <span>Publish</span>
                                </h4>
                            </div>
                            <div class="widget-body">
                                <div class="btn-set">
                                    <button type="submit" name="submit" value="save" class="btn btn-info">
                                        <i class="fa fa-save"></i> Save
                                    </button>
                                    &nbsp;
                                    <button type="submit" name="submit" value="apply" class="btn btn-success">
                                        <i class="fa fa-check-circle"></i> Save &amp; Edit
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="waypoint"></div>


                        <div class="widget meta-boxes">
                            <div class="widget-title">
                                <h4><label for="name" class="control-label required" aria-required="true">Name</label>
                                </h4>
                            </div>
                            <div class="widget-body">
                                <input class="form-control" placeholder="Name" data-counter="120" name="name"
                                       type="text" id="name">


                            </div>
                        </div>
                        <div class="widget meta-boxes">
                            <div class="widget-title">
                                <h4><label for="status" class="control-label required"
                                           aria-required="true">Status</label></h4>
                            </div>
                            <div class="widget-body">
                                <div class="ui-select-wrapper form-group">
                                    <select
                                        class="form-control select-full ui-select ui-select"
                                        id="status" name="status">
                                        <option value="schedule">Schedule</option>
                                        <option value="published">Published</option>
                                        <option value="disabled">Disabled</option>
                                    </select>
                                    <svg class="svg-next-icon svg-next-icon-size-16">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink"
                                             xlink:href="#select-chevron"></use>
                                    </svg>
                                </div>


                            </div>
                        </div>
                        <div class="widget meta-boxes">
                            <div class="widget-title">
                                <h4><label for="date" class="control-label">Date</label></h4>
                            </div>
                            <div class="widget-body">
                                <div class="input-group">
                                    <input class="form-control" name="date" type="date">
                                </div>


                            </div>
                        </div>
                        <div class="widget meta-boxes">
                            <div class="widget-title">
                                <h4><label for="clone" class="control-label">Add More</label></h4>
                            </div>
                            <div class="widget-body">
                                <button class="btn btn-info cloneTimeline clone" type="button">Add More</button>

                            </div>
                        </div>

                    </div>
                </div>

            </form>

        </div>
    </div>
@stop

