@extends('core/base::layouts.master')

@section('content')
    <div class="p-3 bg-white" >
        <div class="clearfix"></div>
        <div id="main">

            <div class="row">
                <div class="col-md-12">
                    <form method="post" action="{{route('threadvariationsamples.uploadSampleMedia', $threadvariationsample->id)}}" enctype="multipart/form-data">
                        @csrf
                        <div class="col-md-6 form-group">
                            <input type="hidden" name="thread_variation_sample_id" value="{{$threadvariationsample->id}}">
                            <label class="font-bold">Select Image:</label>
                            <input class="form-control" name="media_file" type="file" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12 mb-3">
                    <h5 class="order-detail">Sample Media List </h5>
                </div>
            </div>

            <div class="row">
                @foreach($threadvariationsample->sample_media as $sample_media)
                    <div class="col-lg-3">
                        <img src="{{asset($sample_media->media)}}" width="300" height="200">
                    </div>
                @endforeach
            </div>

        </div>
    </div>



@stop


@section('javascript')
<script>

</script>
@endsection
