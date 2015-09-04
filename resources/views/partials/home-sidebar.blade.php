<aside id="sidebar">
    <ul class="list-unstyled">
        <li class="sidebar-main-item">
            <a href="{{ url('home') }}">
                <i class="zmdi zmdi-home zmdi-hc-lg zmdi-hc-fw"></i> Dashboard
            </a>
        </li>

        <!-- Template Management -->
        @if(Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager']))
        <li class="sidebar-main-item">
            <a href="">
                <i class="zmdi zmdi-assignment zmdi-hc-lg zmdi-hc-fw"></i> Template
            </a>
            <ul class="list-unstyled sidebar-sub-list">
                <!-- Create a new template -->
                @if(Auth::user()->can('create_template'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/template/create') }}">
                        Add Template
                    </a>
                </li>
                @endif
                <!-- List all templates -->
                @if(Auth::user()->can('read_template'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/template') }}">
                        All Templates
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Category Management -->
        @if(Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager']))
        <li class="sidebar-main-item">
            <a href="">
                <i class="zmdi zmdi-folder zmdi-hc-lg zmdi-hc-fw"></i> Category
            </a>
            <ul class="list-unstyled sidebar-sub-list">
                <!-- Create a new category -->
                @if(Auth::user()->can('create_template'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/category/create') }}">
                        Add Category
                    </a>
                </li>
                @endif
                <!-- List all categories -->
                @if(Auth::user()->can('read_template'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/category') }}">
                        All Categories
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Article Management -->
        @if(Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager', 'article_manager']))
        <li class="sidebar-main-item">
            <a href="">
                <i class="zmdi zmdi-file zmdi-hc-lg zmdi-hc-fw"></i> Article
            </a>
            <ul class="list-unstyled sidebar-sub-list">
                <!-- Create a new article -->
                @if(Auth::user()->can('create_article'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/article/create') }}">
                        Add Article
                    </a>
                </li>
                @endif
                <!-- List all articles -->
                @if(Auth::user()->can('read_article'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/article') }}">
                        All Articles
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- Department Management -->
        @if(Auth::user()->hasRole(['administrator', 'department_manager']))
        <li class="sidebar-main-item">
            <a href="">
                <i class="zmdi zmdi-city-alt zmdi-hc-lg zmdi-hc-fw"></i> Department
            </a>
            <ul class="list-unstyled sidebar-sub-list">
                <!-- Create a new department -->
                @if(Auth::user()->can('create_department'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/department/create') }}">
                        Add Department
                    </a>
                </li>
                @endif
                <!-- List all departments -->
                @if(Auth::user()->can('read_department'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/department') }}">
                        All Departments
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif

        <!-- User Management -->
        @if(Auth::user()->hasRole(['administrator', 'department_manager', 'category_manager']))
        <li class="sidebar-main-item">
            <a href="">
                <i class="zmdi zmdi-account zmdi-hc-lg zmdi-hc-fw"></i> User
            </a>
            <ul class="list-unstyled sidebar-sub-list">
                <!-- Create a new user -->
                @if(Auth::user()->can('create_user'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/user/create') }}">
                        Add User
                    </a>
                </li>
                @endif
                <!-- List all users -->
                @if(Auth::user()->can('read_user'))
                <li class="sidebar-sub-item">
                    <a href="{{ url('home/user') }}">
                        All Users
                    </a>
                </li>
                @endif
            </ul>
        </li>
        @endif
    </ul>
</aside>