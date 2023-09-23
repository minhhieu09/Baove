<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Login</title>

    <link href="{{ mix('/css/store_login.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up-container">
            <form action="{{ route(STORE_REGISTER) }}" method="post">
                @csrf
                <h1>Tạo tài khoản</h1>
                @include('common.noti_message')
                <input type="text" name="name" placeholder="Nhập tên người dùng" required/>
                <input type="email" name="email" placeholder="Nhập địa chỉ email" required/>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required/>
                <input type="password" name="repassword" placeholder="Nhập lại mật khẩu" required/>
                <button>Đăng kí</button>
            </form>
        </div>
        <div class="form-container sign-in-container">
            <form action="{{ route(STORE_LOGIN) }}" method="post">
                @csrf
                <h1>Đăng nhập</h1>
                @include('common.noti_message')
                <input type="email" name="email" placeholder="Nhập địa chỉ email" required/>
                <input type="password" name="password" placeholder="Nhập mật khẩu" required/>
                <a href="{{ route(STORE_FORM_FORGOT_PASSWORD) }}">Forgot your password?</a>
                <button>Đăng nhập</button>
            </form>
        </div>
        <div class="overlay-container">
            <div class="overlay">
                <div class="overlay-panel overlay-left">
                    <h1>Welcome Back!</h1>
                    <p>To keep connected with us please login with your personal info</p>
                    <button class="ghost" id="signIn">Sign In</button>
                </div>
                <div class="overlay-panel overlay-right">
                    <h1>Hello, Friend!</h1>
                    <p>Enter your personal details and start journey with us</p>
                    <button class="ghost" id="signUp">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    signUpButton.addEventListener('click', () => {
        container.classList.add("right-panel-active");
    });

    signInButton.addEventListener('click', () => {
        container.classList.remove("right-panel-active");
    });
</script>
