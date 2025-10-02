
  <!-- Left Sidebar Start -->
        <div class="app-sidebar-menu">
            <div class="h-100" data-simplebar>

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <div class="logo-box">
                        <a href="index.html" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="{{asset("assets/images/logo-sm.png")}}" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{asset("assets/images/logo-light.png")}}" alt="" height="24">
                            </span>
                        </a>
                        <a href="index.html" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="{{asset("assets/images/logo-sm.png")}}" alt="" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="{{asset("assets/images/logo-dark.png")}}" alt="" height="24">
                            </span>
                        </a>
                    </div>

                    <ul id="side-menu">

                        <li class="menu-title">Menu</li>

                        <li>
                            <a href="{{route('dashboard')}}">
                                <i data-feather="home"></i>
                                <span class="badge bg-success rounded-pill float-end">9+</span>
                                <span> Dashboard </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{Route('categories.index')}}">
                                <i data-feather="aperture"></i>
                                <span> Categories </span>
                            </a>
                        </li>

                        <li>
                            <a href="{{Route('subcategories.index')}}">
                                <i data-feather="globe"></i>
                                <span> SubCategories </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{Route('tags.index')}}">
                                <i data-feather="globe"></i>
                                <span> Tags </span>
                            </a>
                        </li>
                        <li>
                            <a href="{{Route('posts.index')}}">
                                <i data-feather="globe"></i>
                                <span> Posts </span>
                            </a>
                        </li>
                    </ul>

                </div>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
        </div>
        <!-- Left Sidebar End -->
