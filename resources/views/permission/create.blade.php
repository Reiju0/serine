<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">Tambah Permission
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/permission')}}')" class="btn btn-xs btn-danger">close</a>
            </div>
        </h2>
    </div>
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" id="user" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="0">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <style type="text/css"> .white{color:black; } .green{color:green; } .red{color:red; } </style> 
            <div class="form-group" align="center">
                <label style="padding: 0 0 15px;" class="white">Permission Name: <span id="permission_name" class="red"></span></label>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Module</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="module" required id="module">
                        {!! app(\App\Http\Controllers\Permission\PermissionController::class)->getModule("") !!}
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Permission</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="permission" class="form-control" value="" required placeholder="permission" id="permission">
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
    var module = $("#module").val();
    var permission = $("#permission").val();
    //$("#permission_name").html(module+'.'+permission);
}

$(document).ready(function() {

    jQuery("#permission").keyup(checkPasswordMatch);
    jQuery("#module").change(checkPasswordMatch);

    jQuery("#submit").click(function(){
        if($("form")[0].checkValidity()) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url:'{{url('/permission/create')}}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{url('/permission')}}');
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
                ajaxLoad('{{url('/permission')}}');
            }else{
            }
        });

  }
</script>