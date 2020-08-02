  <aside class="main-sidebar">
    <section class="sidebar">
      <a href="javascript:ajaxLoad('{{url('/profile')}}')">
      <div class="user-panel">
        <div class="pull-left image">
          <img src="{{ url($foto) }}" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>{{ $user->nama }}</p>
          <p>{{ $user->nip }}</p>
        </div>
      </div>
      </a>
      
        {!!$menu!!}
      
    </section>
  </aside>
