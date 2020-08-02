<div class="panel panel-info">
    <div class="panel-heading">
        <h2 align="center">Update Indikator {{$satker->kdsatker}}. {{$satker->nmsatker}}
            <div class="btn-group pull-left">
                            <a href="javascript:ajaxLoad('{{url('/ref/satkerblu')}}')" class="btn btn-xs btn-info"><i class="fa fa-angle-left"></i> Back</a>
                        </div>
        </h2>
    </div>
    <div class="panel-body">
    
        <form class="form-horizontal" enctype="multipart/form-data" id="role" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$satker->kdsatker}}">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            
            <div class="form-group">
            @foreach($indikator as $item)
                @if(!(isset($sebelumnya))) 
                    <?php $sebelumnya = $item->kdrumpun; ?>  
                @elseif((isset($sebelumnya)) and ($sebelumnya != $item->kdrumpun)) 
            </div><hr>
            <div class="form-group">
                    <?php $sebelumnya = $item->kdrumpun; ?>  
                @endif
                <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <input type="hidden" name="permission[{{$no}}][name]" value="{{$item->kdindikator}}">
                    <input type="checkbox" value="1" name="permission[{{$no++}}][value]" 
                        @if(!empty($kdindikator))
                            @if(in_array($item->kdindikator, $kdindikator)) checked @endif 
                        @endif
                    > {{$item->kdindikator}}. {{$item->ur_indikator}}
                </div>
            
            @endforeach
                

            </div><hr>
            
            
            
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
                url:'{{url('/ref/satkerblu/indikator/save/'.$satker->kdsatker)}}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{url('/ref/satkerblu')}}');
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
                ajaxLoad('{{url('/ref/satkerblu')}}');
            }else{
            }
        });

  }


</script>