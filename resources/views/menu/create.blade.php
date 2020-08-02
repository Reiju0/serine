<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">Tambah Menu
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/menu')}}')" class="btn btn-xs btn-danger">close</a>
            </div>
        </h2>
    </div>
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="0">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Menu</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="menu" class="form-control" value="" required placeholder="menu">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">have link?</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="link_yes">Ya</label>
                        <input type="radio" name="have_link" id="link_yes" value="1" required>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="link_no">Tidak</label>
                        <input type="radio" name="have_link" id="link_no" value="0">
                    </div>
                </div>
            </div>

            <div class="form-group" class="link_hide">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Link</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="path" class="form-control" value="" required placeholder="Link / path">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">is parent?</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="parent_yes">Ya</label>
                        <input type="radio" name="is_parent" id="parent_yes" value="1" required>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="parent_no">Tidak</label>
                        <input type="radio" name="is_parent" id="parent_no" value="0">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Parent</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="parent_id" id="parent_id">
                        {!! app(\App\Http\Controllers\Menu\MenuController::class)->getParent(0) !!}
                        
                    </select>
                </div>
            </div>
            
            <div class="form-group" id="group_select">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Status</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="status" id="status">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Class</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="style" class="form-control" value="" placeholder="class, ex: fa fa-home">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Order</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="delta" class="form-control" value="" placeholder="number of order">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Permission</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
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
                            > {{$item->permission}}
                        </div>
                    @endforeach
                    </div>
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


$(document).ready(function() {
/*
    $('input[type=radio][name=have_link]').change(function() {
        if (this.value == '1') {
            $('.link').show();
        }
        else{
            $('.link').hide();
        }
    });
  */  

    jQuery("#role").on('change', function(e){
        var role = $('#role').val();
        $.ajax({
            url:'{{url('/menu/group')}}/'+role+'/0',
            method: 'GET',
            success:function(result){
                if(result=="none"){
                    $('#group').html("<option value='0'></option>");
                    $('#group_select').hide();
                }else{
                    $('#group').html(result);
                    $('#group_select').show();
                }
            }
        });
    });

    jQuery("#submit").click(function(){
        if($("form")[0].checkValidity()) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url:'{{url('/menu/create')}}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{url('/menu')}}');
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
                ajaxLoad('{{url('/menu')}}');
            }else{
            }
        });

  }
</script>