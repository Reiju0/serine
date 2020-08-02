<div class="card">
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
			@elseif ($input->type == 'verifikasi')
				
				<div class="tab-content py-3 px-3 px-sm-0">
					@foreach ($array->tab as $navigation)
					<div class=" fade show {!!$navigation->class!!} " id="nav-{!!$navigation->name!!}" role="tabpanel" aria-labelledby="nav-{!!$navigation->name!!}-tab">
					 <form class="form form-horizontal" enctype="multipart/form-data" id="user" onsubmit="return false;" data-toggle="validator">
						 @if($navigation->name == 'pribadi')
							@foreach ($array->pribadi as $data)
							<div class="form-group row">
								<label class="col-md-2 label-control">{!!$data->caption!!}</label>
								<div class="col-md-10">
									<input type="text" class="form-control"  value="{!!$data->value!!}" readonly="">
								</div>
							</div>
							 @endforeach
						@endif
						
						@if($navigation->name == 'kepangkatan')
							<h4 class="form-section"><i class="ft-bar-chart"></i> Riwayat SK Pangkat/Gol </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>No</th>
											<th>Tanggal</th>
											<th>Keterangan</th>
											<th>File</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->kepangkatan as $data)
										<tr>
											<td>{!!$data->no_pangkat!!}</td>
											<td>{!!substr($data->tgl_pangkat,0,10)!!}</td>
											<td>{!!$data->ket_pangkat!!}</th>
											<td> <a href = "{!!$data->nm_file!!}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file-o"></i></td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						@if($navigation->name == 'jabatan_struk')
							<h4 class="form-section"><i class="ft-tag"></i> Riwayat Jabatan Struktural </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>No</th>
											<th>tanggal</th>
											<th>keterangan</th>
											<th>File</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->jabatan_struk as $data)
										<tr>
											<td>{!!$data->no_jabstruk!!}</td>
											<td>{!!substr($data->tgl_jabstruk,0,10)!!}</td>
											<td>{!!$data->ket_jabstruk!!}</td>
											<td> <a href = "{!!$data->nm_file!!}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file-o"></i> Download</a></td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						@if($navigation->name == 'jabatan_fung')
							<h4 class="form-section"><i class="ft-tag"></i> Riwayat Jabatan Fungsional </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>No</th>
											<th>tanggal</th>
											<th>keterangan</th>
											<th>File</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->jabatan_fung as $data)
										<tr>
											<td>{!!$data->no_jabfung!!}</td>
											<td>{!!substr($data->tgl_jabfung,0,10)!!}</td>
											<td>{!!$data->ket_jabfung!!}</td>
											<td> <a href = "{!!$data->nm_file!!}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file-o"></i> Download</a></td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						@if($navigation->name == 'pendidikan')
							<h4 class="form-section"><i class="ft-bookmark"></i> Riwayat Pendidikan </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>Keterangan</th>
											<th>Tahun Lulus</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->pendidikan as $data)
										<tr>
											<td>{!!$data->ket_didik!!}</td>
											<td>{!!$data->thn_lulus!!}</td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						
						
						
						@if($navigation->name == 'pelatihan')
							<h4 class="form-section"><i class="ft-bookmark"></i> Riwayat Diklat/Pelatihan </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>No</th>
											<th>tanggal</th>
											<th>keterangan</th>
											<th>File</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->pelatihan as $data)
										<tr>
											<td>{!!$data->no_diklat!!}</td>
											<td>{!!substr($data->tgl_diklat,0,10)!!}</td>
											<td>{!!$data->ket_diklat!!}</td>
											<td> <a href = "{!!$data->nm_file!!}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file-o"></i> Download</a></td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						
						
						@if($navigation->name == 'penghargaan')
							<h4 class="form-section"><i class="ft-award"></i> Riwayat Penghargaan </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>No</th>
											<th>tanggal</th>
											<th>keterangan</th>
											<th>File</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->penghargaan as $data)
										<tr>
											<td>{!!$data->no_jasa!!}</td>
											<td>{!!substr($data->tgl_jasa,0,10)!!}</td>
											<td>{!!$data->ket_jasa!!}</td>
											<td> <a href = "{!!$data->nm_file!!}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file-o"></i> Download</a></td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						@if($navigation->name == 'file_pendukung')
							<h4 class="form-section"><i class="ft-upload"></i> Kelengkapan Dokumen </h4>
							<div class="table-responsive">
								<table class="table table-striped table-bordered hover" id="table_{{$array->title}}" style="width: 99%">
									<thead>
										<tr>
											<th>jenis</th>
											<th>No</th>
											<th>Tanggal</th>
											<th>File</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($array->file_pendukung as $data)
										<tr>
											<td>{!!$data->nm_jnsdok!!}</td>
											<td>{!!$data->nodok!!}</td>
											<td>{!!substr($data->tgldok,0,10)!!}</td>
											<td> <a href = "{!!$data->nm_file!!}" target="_blank" class="btn btn-xs btn-info"><i class="fa fa-file-o"></i> Download</a></td>
										</tr>
										 @endforeach
									</tbody>
								</table>
							</div>
							 
						@endif
						
						
						
					 </form>
					</div>
					@endforeach
					
					<div class="form-group row">
						<label class="col-md-2 label-control">Keterangan</label>
						<div class="col-md-10">
							
							<textarea name="keterangan" id="keterangan" class="form-control" placeholder="Isikan Keterangan"></textarea>
							<input type="hidden" id="id_detil" name="id_detil" class="form-control"  value="{!!$array->id!!}" required >
						</div>
					</div>
					<div class="form-group row">
						<label class="col-md-2 label-control">Setuju</label>
						<div class="col-md-10">
							<input type="radio" name="setuju" id="link_yes" value="21" required="" checked> Setuju
							<input type="radio" name="setuju" id="link_need" value="23" required=""> Perlu Perbaikan
							<input type="radio" name="setuju" id="link_no" value="22"> Tidak Setuju
						</div>
					</div>
					

					</div>
					
				</div>
			</nav>
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
                        <input type="{{$input->type}}" class="form-control @if(isset($input->class)){{$input->class}} @endif" name="{{$input->name}}" id="{{$input->name}}" placeholder="@if(isset($input->text)){{$input->text}} @else {{$input->value}} @endif" @if(isset($input->required)) required @endif  @if(isset($input->readonly)) readonly @endif @if(isset($input->value)) value="{{$input->value}}" @endif>
                    @elseif($input->type=='button')
                    <a href="{{$input->url}}" @if(isset($input->target)) target="$input->taget"@endif><button type="{{$input->typebutton}}" class="$input->classbutton">{{$input->namebutton}}</button></a>
                    @elseif($input->type=='p')
                    {!! $input->value !!}
                    @elseif ($input->type == 'select')
                        <select name="{{$input->name}}" id="{{$input->name}}" data-placeholder="@if(isset($input->text)){{$input->text}}@endif" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12 @if(isset($input->class)){{$input->class}} @endif" @if(isset($input->required)) required @endif>
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
                        <select name="{{$input->name}}" id="{{$input->name}}" data-placeholder="@if(isset($input->text)){{$input->text}}@endif" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12 @if(isset($input->class)){{$input->class}} @endif" multiple @if(isset($input->required)) required @endif>
                            @foreach ($input->option as $option)
                            <option value="{{$option->val}}">{{$option->text}}</option>
                            @endforeach
                        </select>
                    @elseif ($input->type == 'radio')
                        @foreach ($input->option as $option)
                        <div><input type="radio" name="{{$input->name}}" value="{{$option->val}}" @if(isset($input->value) and $input->value == $option->val) checked="" @endif class="@if(isset($input->class)){{$input->class}} @endif"> {{$option->text}}</div>
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
                        @if (isset($input->value))
                        <p class="help-block"><a href="{{$input->value}}" target="_blank">Download File</a></p>
                        @endif
					@elseif ($input->type == 'radio-inline')
                        @foreach ($input->option as $option)
						<label class="radio-inline">
							<input type="radio" name="{{$input->name}}" value="{{$option->val}}" @if(isset($input->value) and $input->value == $option->val) checked="checked" @endif> {{$option->text}}
						</label>
						@endforeach
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
                <button type="submit" class="btn btn-success" id="submit">@if(isset($array->text_simpan)){{$array->text_simpan}} @else KIRIM @endif</button> <a onclick="confirmation()" class="btn btn-danger">BATAL</a>
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
            @if(isset($array->alertify_open))
            {!! $array->alertify_open !!}
            @endif
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
                        @if(isset($array->forward))
                        ajaxLoad('{{ $array->forward }}');
                        @else
                        ajaxLoad('{{ $array->back }}');
                        @endif
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

            })
			
			;
            @if(isset($array->alertify_close))
            {!! $array->alertify_close !!}
            @endif
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