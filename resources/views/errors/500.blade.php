<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
<style>

.maintenance_header{
	text-align: center;
	font-size: 40px;
	font-weight: 600;
	margin-top: 100px;
}


.maintenance_body{
	position: fixed;
	background: url("{!! url('images/error500.jpg') !!}");
	height: 100%;
	width: 100vw;
	margin: 0px;
	padding: 0xp;
	background-position: center top;
    background-repeat: no-repeat;
    text-align: center;
    /*background-size: cover;
    /*opacity: 0.4;
    filter: alpha(opacity=40);*/
}

.opacity{
	opacity: 1;
    filter: alpha(opacity=100);
}

body{
	margin: 0px;
}
</style>
<div class="maintenance_body">
	<h1>Oh snap, Error 500 Occured</h1>
	<div><a href="{{ url('/') }}">Click Here</a> to go to Homepage. </div>
	<div style="padding: 5px;">Need Help?  <i class="fa fa-envelope-o"></i> hai.djpbn@kemenkeu.go.id <i class="fa fa-phone"></i> 14090</div>
	<div>Copyright &copy; 2017 - Direktorat Sistem Informasi dan Teknologi Perbendaharaan</div>
</div>


