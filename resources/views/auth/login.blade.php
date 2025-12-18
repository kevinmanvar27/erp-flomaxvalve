<!DOCTYPE html>
<html>

<head>
    <title>Login Page</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>

{{-- <body style="background-image:url({{ asset('assets/login.png') }})"> --}}
    <body>
        <div class="container text-center">
            {{-- <img src="{{ asset('assets/flowmax.png') }}" alt="Logo" class="img-fluid mb-3 logoPart"> --}}
    
            <div class="d-flex justify-content-center h-100">
                <div class="card">
                    <div class="card-header mt-5">
                        <h3>Sign In</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
    
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="usercode" class="form-control" name="usercode" placeholder="Enter User Code" value="{{ old('usercode') }}">
                                @error('usercode')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="input-group form-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" class="form-control" placeholder="password" name="password">
                                @error('password')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
    
                            <div class="row align-items-center remember">
                                <input type="checkbox" name="remember">Remember Me
                            </div>
    
                            <div class="form-group">
                                <input type="submit" value="Login" class="btn float-right login_btn">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
    

</html>
