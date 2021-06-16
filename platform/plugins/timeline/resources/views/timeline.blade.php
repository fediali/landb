@extends('core/base::layouts.master')

@section('content')
    <div class="">
        <div class="clearfix"></div>

        <div id="main">
            <form method="POST" action="http://landb.co/admin/timelines/create" accept-charset="UTF-8"
                  id="form_2e8c268d1fefa6f40f069e929ebc92bb" novalidate="novalidate"><input name="_token" type="hidden"
                                                                                            value="Q7Iz0A8LyimsBmoRmXuMUcEjWqknDsPxd2UY3aVp">

                <div class="row">
                    <div class="col-md-9">
                        <div class="main-form">
                            <div class="form-body">
                                <div class="form-group">

                                    <label for="product_link" class="control-label required cloneItem"
                                           aria-required="true">Product Link</label>
                                    <input class="form-control" placeholder="Product Link" data-counter="120"
                                           name="product_link" type="text" id="product_link">
                                </div>

                                <div class="form-group">
                                    <label for="product_desc" class="control-label required cloneItem"
                                           aria-required="true">Description</label>
                                    <textarea class="form-control" placeholder="Name" data-counter="120"
                                              name="product_desc" cols="50" rows="10" id="product_desc">

                                    </textarea>

                                </div>

                                <div class="form-group">
                                    <label for="file" class="control-label cloneItem">Select File</label>


                                    @include('core/base::forms.partials.images', ['name' => 'images[]', 'values' => ''])

                                    <button
                                        style="position:absolute; bottom: 50px; right:0;background: #d64635;color: #fff;border: none;   border-radius: 3px;"
                                        class="remove remove-btn" data-toggle="tooltip" data-placement="top"
                                        title="Delete"><i clas="fa fa-trash-o" aria-hidden="true"></i>X
                                    </button>
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
                        <div class="form-actions form-actions-fixed-top hidden">
                            <ol class="breadcrumb">

                                <li class="breadcrumb-item"><a href="http://landb.co/admin">Dashboard</a></li>


                                <li class="breadcrumb-item"><a href="http://landb.co/admin/timelines">Timelines</a></li>


                                <li class="breadcrumb-item active">New timeline</li>

                            </ol>


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
                                        class="form-control select-full ui-select ui-select select2-hidden-accessible"
                                        id="status" name="status" tabindex="-1" aria-hidden="true">
                                        <option value="published">Published</option>
                                        <option value="draft">Draft</option>
                                        <option value="pending">Pending</option>
                                        <option value="schedule">schedule</option>
                                        <option value="hidden">hidden</option>
                                        <option value="active">active</option>
                                        <option value="disabled">disabled</option>
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
                                    <input class="form-control  datepicker" name="date" type="text" value="06/16/2021"
                                           id="date">
                                    <span class="input-group-prepend">
            <button class="btn default" type="button">
                <i class="fa fa-calendar"></i>
            </button>
        </span>
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


