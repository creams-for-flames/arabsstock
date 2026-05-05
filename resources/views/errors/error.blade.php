<!DOCTYPE html>
<html>
<head>
	<title> Oops Error </title>
 <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
 </head>
<body> 
<div class="container">
  <br>
  <br>
  <br>
  <br>
  <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <div class="panel panel-danger">
        <div class="panel-heading">
          <h3 class="text-center">
          <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Oops:
          <small> <b> Error</b></small>
          </h3>
        </div>
        <div class="panel-body">

            <ul class="list-group">

 
				@if(is_string($error))
              <li class="list-group-item"> {!! $error !!} </li>
			   @elseif(is_array($error))	 
                  @foreach($error as $err)
                  <li class="list-group-item"> {!! $err !!} </li>
                  @endforeach
                 @endif
              </ul>
          </div>
        </div>
      </div>
      <div class="col-md-2">

      </div>
    </div>

</div>
</body>
</html>