<div class="card panel-info" id="main_content">
    <div class="card-header">
        <h2 align="center">List Permission
            <div class="btn-group pull-right">
            <a href="javascript:ajaxLoad('{{url('/permission/create')}}')" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </h2>

    </div>
    <div class="card-body">
        <form method="POST" id="search-form" class="form-inline" role="form">
            <div class="form-group">
                
                <input type="text" class="form-control" name="module" id="module" placeholder="search module">
            </div>
            <div class="form-group">
                
                <input type="text" class="form-control" name="permission" id="permission" placeholder="search permission">
            </div>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="table" style="width: 100%">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Module</th>
                        <th>Permission</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<div id="main_detail" style="display: none">
</div>

<script>
$(document).ready(function() {

    
    var oTable = $('#table').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        ajax: {
            url : '{{ url('/permission/data') }}',
            data: function (d){
                d.module = $('input[name=module]').val();
                d.permission = $('input[name=permission]').val();
            }
        },
        columns: [
            { data: 'nomor', name: 'nomor', sortable: false, orderable: false, searchable: false },
            { data: 'module', name: 'module' },
            { data: 'permission', name: 'permission', searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [['1', 'asc']],
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]]
    });

    $('#search-form').on('submit', function(e) {
        oTable.draw();
        e.preventDefault();
    });
});

    function get_detail(id) {
            $('.loading').show();
            $.ajax({
                type: "GET",
                url: '{{ url('/permission/detail') }}/'+id,
                contentType: false,
                success: function (data) {
                    $("#main_detail").html(data).show();
                    $('.loading').hide();
                    $("#main_content").hide();

                },
                error: function (xhr, status, error) {
                    alert(xhr.responseText);
                }
            });
    }

    function detail_close(){
        $("#main_detail").hide();
        $("#main_content").show();
    }

function data_delete(id){
    //var id = $(this).attr('id');
    alertify.confirm('Anda yakin akan menghapus data ini?', function (e) {
        if (e) {
            $.ajax({
                type: 'GET',
                url: '{{url('/permission/delete')}}/'+id,
                contentType: false,
                success: function (data) {
                    if(data == 'success'){
                        alertify.log('data berhasil dihapus');
                        ajaxLoad('{{url('/permission')}}');
                    }else{
                        alertify.log(data);
                    }
                    
                }
            });
                
        }
    });
}


</script>
