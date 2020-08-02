<div class="card panel-info" id="main_content">
    <div class="card-header">
        <h2 align="center">Data Menu
            <div class="btn-group pull-right">
            <a href="javascript:ajaxLoad('{{url('/menu/create')}}')" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </h2>

    </div>

    <div class="card-body">
        <form method="POST" id="search-form" class="form-inline" role="form">
                <div class="form-group">
                    <div class="col-xs-5 col-sm-5 col-md-5 col-lg-5">
                        <select name="menu" id="menu" data-placeholder="pilih periode" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            {!! PublicFunction::get_parent() !!}
                        </select>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
                        <select name="parent" id="parent" data-placeholder="" class="chosen-select col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <option value="">All</option>
                            <option value="1">Parent</option>
                            <option value="0">Non Parent</option>
                        </select>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
                        <input type="text" class="form-control" name="nmmenu" id="nmmenu" placeholder="Nama Menu">
                    </div>
                    <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1" align="center">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i> </button> 
                    </div>
                </div>
        </form>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="" style="width: 100%">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>id</th>
                        <th>Menu</th>
                        <th>is Parent?</th>
                        <th>parent_id</th>
                        <th>Urutan</th>
                        <th>Permission</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>


<script>
$(document).ready(function() {

    
    $(".chosen-select").chosen({no_results_text: "Oops, nothing found!"});
    var oTable = $('table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url : '{{ url('/menu/data') }}',
            data: function (d){
                  d.menu = $('select[name=menu]').val();
                  d.parent = $('select[name=parent]').val();
                  d.nmmenu=$('input[name=nmmenu]').val();                 
            }
        },
        columns: [
            { data: 'nomor', name: 'nomor', sortable: false, orderable: false, searchable: false },
            { data: 'id', name: 'id' },
            { data: 'menu', name: 'menu' },
            { data: 'is_parent', name: 'is_parent' },
            { data: 'parent_id', name: 'parent_id' },
            { data: 'delta', name: 'delta' },
            { data: 'permission_break', name: 'permission' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });
});


function data_delete(id){
    //var id = $(this).attr('id');
    alertify.confirm('Anda yakin akan menghapus data ini?', function (e) {
        if (e) {
            $.ajax({
                type: 'GET',
                url: '{{url('/menu/delete')}}/'+id,
                contentType: false,
                success: function (data) {
                    if(data == 'success'){
                        alertify.log('data berhasil dihapus');
                        ajaxLoad('{{url('/menu')}}');
                    }else{
                        alertify.log(data);
                    }
                    
                }
            });
                
        }
    });
}


</script>
