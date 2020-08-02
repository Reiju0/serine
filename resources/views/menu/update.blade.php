<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">Update Data Menu
            <div class="btn-group pull-right">
                <a href="javascript:ajaxLoad('{{url('/menu')}}')" class="btn btn-xs btn-danger">close</a>
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
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Menu</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="menu" class="form-control" value="{{$item->menu}}" required placeholder="menu">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">have link?</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="link_yes">Ya</label>
                        <input type="radio" name="have_link" id="link_yes" value="1" required @if ($item->have_link == "1") checked @endif>
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="link_no">Tidak</label>
                        <input type="radio" name="have_link" id="link_no" value="0" @if ($item->have_link == "0") checked @endif>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Link</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="path" class="form-control" value="{{$item->path}}" required placeholder="Link / path">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">is parent?</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="parent_yes">Ya</label>
                        <input type="radio" name="is_parent" id="parent_yes" value="1" required @if ($item->is_parent == "1") checked @endif >
                    </div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                        <label for="parent_no">Tidak</label>
                        <input type="radio" name="is_parent" id="parent_no" value="0" @if ($item->is_parent == "0") checked @endif >
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Parent</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="parent_id" id="parent_id">
                        {!! app(\App\Http\Controllers\Menu\MenuController::class)->getParent($item->parent_id) !!}
                    </select>
                </div>
            </div>
            
            <div class="form-group" id="group_select">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Status</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <select class="form-control" name="status" id="status">
                        <option value="1" @if ($item->status == "1") selected @endif >Aktif</option>
                        <option value="0" @if ($item->status == "0") selected @endif>Tidak Aktif</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Class</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="style" class="form-control" value="{{$item->style}}" placeholder="class, ex: fa fa-home">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Order</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="delta" class="form-control" value="{{$item->delta}}" placeholder="number of order">
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Permission</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <div class="form-group">
                    @foreach($all as $key)
                        @if(!(isset($sebelumnya))) 
                            <?php $sebelumnya = $key->module; ?>    
                        @elseif((isset($sebelumnya)) and ($sebelumnya != $key->module)) 
                    </div><hr>
                    <div class="form-group">
                            <?php $sebelumnya = $key->module; ?>
                        @endif
                        <div class="col-xs-6 col-sm-4 col-md-4 col-lg-4">
                            <input type="hidden" name="permission[{{$no}}][name]" value="{{$key->permission}}">
                            <input type="checkbox" value="1" name="permission[{{$no++}}][value]" 
                                @if(in_array($key->permission, $permission)) checked @endif
                            > {{$key->permission}}
                        </div>
                    @endforeach
                    </div>
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
        if($("form")[0].checkValidity()) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url:'{{url('/menu/update')}}',
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