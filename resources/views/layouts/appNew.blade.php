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
        <!-- <link rel="shortcut icon" type="image/x-icon" href="{{url('/apex/img/ico/favicon.ico')}}">
        <link rel="shortcut icon" type="image/png" href="{{url('/apex/img/ico/favicon-32.png')}}"> -->
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
  <link rel="stylesheet" type="text/css" href="{{url('/plugin/jquery-file-upload/css/jquery.fileupload.css')}}">

  <link rel="stylesheet" type="text/css" href="{{url('/plugin/chosen/chosen.min.css')}}">

        <link href="https://cdnjs.cloudflare.com/ajax/libs/gijgo/1.9.13/combined/css/gijgo.min.css" rel="stylesheet">


  <link href="{{url('/css/custom.css')}}" rel="stylesheet">
        <!-- END VENDOR CSS-->
        <!-- BEGIN APEX CSS-->
        <link rel="stylesheet" type="text/css" href="{{url('/apex/css/app.css')}}">
        <!-- END APEX CSS-->
        <!-- BEGIN Page Level CSS-->
        <!-- END Page Level CSS-->
         @stack('stylesheets')
    </head>
    <body data-col="2-columns" class=" 2-columns " onload="homefunction()">
        <!-- ////////////////////////////////////////////////////////////////////////////-->
        <div class="wrapper nav-collapsed menu-collapsed">


            <!-- main menu-->
            <!--.main-menu(class="#{menuColor} #{menuOpenType}", class=(menuShadow == true ? 'menu-shadow' : ''))-->
            <div data-active-color="white" data-background-color="purple-bliss" data-image="{{url('/apex/img/sidebar-bg/01.jpg')}}" class="app-sidebar">
                <!-- main menu header-->
                <!-- Sidebar Header starts-->
                <div class="sidebar-header">
                    <div class="logo clearfix">
                        <a href="{{ url('/') }}" class="logo-text float-left">
                            <div class="logo-img">
                                SI
                                {{-- <img src="{{url('images/full-main-logo.png')}}" width="30px" height="auto" /> --}}
                            </div>
                            <span class="text align-middle">
                               
                                {{-- <img src="{{url('images/full-text-logo-cut.png')}}"> --}}
                            </span>
                        </a>
                        <a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block">
                            <i data-toggle="collapsed" class="ft-toggle-left toggle-icon"></i>
                        </a>
                        <a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none">
                            <i class="ft-x"></i>
                        </a>
                    </div>
                </div>
                <!-- Sidebar Header Ends-->
                <!-- / main menu header-->
                   @include('includes/sidebarApex')

                <div class="sidebar-background"></div>
                <!-- main menu footer-->
                <!-- include includes/menu-footer-->
                <!-- main menu footer-->
            </div>
            <!-- / main menu-->


            @include('includes/topbarApex')

            <div class="main-panel">
                <div class="main-content">
                    <div class="content-wrapper">
                    <section class="content">

                        @yield('content')

                    </section>

                    </div>
                </div>

                <footer class="footer footer-static footer-light">
                    <p class="clearfix text-muted text-sm-center px-2"><span>Copyright  &copy; 2018 , All rights reserved. </span></p>
                </footer>

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
<script src="https://cdnjs.cloudflare.com/ajax/libs/alertify.js/0.3.11/alertify.min.js"></script>

<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.13/af-2.1.3/fh-3.1.2/datatables.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.0.0-beta10/jstree.min.js"></script>





<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.3/FileSaver.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script src="http://code.highcharts.com/modules/exporting.js"></script>



<script src="https://cdn.ckeditor.com/4.11.1/standard/ckeditor.js"></script>

<script src="{{url('/plugin/chosen/chosen.jquery.min.js')}}"></script>

<script src="{{url('/plugin/pnotify/pnotify.custom.min.js')}}"></script>
<script src="{{url('/plugin/jquery-file-upload/js/vendor/jquery.ui.widget.js')}}"></script>
<script src="{{url('/plugin/jquery-file-upload/js/jquery.fileupload.js')}}"></script>
<script src="{{url('/js/handlebars.js')}}"></script>
<script>


    $(document).ready(function () {
        //PNotify.prototype.options.styling = "bootstrap3";
        function showNotify(menit){
                new PNotify({
                    title: 'Informasi Ujian',
                    text: 'Waktu Tersisa '+ menit +' Menit..!!' ,
                    type: 'info',
                    //hide: false,
                });
        }

        //    showNotify('22');



        $('.sidebar-menu').tree();





    })
	
	function homefunction(){
        ajaxLoad('{{url('dashboard/switcher')}}');
    }
		
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
    <script src='https://cdnjs.cloudflare.com/ajax/libs/gijgo/1.9.13/combined/js/gijgo.min.js'></script>
        <!-- END APEX JS-->
        <!-- BEGIN PAGE LEVEL JS-->
        <!-- END PAGE LEVEL JS-->

    </body>
</html>