<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
</head>
<body>

<div>
    <table>
        <thead>
            <th>Product Code</th>
            <th>Quantity</th>
        </thead>
        <tbody>
        @foreach($data as $c => $q)
            <tr>
                <td>{{$c}}</td>
                <td>{{$q}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

</body>
</html>
