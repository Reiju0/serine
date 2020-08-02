
<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">Profile</h2>
    </div>
    <div class="box box-primary">
  <div class="box-body box-profile">
    <img class="profile-user-img img-responsive img-circle" src="{{ url($foto) }}" alt="User profile picture">

  </div>
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
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Email</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="email" name="email" class="form-control" value="{{$user->email}}" required placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Nama</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="nama" class="form-control" value="{{$user->nama}}" placeholder="Nama">
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Nama Unit</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="nama_unit" class="form-control" value="{{$user->nama_unit}}" placeholder="Nama Unit">
                </div>
            </div>
           
            <div class="form-group nip_default">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">NIP</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="nip" class="form-control" value="{{$user->nip}}" placeholder="NIP">
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Alamat</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <textarea name="alamat" class="form-control" placeholder="Alamat">{{$user->alamat}}</textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Telepon</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    <input type="text" name="telepon" class="form-control" value="{{$user->telp}}" placeholder="Telepon">
                </div>
            </div>
            <div class="form-group" align="center">
                <a href="javascript:ajaxLoad('{{url('/password')}}')"><button type="button" class="btn btn-warning">Change Password</button></a>
                <button type="submit" class="btn btn-primary" id="update">Update</button>
            </div>
        </form>
            
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.datepicker').datepicker();

    jQuery("#update").click(function(){
        if($("form")[0].checkValidity()) {
            var formData = new FormData($('form')[0]);
            $.ajax({
                url:'{{url('/profile')}}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{url('/profile')}}');
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

</script>