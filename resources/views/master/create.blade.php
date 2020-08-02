
<div class="card">
    <div class="card-header">
        <h4 class="card-title" id="horz-layout-basic" align="center"> {{$array->title}}
            <div class="btn-group pull-left">
            @if(isset($array->back))
            <a href="javascript:ajaxLoad('{{$array->back}}')" class="btn btn-xs btn-info"><i class="fa fa-angle-left"></i> Back</a>
            @endif
            </div>
        </h4>
    </div>
    @if(isset($array->pre_form))
    <div class="card-body">
        {!!$array->pre_form!!}
    </div>
    @endif
    <div class="card-body">
        <div id="message_area" class="message_area">
        </div>
    </div>
    <div class="card-body">
        <div class="px-3">
            <form class="form form-horizontal" enctype="multipart/form-data" id="user" onsubmit="return false;" data-toggle="validator">
                {{ csrf_field() }}
                <input type="hidden" name="id" value="0">
                <div class="form-body">
                @foreach ($array->input as $input)
                    @if ($input->type == 'rowhtml')
                    <div class="form-group">
                    {!! $input->html !!}
                    @else
                    <div class="form-group row @if(isset($input->div_class)){{$input->div_class}}@endif">
                        <label class="col-md-2 label-control">@if(isset($input->title)){{$input->title}}@else{{$input->name}}@endif @if(isset($input->required)) * @endif</label>
                        <div class="col-md-10">
                        @if($input->type == 'template')
                            @if(isset($input->option))
                                {!! call_user_func_array('PublicRef::'.$input->name, $input->option)!!}
                            @else
                                {!! call_user_func('PublicRef::'.$input->name)!!}
                            @endif
                        @elseif ( in_array($input->type, array('text', 'email', 'password', 'number')))
                            <input type="{{$input->type}}"  class="form-control"  id="{{$input->name}}" name="{{$input->name}}">
                        @elseif ( in_array($input->type, array('date')))
                            <input type="date" id="{{$input->name}}" class="form-control" name="{{$input->name}}">
                        
                        @elseif ($input->type == 'select')
                            <select name="{{$input->name}}" id="{{$input->name}}" 
                                placeholder="@if(isset($input->text)){{$input->text}}@endif" 
                                class="chosen-select form-control @if(isset($input->class)){{$input->class}} @endif" 
                                @if(isset($input->required)) required @endif
                            >

                            @foreach ($input->option as $option)
                            <option value="{{$option->val}}"
                            @if (isset($input->value))
                                @if($input->value == $option->val)
                                    selected
                                @endif
                            @endif>{{$option->text}}</option>
                            @endforeach
                            </select>
                        @elseif ($input->type == 'select-multiple')
                            <select name="{{$input->name}}" id="{{$input->name}}" 
                                placeholder="@if(isset($input->text)){{$input->text}}@endif" 
                                class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12 @if(isset($input->class)){{$input->class}} @endif" multiple @if(isset($input->required)) required @endif>
                            @foreach ($input->option as $option)
                            <option value="{{$option->val}}">{{$option->text}}</option>
                            @endforeach
                            </select>
                        @elseif ($input->type == 'radio')
                            @foreach ($input->option as $option)
                            <div><input type="radio" name="{{$input->name}}" value="{{$option->val}}" class="@if(isset($input->class)){{$input->class}} @endif"> {{$option->text}}</div>
                            @endforeach
                        @elseif ($input->type == 'radio-inline')
                            @foreach ($input->option as $option)
                            <label class="radio-inline">
                            <input type="radio" name="{{$input->name}}" value="{{$option->val}}" @if($option->checked==1) checked='checked' @endif> {{$option->text}}
                            </label>
                            @endforeach
                        @elseif ($input->type == 'file')
                            <input type="file" name="{{$input->name}}" @if(isset($input->required)) required @endif @if(isset($input->extention)) accept="{{$input->extention}}" @endif class="@if(isset($input->class)){{$input->class}} @endif">
                            <p class="help-block">{{$input->text}}</p>
                        @elseif ($input->type == 'textarea')
                            <textarea name="{{$input->name}}" id="{{$input->name}}" class="form-control @if(isset($input->class)){{$input->class}} @endif" placeholder="@if(isset($input->text)){{$input->text}} @else {{$input->name}} @endif"></textarea>
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

                <div class="form-actions" align="center"><a onclick="confirmation()">
                    <button type="button" class="btn btn-raised btn-warning mr-1">
                        <i class="ft-x"></i> Batal
                    </button>
                    </a>
                    <button type="button"  id="submit" class="btn btn-raised btn-primary">
                        <i class="fa fa-check-square-o"></i> Save
                    </button>
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
    $('.datepicker').datepicker({
            autoclose: true,
            format: "dd/mm/yyyy"});

    @if(isset($array->jquery))
    {!! $array->jquery !!}
    @endif

    $('#group_select').hide();
    jQuery("#submit").click(function(){
    @if(isset($array->jquery_click))
    {!! $array->jquery_click !!}
    @endif
    if($("form")[0].checkValidity()) {
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