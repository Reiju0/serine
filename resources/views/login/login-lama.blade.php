<!DOCTYPE html>
<html lang="en">
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
        <meta name="description" content="Apex admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
        <meta name="keywords" content="admin template, Apex admin template, dashboard template, flat admin template, responsive admin template, web app">
        <meta name="author" content="PIXINVENT">
        <title>Serine KPPN Tanjung Pandan</title>
        <link rel="apple-touch-icon" sizes="60x60" href="{{url('/apex/img/ico/apple-icon-60.png')}}">
        <link rel="apple-touch-icon" sizes="76x76" href="{{url('/apex/img/ico/apple-icon-76.png')}}">
        <link rel="apple-touch-icon" sizes="120x120" href="{{url('/apex/img/ico/apple-icon-120.png')}}">
        <link rel="apple-touch-icon" sizes="152x152" href="{{url('/apex/img/ico/apple-icon-152.png')}}">
        <!-- <link rel="shortcut icon" type="image/x-icon" href="{{url('/apex/img/ico/favicon.ico')}}"> -->
        <link rel="shortcut icon" type="image/png" href="{{url('images/fave-icon-32.png')}}">

        <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
        <!-- BEGIN VENDOR CSS-->
        <!-- font icons-->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.core.min.css" rel="stylesheet">
		<link href="https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="{{url('/apex/fonts/feather/style.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/apex/fonts/simple-line-icons/style.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/apex/fonts/font-awesome/css/font-awesome.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/apex/vendors/css/perfect-scrollbar.min.css')}}">
        <link rel="stylesheet" type="text/css" href="{{url('/apex/vendors/css/prism.min.css')}}">
            <link rel="stylesheet" type="text/css" href="{{url('/apex/vendors/css/tables/datatable/datatables.min.css')}}">

  <link rel="stylesheet" type="text/css" href="{{url('/plugin/pnotify/pnotify.custom.min.css')}}">


  <link href="{{url('/css/custom.css')}}" rel="stylesheet">
        <!-- END VENDOR CSS-->
        <!-- BEGIN APEX CSS-->
        <link rel="stylesheet" type="text/css" href="{{url('/apex/css/app.css')}}">
        <!-- END APEX CSS-->
        <!-- BEGIN Page Level CSS-->
        <!-- END Page Level CSS-->
         @stack('stylesheets')
    </head>
    
	<body data-col="1-column" class=" 1-column  blank-page blank-page">
    <!-- ////////////////////////////////////////////////////////////////////////////-->
    <div class="wrapper">
      <div class="main-panel">
        <div class="main-content">
          <div class="content-wrapper"><!--Forgot Password Starts-->
				<section id="forgot-password">
					<div class="container-fluid gradient-indigo-blue">
						<div class="row full-height-vh">
							<div class="col-12 d-flex align-items-center justify-content-center">
								<div class="card bg-blue-grey bg-darken-3 px-4">
									<div class="card-header">
										<div class="card-image text-center">
											<!-- <i class="icon-users font-large-5 blue-grey lighten-4"></i> -->
											{{-- <img src="{{url('images/light-bg-text-logo.png')}}"> --}}
											<div class="white">Serine KPPN Tanjung Pandan</div>
										</div>
									</div>
									<div class="card-body">
										<div class="card-block">
											<!-- <div class="text-center">
												<h4 class="text-uppercase text-bold-400 white">BAS ONLINE</h4>
												<img src="{{url('images/light-bg-text-logo.png')}}">
											</div> -->
											@if (isset($message))
											<h2><div class="white">{{ $message }}</div></h2>
											@endif
											@if($errors->any())
											<h2><div class="white">{{$errors->first()}} </div></h2>
											@endif
											<form class="pt-4" action="{{url('/login')}}" method="post">
												{{ csrf_field() }}
												<div class="form-group">
													<input type="text" class="form-control" name="email" id="email" placeholder="Username" value="@if(isset($email)){{$email}}@endif">
												</div>
												<div class="form-group">
													<input type="password" class="form-control" name="password" id="password" placeholder="Password">
												</div>
												<!-- <div class="form-group">
													<select class="form-control" name="tahun" id="tahun">
														<option value="2019">2019</option>
														<option value="2017">2017</option>
														<option value="2018">2018</option>
														<option value="2019" selected>2019</option>
													</select>
												</div> -->
												<div class="form-group pt-2">
													<div class="text-center mt-3">
														<button type="submit" class="btn btn-info btn-raised btn-block">Login</button>
													</div>
												</div>
											</form>
										</div>
										<div class="card-footer bg-blue-grey bg-darken-3">
											<center><div class="white"><a>Aplikasi Serine KPPN</a></div></center>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</section>
<!--Forgot Password Ends-->
          </div>
        </div>
      </div>
    </div>

        <!-- BEGIN VENDOR JS-->

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="{{url('/apex/vendors/js/core/popper.min.js')}}"></script>
        <script src="{{url('/apex/vendors/js/core/bootstrap.min.js')}}"></script>
        <script src="{{url('/apex/vendors/js/perfect-scrollbar.jquery.min.js')}}"></script>
        <script src="{{url('/apex/vendors/js/prism.min.js')}}"></script>
        <script src="{{url('/apex/vendors/js/jquery.matchHeight-min.js')}}"></script>
        <script src="{{url('/apex/vendors/js/screenfull.min.js')}}"></script>
        <script src="{{url('/apex/vendors/js/pace/pace.min.js')}}"></script>
        <!-- BEGIN VENDOR JS-->
        <!-- BEGIN PAGE VENDOR JS-->

		<!-- Custom Theme Scripts -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/gentelella/1.3.0/js/custom.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.min.js"></script>

		<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.13/af-2.1.3/fh-3.1.2/datatables.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.0.0-beta10/jstree.min.js"></script>

		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>

		<script src="https://code.highcharts.com/highcharts.js"></script>
		<script src="https://code.highcharts.com/modules/data.js"></script>
		<script src="https://code.highcharts.com/modules/drilldown.js"></script>
		<script src="http://code.highcharts.com/modules/exporting.js"></script>

		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js"></script>

		<script src="https://cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script>

		<script src="{{url('/plugin/chosen/chosen.jquery.min.js')}}"></script>

		<script src="{{url('/plugin/pnotify/pnotify.custom.min.js')}}"></script>
		<script src="{{url('/js/handlebars.js')}}"></script>
		<script>
			$(document).ready(function () {
				$('.sidebar-menu').tree();
			})
		</script>

        <!-- END PAGE VENDOR JS-->
        <!-- BEGIN APEX JS-->
        <script src="{{url('/apex/js/app-sidebar.js')}}"></script>
        <script src="{{url('/apex/js/notification-sidebar.js')}}"></script>
        <script src="{{url('/apex/js/customizer.js')}}"></script>
        <script src="{{url('/apex/js/data-tables/datatable-advanced.js')}}" type="text/javascript"></script>

        <script src="{{url('/apex/vendors/js/datatable/datatables.min.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/dataTables.buttons.min.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/buttons.flash.min.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/jszip.min.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/pdfmake.min.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/vfs_fonts.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/buttons.html5.min.js')}}"></script>
		<script src="{{url('/apex/vendors/js/datatable/buttons.print.min.js')}}"></script>
        <!-- END APEX JS-->
        <!-- BEGIN PAGE LEVEL JS-->
        <!-- END PAGE LEVEL JS-->

    </body>
</html>