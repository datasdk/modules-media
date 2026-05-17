<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test PDF</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
        }
    </style>
</head>
<body>
    <h1>Test PDF</h1>
    <p>This is an automatically generated test PDF.</p>

    @if(isset($params))
        <p>Parameters:</p>
        <ul>
            @foreach($params as $key => $value)
                <li><strong>{{ $key }}:</strong> {{ $value }}</li>
            @endforeach
        </ul>
    @else
        <p>No parameters provided.</p>
    @endif
</body>
</html>
