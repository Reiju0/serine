    <nav class="navbar navbar-static-top">
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          
          
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="{{ url($foto) }}" class="user-image" alt="User Image">
              <span class="hidden-xs">{{ $user->username }}</span>
            </a>
            <ul class="dropdown-menu">
                <div class="bg-blue">
                    <ul class="nav nav-stacked">
                      <li> <a href="javascript:ajaxLoad('{{url('/profile')}}')">Profile</a></li>
                      <li><a href="javascript:ajaxLoad('{{url('/password')}}')">Change Password</a></li>
                      <li><a href="javascript:logout()">Logout</a></li>
                    </ul>
            </ul>
          </li>
        </ul>
      </div>
    </nav>