<div class="card">
    <div class="card-header">
        <h2 align="center">Update Data Role
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/role')}}')" class="btn btn-xs btn-danger">close</a>
            </div>
        </h2>
    </div>
    <div class="card-body">
    
        <form class="form-horizontal" enctype="multipart/form-data" id="role" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$role->id}}">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Role</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="role" class="form-control" value="{{$role->name}}" readonly placeholder="role">
                </div>
            </div>
            <div class="form-group">
            @foreach($all as $item)
                @if(!(isset($sebelumnya))) 
                    <?php $sebelumnya = $item->module; ?>  
                @elseif((isset($sebelumnya)) and ($sebelumnya != $item->module)) 
            </div><hr>
            <div class="form-group">
                    <?php $sebelumnya = $item->module; ?>  
                @endif

                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                    <input type="hidden" name="permission[{{$no}}][name]" value="{{$item->permission}}">
                    <input type="checkbox" value="1" name="permission[{{$no++}}][value]" 
                        @if(isset($permission[$item->permission]) == 1) checked @endif
                    id="pid_{{$item->permission}}"> <label for="pid_{{$item->permission}}">{{$item->permission}}</label>
                </div>
            
            @endforeach
            </div><hr>
            
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Admin For Role:</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    @foreach($roles as $items)
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <input type="hidden" name="admin[{{$no2}}][name]" value="{{$items->id}}">
                            <input type="checkbox" value="1" name="admin[{{$no2++}}][value]" 
                                @if(in_array($items->id, $admin)) checked @endif
                            > {{$items->name}}
                        </div>
                    @endforeach
                </div>
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
$(document).ready(function() {

    jQuery("#submit").click(function(){
        $('.validations').prop( 'checked', true );
        if($("form")[0].checkValidity()) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url:'{{url('/role/permission')}}',
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