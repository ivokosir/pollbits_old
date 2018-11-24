<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('polls.create') }}">{{ config('app.name') }}</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link{{ Route::currentRouteNamed('polls.create') ? ' active' : '' }}" href="{{ route('polls.create') }}">Add Poll</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ Route::currentRouteNamed('polls.index') ? ' active' : '' }}" href="{{ route('polls.index') }}">All Polls</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link{{ Route::currentRouteNamed('about') ? ' active' : '' }}" href="{{ route('about') }}">About</a>
                </li>
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <form action="{{ route('polls.index') }}" class="form-inline mx-md-2 my-2 my-md-0">
                    <input class="form-control" type="text" placeholder="Search" name="search" value="{{ $search ?? '' }}" maxlength="250" required>
                </form>
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link{{ Route::currentRouteNamed('login') ? ' active' : '' }}" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    <li class="nav-item">
                        @if (Route::has('register'))
                            <a class="nav-link{{ Route::currentRouteNamed('register') ? ' active' : '' }}" href="{{ route('register') }}">{{ __('Register') }}</a>
                        @endif
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="{{ route('account') }}">My Account</a>
                            <button class="dropdown-item cursor-pointer" form="logout-form">{{ __('Logout') }}</button>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" hidden">@csrf</form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
