<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    Hi,
    <br>
    Please see attached New PO {{$order_no}}.
    <br>
    Please note on PP Sample Due Date {{$pp_sample_date}}.
    <br>
    Thread Detail Link: <a href="{{url('/admin/threads/details', $thread_id)}}">click here</a>
    Thanks!
    <br/>
</div>

</body>
</html>
