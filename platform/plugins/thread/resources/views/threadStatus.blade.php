<link media="all" type="text/css" rel="stylesheet" href="{{asset('vendor/core/core/base/libraries/bootstrap3-editable/css/bootstrap-editable.css')}}">
<script src="{{asset('vendor/core/core/base/libraries/bootstrap3-editable/js/bootstrap-editable.min.js')}}"></script>


<a data-type="select"
   data-source="{{ json_encode([ ['value' => 'pending', 'text' => 'Pending'], ['value' => 'draft', 'text' => 'Draft'], ['value' => 'published', 'text' => 'Published'],  ]) }}"
   data-pk="{{ $item->id }}"
   data-url="{{ route('thread.changeStatus') }}"
   data-value="{{ $item->status }}"
   data-title="Change Status"
   class="editable"
   href="#">
    {{ $item->status }}
</a>
