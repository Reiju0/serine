<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">Option
        </h2>
    </div>
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" onsubmit="return false;">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="0">
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Maintenance</label>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                    <select class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12" name="maintenance" id="maintenance">
                        <option value="1" @if ($maintenance->val == 1 ) selected @endif>false</option>
                        <option value="0" @if ($maintenance->val == 0 ) selected @endif>true</option>
                    </select>
                </div>
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a class="btn btn-primary" onclick="update_data('maintenance')"><i class="fa fa-check"></i> Change</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Version</label>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                    <input type="text" name="version" class="form-control" value="{{$version->val}}" placeholder="version">
                </div>
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a class="btn btn-primary" onclick="update_data('version')"><i class="fa fa-check"></i> Change</a>
                </div>
            </div>
            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Message</label>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                    <input type="text" name="message" class="form-control" value="{{$message->val}}" placeholder="message">
                    
                        {{-- <textarea name="messages" id="messages" class="editor" rows="10" cols="80">
                            {{$message->val}}
                        </textarea>
                        <input type="hidden" name="real_message"> --}}
                      
                      
                </div>

                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a class="btn btn-primary" onclick="update_data('message')"><i class="fa fa-check"></i> Change</a>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Token</label>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                    <input type="text" name="token" class="form-control" value="{{$token->val}}" placeholder="token">
                </div>
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a class="btn btn-primary" onclick="update_data('token')"><i class="fa fa-check"></i> Change</a>
                </div>
            </div>

            <div class="form-group">
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">Seeder URL</label>
                <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
                    <input type="text" name="seeder" class="form-control" @if(!is_null($seeder)) value="{{$seeder->val}}" @endif placeholder="seeder url">
                </div>
                <div class="col-xs-12 col-sm-2 col-md-2 col-lg-2">
                    <a class="btn btn-primary" onclick="update_data('seeder')"><i class="fa fa-check"></i> Change</a>
                </div>
            </div>
            

            
        </form>
            
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/11.2.0/classic/ckeditor.js"></script>
<script>

$(document).ready(function() {
    $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});
    
    ClassicEditor
        .create( document.querySelector( '#message' ),{
            alignment: {
                options: [ 'left', 'right', 'center', 'justify' ]
            },
            toolbar: [ 'heading', '|', 'alignment:left', 'alignment:right', 'alignment:center', 'alignment:justify', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable' ],
            table: {
                contentToolbar: [ 'tableColumn', 'tableRow', 'mergeTableCells' ]
            }
        } )
        .then( newEditor => {
            editor = newEditor;
        } )
        .catch( error => {
            console.error( error );
        } );


    

});

  function update_data(data){
        alertify.confirm("Anda yakin akan mengganti ini?", function (e) {
            if (e) {
                    // if(data == 'message'){
                    //     $( "#message" ).val(editor.getData());
                    // }
                    
                    var formData = new FormData($('form')[0]);
                    
                    $.ajax({
                        url:'{{url('/option/update')}}/'+data,
                        method:'POST',
                        data:formData,
                        contentType: false,
                        processData: false,
                        success:function(result){
                            if(result=='success'){
                                alertify.log('Data berhasil disimpan.');
                                ajaxLoad('{{url('/option')}}');
                            }
                            else{
                                alertify.log(result);
                                $('#message_area').html(result).show();
                            }
                        }
                    });
                
            }else{
            }
        });

  }
</script>