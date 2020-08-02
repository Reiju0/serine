@extends('layouts.appNew')

@section('content')
<div class="right_col" role="main">
<style>
    .loading {
        background: lightgoldenrodyellow url('{{asset('images/processing.gif')}}') no-repeat center 65%;
        height: 80px;
        width: 100px;
        position: fixed;
        border-radius: 4px;
        left: 50%;
        top: 50%;
        margin: -40px 0 0 -50px;
        z-index: 2000;
        display: none;
    }
</style>
<style>
    .loading {
        background: lightgoldenrodyellow url('{{asset('images/processing.gif')}}') no-repeat center 65%;
        height: 80px;
        width: 100px;
        position: fixed;
        border-radius: 4px;
        left: 50%;
        top: 50%;
        margin: -40px 0 0 -50px;
        z-index: 2000;
        display: none;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="content" id="content">
            <div id="ajaxdata">

            </div>
        </div>
    </div>
    <div class="loading"></div>
</div>
    <script>
		
        function ajaxLoad(filename, content) {
            content = typeof content !== 'undefined' ? content : 'ajaxdata';
            $('.loading').show();
            $.ajax({
                type: "GET",
                url: filename,
                contentType: false,
                success: function (data) {
                    $("#" + content).html(data);
                    $('.loading').hide();
                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText+' '+xhr.status);
                    //alert(xhr.responseJSON.Message);
                },
                statusCode:{
                    401: function(){
                        window.location = '{{url('/logout')}}';
                    }
                }
            });
        }

        function notifLoad(url, id){
            if ($("#"+id).hasClass("text-light-blue")){
                var notif_all = window.localStorage.getItem("notif_all_count") - 1;
                window.localStorage.setItem("notif_all_count", notif_all);
                $("#notification_number").html(notif_all);
                $("#"+id).removeClass("text-light-blue");
            }
            ajaxLoad(url);
        }

		function logout(){
			alertify.confirm('Anda yakin ingin keluar?', function (e) {
				if (e) {
		//{{ url('/logout') }}
					window.location.href = "{{ url('/logout') }}";
				}
			});
		}
    </script>
@endsection
