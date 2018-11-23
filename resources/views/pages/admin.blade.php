<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }}</title>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <main class="py-4">
            <div class="container">
                <form action="{{ route('admin.run') }}" method="post">
                    @csrf

                    <div class="form-group row">
                        <label for="password" class="col-md-3 col-form-label text-md-right">{{ __('Password') }}</label>

                        <div class="col-md-4">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-3 col-form-label text-md-right">Database Actions</label>
                        <div class="btn-group col-md-4">
                            <button class="btn btn-primary" name="action" value="migrate">Migrate</button>
                            <button class="btn btn-primary" name="action" value="reset">Reset</button>
                        </div>
                    </div>
                </form>

                @if (session('success'))
                <div class="row">
                    <div class="col-md-auto offset-md-3">
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
                @if ($errors->has('exitCode'))
                <div class="row">
                    <div class="col-md-auto offset-md-3">
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ $errors->first('exitCode') }}
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
