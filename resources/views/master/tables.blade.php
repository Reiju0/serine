
    
    @if(isset($array->pre_form))
    <div class="card-body">
        {!!$array->pre_form!!}
    </div>
    @endif
   
	<div class="card-body">
		<form method="POST" id="post_{{$array->title}}" class="form-horizontal" role="form">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="x_panel">
                    <div class="advance_search">
                        {!!$advance_filter!!}
                    </div>
                    
                    @if(isset($array->search))
                    
                    @foreach ($array->search as $search)
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        @if($search->type == 'template')
                            @if(isset($search->option))
                            {!! call_user_func_array('PublicRef::'.$search->name, $search->option)!!}
                            @else
                            {!! call_user_func('PublicRef::'.$search->name)!!}
                            @endif
                        @elseif ( in_array($search->type, array('text', 'email', 'password', 'number')))
                        <input type="text" class="form-control " name="{{$search->name}}" id="{{$search->name}}" placeholder="@if(isset($search->text)) {{$search->text}} @else {{$search->name}} @endif">
                        @elseif ($search->type == 'select')
                        <select name="{{$search->name}}" id="{{$search->name}}" data-placeholder="@if(isset($search->text)) {{$search->text}} @else {{$search->name}} @endif" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            @foreach ($search->option as $option)
                            <option value="{{$option->val}}">{{$option->text}}</option>
                            @endforeach
                        </select>
                        @endif
                    </div>
                    @endforeach
                    
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" align="center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> </button> 
                    </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
                <thead>
                    <tr>
                        <th>No</th>
                        <?php $center = "0"; $right = "0"; $no = 1;?>
                        @foreach ($array->data as $data)
                        <th>{{$data->title}}</th>
                            <?php 
                                if(isset($data->align)){
                                    if($data->align == 'center'){
                                        $center .= ", ".$no;
                                    }
                                    elseif($data->align == 'right'){
                                        $right .= ", ".$no;
                                    }
                                }
                                $no++;
                            ?>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>


@if(isset($array->form))
    {!!$array->form!!}
@endif

<script>
@if(isset($array->javascript))
    {!!$array->javascript!!}
@endif

 var {{$array->title}};
 
$(document).ready(function() {
$(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});

    @if(isset($array->jquery))
    {!! $array->jquery !!}
    @endif
    
    {{$array->title}} = $('#table_{{$array->title}}').DataTable({
		language: {
			"thousands": "."
		  },
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url : '{{ $array->url }}',
            data: function (d){
                @if(isset($array->search))
                @foreach ($array->search as $search)
                    @if ($search->type == 'text')
                        d.{{$search->name}} = $('#post_{{$array->title}} input[name={{$search->name}}]').val();
                    @elseif ($search->type == 'select')
                        d.{{$search->name}} = $('#post_{{$array->title}} select[name={{$search->name}}]').val();
                    @elseif ($search->type == 'template')
                        @if(isset($search->delta))
                        d.{{$search->delta}} = $('#post_{{$array->title}} {{$search->param}}[name={{$search->delta}}]').val();
                        @else
                        d.{{$search->name}} = $('#post_{{$array->title}} {{$search->param}}[name={{$search->name}}]').val();
                        @endif
                    @endif 
                @endforeach
                @endif
            }
        },
        "columnDefs": [
            {"className": "dt-center", "targets": [{{$center}}]},
            {"className": "dt-right", "targets": [{{$right}}]}
        ],
       columns: [
            { data: 'nomor', name: 'nomor', sortable: false, orderable: false},
            @foreach ($array->data as $data)
                { data: '{{$data->name}}', name: '{{$data->name}}'
                    @if(isset($data->format) and $data->format == 'number')
                    , render: $.fn.dataTable.render.number('.', ',', 0, '')
                    @endif
                },
            @endforeach
            
        ],
        @if(isset($array->order))
            order: [['{{$array->order->kolom}}', @if(isset($array->order->param)) '{{$array->order->param}}' @else 'asc' @endif]],
        @else
            order: [['1', 'asc']],
        @endif
        lengthMenu: [[@if(isset($array->length)){{$array->length}},@endif 25, 50, 100, 500, 1000, -1], [@if(isset($array->length)){{$array->length}},@endif 25, 50, 100, 500, 1000, "All"]],
		
		dom: 'BlfrptBlip',
        buttons: [ {
            extend: 'excelHtml5',
                customize: function ( xlsx ){
                    var sheet = xlsx.xl.worksheets['sheet1.xml'];
                    // jQuery selector to add a border
                    $('row c[r*="10"]', sheet).attr( 's', '25' );
                },
                title : "{{$array->file_name}} "
                        @if(isset($array->search))
                        @foreach ($array->search as $search) 
                            @if($search->type == 'select') 
                                +$("#{{$search->name}}").val()+ " "
                            @elseif ($search->type == 'template' and $search->param == 'select')
                                +$("#{{$search->name}}").val()+ " "
                            @endif
                        @endforeach
                        @endif,
                text : "Excel"
            } ],
		
    });
	

    $('#post_{{$array->title}}').on('submit', function(e) {
        {{$array->title}}.draw();
        e.preventDefault();
    });

    
});
</script>
