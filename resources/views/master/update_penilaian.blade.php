<div class="card panel-info">
    <div class="card-header">
        <h2 align="center">{{$array->title}}
            <div class="btn-group pull-left">
            @if(isset($array->back))
                <a href="javascript:ajaxLoad('{{$array->back}}')" class="btn btn-xs btn-info"><i class="fa fa-angle-left"></i> Back</a>
            @endif
            </div>
        </h2>
    </div>
    @if(isset($array->pre_form))
    <div class="card-body">
        {!!$array->pre_form!!}
    </div>
    @endif
    <div class="card-body">
        <form class="form-horizontal" enctype="multipart/form-data" id="user" onsubmit="return false;">
            {{ csrf_field() }}
            <div class="alert alert-danger" role="alert" style="display: none" id="message_area">
            </div>
            @foreach ($array->input as $input)
            <div class="form-group">
            @if ($input->type == 'rowhtml')
                {!! $input->html !!}
            @else
            
                <label class="col-xs-12 col-sm-2 col-md-2 col-lg-2 control-label">@if(isset($input->title)){{$input->title}}@else{{$input->name}}@endif @if(isset($input->required)) * @endif</label>
                <div class="col-xs-12 col-sm-10 col-md-10 col-lg-10">
                    @if($input->type == 'template')
                        @if(isset($input->option))
                        {!! call_user_func_array('PublicRef::'.$input->name, $input->option)!!}
                        @else
                        {!! call_user_func('PublicRef::'.$input->name)!!}
                        @endif
                    @elseif ( in_array($input->type, array('text', 'email', 'password', 'number')))
                        <input type="{{$input->type}}" class="form-control @if(isset($input->class)){{$input->class}} @endif" name="{{$input->name}}" id="{{$input->name}}" placeholder="@if(isset($input->text)){{$input->text}} @else {{$input->name}} @endif" @if(isset($input->required)) required @endif  @if(isset($input->readonly)) readonly @endif @if(isset($input->value)) value="{{$input->value}}" @endif>
                    @elseif($input->type=='button')
                    <a href="{{$input->url}}" @if(isset($input->target)) target="$input->taget"@endif><button type="{{$input->typebutton}}" class="$input->classbutton">{{$input->namebutton}}</button></a>
                    @elseif($input->type=='p')
                    <p @if(isset($input->class)) class="$input->class"@endif>{{$input->name}}</p>
                    @elseif ($input->type == 'select')
                        <select name="{{$input->name}}" id="{{$input->name}}" data-placeholder="@if(isset($input->text)){{$input->text}}@endif" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12 @if(isset($input->class)){{$input->class}} @endif" @if(isset($input->required)) required @endif>
                            @foreach ($input->option as $option)
                            <option value="{{$option->val}}" @if(isset($input->value) and $input->value == $option->val) selected @endif>{{$option->text}}</option>
                            @endforeach
                        </select>
					@elseif ($input->type == 'select-multiple')
                        <select name="{{$input->name}}" id="{{$input->name}}" data-placeholder="@if(isset($input->text)){{$input->text}}@endif" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12 @if(isset($input->class)){{$input->class}} @endif" multiple @if(isset($input->required)) required @endif>
                            @foreach ($input->option as $option)
                            <option value="{{$option->val}}">{{$option->text}}</option>
                            @endforeach
                        </select>
                    @elseif ($input->type == 'radio')
                        @foreach ($input->option as $option)
                        <div><input type="radio" name="{{$input->name}}" value="{{$option->val}}" @if(isset($input->value) and $input->value == $option->val) checked="" @endif class="@if(isset($input->class)){{$input->class}} @endif"> {{$option->text}}</div>
                        @endforeach
                    @elseif ($input->type == 'file')
                        <input type="file" name="{{$input->name}}" @if(isset($input->required)) required @endif @if(isset($input->extention)) accept="{{$input->extention}}" @endif class="@if(isset($input->class)){{$input->class}} @endif">
                        <p class="help-block">{{$input->text}}</p>
                        @if (isset($input->value))
                        <p class="help-block"><a href="{{$input->value}}" target="_blank">Download File</a></p>
                        @endif
                    @elseif ($input->type == 'textarea')
                        <textarea name="{{$input->name}}" id="{{$input->name}}" class="form-control @if(isset($input->class)){{$input->class}} @endif" placeholder="@if(isset($input->text)){{$input->text}} @else {{$input->name}} @endif">@if(isset($input->value)) {{$input->value}} @endif</textarea>
                    @elseif ($input->type == 'html')
                        {!! $input->html !!}
                    @endif
                </div>
            
            @endif
            </div>
            @endforeach

            @if(isset($array->form))
                {!!$array->form!!}
            @endif

            </div>
            <div class="form-group" align="center">
                <button type="submit" class="btn btn-success" id="submit">KIRIM</button> <a onclick="confirmation()" class="btn btn-danger">BATAL</a>
            </div>
        </form>
            
        </div>
    </div>
</div>

<script>
@if(isset($array->javascript))
    {!!$array->javascript!!}
@endif

$(document).ready(function() {
    $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});
    $('.datepicker').datepicker({autoclose: true});

    @if(isset($array->jquery))
    {!! $array->jquery !!}
    @endif

    $('#group_select').hide();
	
	CKEDITOR.replaceAll( 'editor' );

    jQuery("#submit").click(function(){
        if($("form")[0].checkValidity()) {
            
			jQuery( "#penilaian" ).val(CKEDITOR.instances.penilaian.getData());
			
			var formData = new FormData($('form')[0]);
            $(".loading").show();
            $.ajax({
                url:'{{ $array->url }}',
                method:'POST',
                data:formData,
                contentType: false,
                processData: false,
                success:function(result){
                    if(result=='success'){
                        alertify.log('Data berhasil disimpan.');
                        ajaxLoad('{{ $array->back }}');
                    }
                    else{
                        alertify.log(result);
                        $('#message_area').html(result).show();
                    }
                    $(".loading").hide();
                },
                error: function (e, messages, detail){
                    alertify.log(detail);
                    $('.loading').hide();
                }
            });
        }else{
            //do nothing
            alertify.log('Lengkapi Data * terlebih dahulu');
        }

  });
});

  function confirmation(){
        alertify.confirm("Anda yakin akan membatalkan?", function (e) {
            if (e) {
                ajaxLoad('{{ $array->back }}');
            }else{
            }
        });

  }
</script>