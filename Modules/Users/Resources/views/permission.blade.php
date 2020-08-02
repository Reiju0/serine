<div class="panel panel-info">
    <div class="panel-heading">
        <h2 align="center">Update Data User
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/users')}}')" class="btn btn-xs btn-danger">close</a>
            </div>
        </h2>
    </div>
    <div class="panel-body">
    
        <form class="form-horizontal" enctype="multipart/form-data" id="role" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$users->id}}">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Role</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="role" class="form-control" value="{{$users->username}}" readonly placeholder="role">
                </div>
            </div>
            
            <div class="form-group">
                @foreach($dokumen as $doc)
                <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                    <input type="hidden" name="permission[{{$no}}][name]" value="Dokumen_{{$doc->id}}">
                    <input type="checkbox" value="1" name="permission[{{$no++}}][value]" 
                        @if(isset($permission['Dokumen_'.$doc->id]) == 1) checked @endif
                    id="pid_Dokumen_{{$doc->id}}"> <label for="pid_Dokumen_{{$doc->id}}"> Dokumen {{$doc->uraian}}
                </div>
                @endforeach
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
                url:'{{url('/users/permission/'.$users->id)}}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{url('/users')}}');
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
                ajaxLoad('{{url('/users')}}');
            }else{
            }
        });

  }


</script>