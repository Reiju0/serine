<div class="row match-height">
  <div class="col-md-4 col-12">
	<div class="card">
	  <div class="card-header text-center pb-0">
        <span class="font-medium-2 warning">Total</span>
        <h3 class="font-large-2 mt-2">{{$rekap->total}}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12">
    <div class="card">
	  <div class="card-header text-center pb-0">
        <span class="font-medium-2 info">Selesai</span>
        <h3 class="font-large-2 mt-2">{{$rekap->selesai}}</h3>
      </div>
    </div>
  </div>
  <div class="col-md-4 col-12">
    <div class="card">
	  <div class="card-header text-center pb-0">
        <span class="font-medium-2 danger">Progress</span>
        <h3 class="font-large-2 mt-2">{{$rekap->progress}}</h3>
      </div>
    </div>
  </div>
</div>

<div class="row match-height">
  <div class="col-xl-8 col-lg-12 col-12">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title mb-0">To Do List Hari Ini</h4>
      </div>
      <div class="card-content">
        <div class="card-body">
		  <div class="px-3">
			  <div class="table-responsive">
              <table id="table" class="table table-striped table-bordered zero-configuration">
                <thead>
                  <tr>
                    <th>Jenis</th>
                    <th>Description</th>
                    <th>Batas Waktu</th>
                    <th>Penerima</th>
                    <th>Selesai</th>
                    <th>Progress</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($today as $r_today)
				  <tr>
                    <td>{{$r_today->jenis}}</td>
                    <td>{{$r_today->description}}</td>
                    <td>{{$r_today->deadline}}</td>
                    <td>{{$r_today->jumlah}}</td>
                    <td>{{$r_today->selesai}}</td>
                    <td>{{$r_today->progress}}</td>
                  </tr>
				  @endforeach
				</tbody>
			  </table>
			  </div>
		  </div>
        </div>
      </div>
    </div>
  </div>
  
  <div class="col-xl-4 col-lg-12">
    <div class="card">
	  <div class="card-header">
        <h4 class="card-title mb-0">To Do List Per Hari</h4>
      </div>
      <div class="card-content">
        <div class="px-3">
		<div class="card-body">
          <ul class="list-group mb-3">
            @foreach($rank as $r_rank)
			<li class="list-group-item">
              <span>{{$r_rank->tanggal}}</span>
              <span class="badge bg-danger float-right mb-1 mr-2"><font color="white">{{$r_rank->jumlah}}</font></span>
            </li>
			@endforeach
          </ul>
        </div>
		</div>
      </div>
</div>



<script>

var oTableDt;

$(document).ready(function() {
   oTableDt = $('#table').DataTable({
	   "pageLength": 5
   });
});

</script>