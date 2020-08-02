<div class="card" id="main_content">
    <div class="card-header">
        <h2 align="center">List Role
            <div class="btn-group pull-right">
            <a href="javascript:ajaxLoad('{{url('/role/create')}}')" class="btn btn-xs btn-primary"><i class="fa fa-plus"></i> Tambah</a>
            </div>
        </h2>

    </div>
    <div class="card-body">
        <form method="POST" id="search-form" class="form-inline" role="form">
            <div class="form-group">
                
                <input type="text" class="form-control" name="name" id="name" placeholder="search role">
            </div>
            <div class="form-group">
                <input type="text" class="form-control" name="slug" id="slug" placeholder="search slug">
            </div>

            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered wrap" id="table" style="width: 100%">
                <thead>
                    <tr>
                        <th>Nomor</th>
                        <th>Role</th>
                        <th>Slug</th>
                        <th>Group</th>
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
            url : '{{ url('/role/data') }}',
            data: function (d){
                d.name = $('input[name=name]').val();
                d.permission = $('input[name=permission]').val();
            }
        },
        columns: [
            { data: 'nomor', name: 'nomor', sortable: false, orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' },
            { data: 'group', name: 'group'},
            { data: 'json_permission', name: 'permissions', searchable: false },
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
                url: '{{ url('/role/detail') }}/'+id,
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

function data_delete(id, slug){
    //var id = $(this).attr('id');

    alertify.prompt('Untuk menghapus role ini, masukkan: '+slug, 
        function(evt, value) { 
            if(value==slug){
                $.ajax({
                    type: 'GET',
                    url: '{{url('/role/delete')}}/'+id,
                    contentType: false,
                    success: function (data) {
                        if(data == 'success'){
                            alertify.log('data berhasil dihapus');
                            ajaxLoad('{{url('/role')}}');
                        }else{
                            alertify.log(data);
                        }
                        
                    }
                });
            }else{
                alertify.error('Kode salah');
            }
        });

    /*alertify.prompt( 'Konfirmasi', 'Jika anda ingin menghapus role ini silahkan masukkan: '+slug, 'value'
               , function(evt, value) { alertify.success('You entered: ' + value) }
               , function() { alertify.error('Cancel') });

    alertify.confirm('Anda yakin akan menghapus data ini?', function (e) {
        if (e) {
            
                
        }
    });
    */
}


</script>
