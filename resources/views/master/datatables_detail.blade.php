<section id="ajax">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title" align="center">{{$array->title}}
                        <div class="btn-group pull-left">
                        @if(isset($array->back))
                            <a href="javascript:ajaxLoad('{{$array->back}}')" class="btn btn-xs btn-info"><i class="fa fa-angle-left"></i> Back</a>
                        @endif
                        </div>
                        <div class="btn-group pull-right">
                            @if(isset($array->advance_filter))
                            <a href="#" class="btn btn-xs btn-primary filter_button"><i class="fa fa-search"></i> Advance Filter</a>
                            @endif

                            @if(isset($array->create))
                            <a href="javascript:ajaxLoad('{{$array->create}}')" class="btn btn-xs btn-success"><i class="fa fa-plus"></i> Tambah</a>
                            @endif
                            @if(isset($array->header_button))
                                {!!$array->header_button!!}
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
        <div class="table-responsive">
			
			<form method="POST" id="search-form" class="form-horizontal" role="form">
				<div class="col-md-12 col-sm-12 col-xs-12">
					<div class="x_panel">
						<div class="advance_search">
						{!!$advance_filter!!}
						</div>
						<div class="row">
						@if(isset($array->search))


						@foreach ($array->search as $search)


						@if(isset($search->col))
						<div class="@if(isset($search->col)){{$search->col}}@else col-2 @endif">
						@else
						<div class="
						@if(isset($search->width))
						col-{{$search->width}}
						@else
						col-2
						@endif">
						@endif
						@if($search->type == 'template')
						@if(isset($search->option))
						{!! call_user_func_array('PublicRef::'.$search->name, $search->option)!!}
						@else
						{!! call_user_func('PublicRef::'.$search->name)!!}
						@endif
						@elseif ( in_array($search->type, array('text', 'email', 'password', 'number')))
						<input type="text" class="form-control" name="{{$search->name}}" id="{{$search->name}}" placeholder="@if(isset($search->text)) {{$search->text}} @else {{$search->name}} @endif">
						@elseif ($search->type == 'select')
						<select name="{{$search->name}}" id="{{$search->name}}" data-placeholder="@if(isset($search->text)) {{$search->text}} @else {{$search->name}} @endif" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12">
						@foreach ($search->option as $option)
						<option value="{{$option->val}}">{{$option->text}}</option>
						@endforeach
						</select>
						@endif
						</div>
						@endforeach
						
						<div class="col-1" align="center">
							<button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> </button>
						</div>
						 </div>
						@endif
					</div>
				</div>
			</form>
			
            <table class="table table-condensed" id="table" style="width: 99%">
                <thead>
                    <tr>
                        <th></th>
                        <th>No</th>
                        <?php $center = "0"; $right = "0"; $no = 1; $key_detail = '';?>
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
            <script id="details-template" type="text/x-handlebars-template">
                <div class="label label-info">Detail</div>
                <table class="table details-table" @if(isset($array->detail_key))
    id="detail-<?php echo "{{".$array->detail_key."}}"; ?>"
    @else
    id="detail-<?php echo "{{".$array->data[0]->name."}}"; ?>"
    @endif >
                    <thead>
                        <tr>
                            <?php $dcenter = "0"; $dright = "0"; $no = 1;?>
                            @foreach ($array->detail as $data)
                            <th>{{$data->title}}</th>
                                <?php 
                                    if(isset($data->align)){
                                        if($data->align == 'center'){
                                            $dcenter .= ", ".$no;
                                        }
                                        elseif($data->align == 'right'){
                                            $dright .= ", ".$no;
                                        }
                                    }
                                    $no++;
                                ?>
                            @endforeach
                        </tr>
                    </thead>
                </table>
            </script>
        </div>
    </div>
</div>

@if(isset($array->form))
    {!!$array->form!!}
@endif
<div id="main_detail" style="display: none">
</div>

<script>
@if(isset($array->javascript))
    {!!$array->javascript!!}
@endif
$(document).ready(function() {
$(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});

    @if(isset($array->jquery))
    {!! $array->jquery !!}
    @endif
    
    var template = Handlebars.compile($("#details-template").html());
    var oTable = $('#table').DataTable({
		language: {
			"thousands": "."
		  },
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url : '{{ $array->url }}',
            data: function (d){
                @if(isset($array->advance_filter))
                d.kdba = $('select[name=kdba]').val();
                d.kdbaes1 = $('select[name=kdbaes1]').val();
                d.kduappaw = $('select[name=kduappaw]').val();
                d.kdkanwildjpbn = $('select[name=kdkanwildjpbn]').val();
                d.kdkppn = $('select[name=kdkppn]').val();
                d.kdkanwildjkn = $('select[name=kdkanwildjkn]').val();
                d.kdkpknl = $('select[name=kdkpknl]').val();
                @endif

                @if(isset($array->search))
                @foreach ($array->search as $search)
                    @if ($search->type == 'text')
                        d.{{$search->name}} = $('input[name={{$search->name}}]').val();
                    @elseif ($search->type == 'select')
                        d.{{$search->name}} = $('select[name={{$search->name}}]').val();
                    @elseif ($search->type == 'template')
                        @if(isset($search->delta))
                        d.{{$search->delta}} = $('{{$search->param}}[name={{$search->delta}}]').val();
                        @else
                        d.{{$search->name}} = $('{{$search->param}}[name={{$search->name}}]').val();
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
            {
                "className":      'details-control',
                "orderable":      false,
                "searchable":      false,
                "data":           null,
                "defaultContent": ''
            },
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
            order: [['2', 'asc']],
        @endif
        lengthMenu: [[25, 50, 100, 500, 1000, -1], [25, 50, 100, 500, 1000, "All"]],
		
		dom: 'BlfrptBlip',
        @if(isset($array->export))
        buttons: [],
        @else
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
        @endif

        
		
    });

    $('#table tbody').on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = oTable.row(tr);
        var tableId = 'detail-' + row.data().@if(isset($array->detail_key))<?php echo $array->detail_key; ?>@else<?php echo $array->data[0]->name; ?>@endif ;
        //console.log(row.data());
        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(template(row.data())).show();
            initTable(tableId, row.data());
            tr.addClass('shown');
            tr.next().find('td').addClass('no-padding bg-gray');
            //console.log(row.data());
        }
    });

    function initTable(tableId, data) {
        $('#' + tableId).DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            ajax: data.details_url,
            columns: [
                @foreach ($array->detail as $data)
                    { data: '{{$data->name}}', name: '{{$data->name}}'
                        @if(isset($data->format) and $data->format == 'number')
                        , render: $.fn.dataTable.render.number('.', ',', 0, '')
                        @endif
                    },
                @endforeach
                
            ],order: [[ 0, 'asc' ]]
        })
    }
	

    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });

    @if(isset($array->advance_filter))
    $(".filter_button").click(function(){
    $(".advance_search").slideToggle();
    });

    $(".advance_search").hide();

    $('select[name="kdba"]').change(function() {
        var kdba = $(this).val();
        $('select[name="kdbaes1"]').html("<option>loading...</option>").trigger('chosen:updated');
        $.ajax({
            url:'{{url('/public/ref/es1')}}/'+kdba,
            method:'GET',
            contentType: false,
            processData: false,
            success:function(result){
                $('select[name="kdbaes1"]').html(result).trigger('chosen:updated').change();
            },
            error: function (e, messages, detail){
                alertify.log(detail);
                $('.loading').hide();
            }
        });
    });

    $('select[name="kdbaes1"]').change(function() {
        var kdbaes1 = $(this).val();
        $('select[name="kduappaw"]').html("<option>loading...</option>").trigger('chosen:updated');
        $.ajax({
            url:'{{url('/public/ref/uappaw')}}/'+kdbaes1,
            method:'GET',
            contentType: false,
            processData: false,
            success:function(result){
                $('select[name="kduappaw"]').html(result).trigger('chosen:updated').change();
            },
            error: function (e, messages, detail){
                alertify.log(detail);
            }
        });
    });

    $('select[name="kdkanwildjpbn"]').change(function() {
        var kdkanwildjpbn = $(this).val();
        $('select[name="kdkppn"]').html("<option>loading...</option>").trigger('chosen:updated');
        $.ajax({
            url:'{{url('/public/ref/kppn')}}/'+kdkanwildjpbn,
            method:'GET',
            contentType: false,
            processData: false,
            success:function(result){
                $('select[name="kdkppn"]').html(result).trigger('chosen:updated').change();
            },
            error: function (e, messages, detail){
                alertify.log(detail);
            }
        });
    });

    $('select[name="kdkanwildjkn"]').change(function() {
        var kdkanwildjkn = $(this).val();
        $('select[name="kdkpknl"]').html("<option>loading...</option>").trigger('chosen:updated');
        $.ajax({
            url:'{{url('/public/ref/kpknl')}}/'+kdkanwildjkn,
            method:'GET',
            contentType: false,
            processData: false,
            success:function(result){
                $('select[name="kdkpknl"]').html(result).trigger('chosen:updated').change();
            },
            error: function (e, messages, detail){
                alertify.log(detail);
            }
        });
    });

    $('select[name="kdba"]').trigger('chosen:updated').change();
    @endif
});
</script>
