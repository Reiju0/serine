<div class="card">
    <div class="card-header">
        <h2 align="center">Update Data User
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/role')}}')" class="btn btn-xs btn-danger">close</a>
            </div>
        </h2>
    </div>
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" id="user" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$item->id}}">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Role</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="role" class="form-control" value="{{$item->name}}" readonly quired placeholder="role name" id="role">
                </div>
            </div>
            <style type="text/css"> .white{color:black; } .green{color:green; } .red{color:red; } </style> 
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Slug</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="role_slug" class="form-control" value="{{$item->slug}}" readonly placeholder="Slug" id="role_slug">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">group</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="groups">
                        {!! app(\App\Http\Controllers\Role\RoleController::class)->getUserGroup($item->group_id) !!}
                    </select>
                </div>
            </div>
            <div class="alert alert-danger" role="alert" id="message_area" style="text-align: center">Updating this role may cause havoc to users that already have this role.
            </div>
            <div class="form-group" align="center">
                <button type="submit" class="btn btn-success" id="submit">KIRIM</button>
                <a onclick="confirmation()" class="btn btn-danger">BATAL</a>
            </div>

        </form>
            
        </div>

    </div>
</div>


<script>
function checkPasswordMatch() {
    var module = $("#module").val();
    var permission = $("#permission").val();
    $("#permission_name").html(module+'.'+permission);
}
$(document).ready(function() {
    jQuery("#permission").keyup(checkPasswordMatch);
    jQuery("#module").change(checkPasswordMatch);

    jQuery("#submit").click(function(){
        if($("form")[0].checkValidity()) {
            alertify.confirm("Anda yakin akan Melakukan Update?", function (e) {
                if (e) {
                    var formData = new FormData($('form')[0]);
                    $.ajax({
                        url:'{{url('/role/update')}}',
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