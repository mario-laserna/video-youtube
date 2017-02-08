<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body>

<br><br>

{!! Form::open(['route' => 'video.store', 'method' => 'POST', 'files'=>true,
                'class' => "form-horizontal form-bordered", 'id' => 'video-form']) !!}


    <div class="form-group row">
        <label class="col-md-3 control-label" for="ciudad">Video</label>
        <div class="col-md-9">
            {!! Form::file('video') !!}
        </div>
    </div>

    <div class="form-group form-actions">
        <div class="col-md-9 col-md-offset-3">
            <button type="submit" class="btn btn-primary"><i class="gi gi-ok_2"></i> Crear</button>
        </div>
    </div>

    {!! Form::close() !!}


<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>