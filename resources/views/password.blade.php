<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">Change Password</h2>
    </div>
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" id="form" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="0">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Username</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="username" class="form-control" value="{{$user->username}}" placeholder="Username" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Old Password</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="password" name="old_password" class="form-control" value="" required placeholder="Old Password">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">New Password</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="password" name="new_password" class="form-control" value="" required placeholder="New Password" id="new_password">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Re-new Password</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="password" name="re_new_password" class="form-control" value="" required placeholder="Re-new Password" id="re_new_password">
                </div>
            </div>
            <style type="text/css"> .white{color:black; } .green{color:green; } .red{color:red; } </style> 
            <div class="form-group" align="center">
            <label id="divCheckPasswordMatch"  style="padding: 0 0 15px;" class="white">password matching ...</label>
            </div>
            <div class="form-group" align="center">
                <button type="submit" class="btn btn-success" id="update" disabled>Update</button>
            </div>
        </form>
            
        </div>
    </div>
</div>

<script>
function checkPasswordMatch() {
    var password = $("#new_password").val();
    var confirmPassword = $("#re_new_password").val();

    if (password != confirmPassword){
        $("#divCheckPasswordMatch").html("New Passwords do not match!").removeClass('white').removeClass('green').addClass('red');
        $('#update').attr('disabled', 'disabled').addClass('btn-default').removeClass('btn-primary');
    }else{
        $("#divCheckPasswordMatch").html("New Passwords match.").removeClass('white').removeClass('red').addClass('green');
        $('#update').removeAttr("disabled").addClass('btn-primary').removeClass('btn-default');
    }

}


$(document).ready(function() {
    jQuery("#re_new_password").keyup(checkPasswordMatch);

    jQuery("#update").click(function(){

        alertify.confirm('Anda yakin akan mengganti password?', function (e) {
            if (e) {

                if($("form")[0].checkValidity()) {
                    var formData = new FormData($('form')[0]);
                    $.ajax({
                        url:'{{url('/password')}}',
                        method:'POST',
                        data:formData,
                        contentType: false,
                        processData: false,
                        success:function(result){
                            if(result=='success'){
                                alertify.log('Data berhasil disimpan.');
                                ajaxLoad('{{url('/password')}}');
                            }
                            else{
                                alertify.log(result);
                                $('#message_area').html(result).show();
                            }
                        }
                    });
                }
            }
        });

  });
});

</script>