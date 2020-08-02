<div class="card">
    <div class="card-header">
        <h2 align="center">Tambah Role
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/role')}}')" class="btn btn-xs btn-danger">close</a>
            </div>
        </h2>
    </div>
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" id="user" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="0">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Role</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="role" class="form-control" value="" required placeholder="role name" id="role">
                </div>
            </div>
            <style type="text/css"> .white{color:black; } .green{color:green; } .red{color:red; } </style> 
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Slug</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="role_slug" class="form-control" value="" readonly placeholder="Slug" id="role_slug">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">group</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="groups">
                        {!! app(\App\Http\Controllers\Role\RoleController::class)->getUserGroup(0) !!}
                    </select>
                </div>
            </div>
            <div class="form-group" align="center">
                <button type="submit" class="btn btn-success" id="submit">KIRIM</button> <a onclick="confirmation()" class="btn btn-danger">BATAL</a>
            </div>
        </form>
            
        </div>
    </div>
</div>

<script>


function checkPasswordMatch() {
    var role = $("#role").val();
    $("#role_slug").val(role.replace(/\s+/g, ''));
}

$(document).ready(function() {

    jQuery("#role").keyup(checkPasswordMatch);

    jQuery("#submit").click(function(){
        if($("form")[0].checkValidity()) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url:'{{url('/role/create')}}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{url('/role')}}');
                    }
                    else{
                        alertify.log(result);
                        $('#message_area').html(result).show();
                    }
                }
            });
        }else{
            //do nothing
        }

  });
});

  function confirmation(){
        alertify.confirm("Anda yakin akan membatalkan?", function (e) {
            if (e) {
                ajaxLoad('{{url('/role')}}');
            }else{
            }
        });

  }
</script>