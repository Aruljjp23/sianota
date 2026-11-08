<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f4f4f2;
            font-family: 'Poppins', sans-serif;
            color: #16281f;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            color: #1a5c42;
            font-size: 22px;
        }

        .brand {
            margin-bottom: 7px;
            font-family: 'Playfair Display', serif;
            font-size: 34px;
            font-weight: 700;
            color: #16281f;
        }

        .tagline {
            font-size: 13.5px;
            color: #8a8375;
        }

        .login-card {
            padding: 34px 32px;
            background: #ffffff;
            border: 1px solid #ece6d8;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(31, 41, 33, 0.08);
        }

        .login-title {
            margin-bottom: 26px;
            text-align: center;
            font-size: 19px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 500;
            color: #354139;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            z-index: 1;
            transform: translateY(-50%);
            color: #a3ac9f;
            font-size: 14px;
            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: 46px;
            padding: 0 42px 0 40px;
            border: 1.5px solid #e6e2d5;
            border-radius: 10px;
            outline: none;
            background: #fbf9f4;
            color: #24301f;
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.2s ease, background 0.2s ease, box-shadow 0.2s ease;
        }

        .form-input::placeholder {
            color: #ada697;
        }

        .form-input:focus {
            border-color: #1a5c42;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(26, 92, 66, 0.1);
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            padding: 0;
            border: none;
            transform: translateY(-50%);
            background: transparent;
            color: #a3ac9f;
            cursor: pointer;
        }

        .password-toggle:hover {
            color: #1a5c42;
        }

        .login-button {
            width: 100%;
            height: 46px;
            border: none;
            border-radius: 10px;
            background: #1a5c42;
            color: #ffffff;
            font-family: inherit;
            font-size: 14.5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.1s ease;
        }

        .login-button:hover {
            background: #134a36;
        }

        .login-button:active {
            transform: scale(0.98);
        }

        .register-text {
            margin-top: 20px;
            text-align: center;
            color: #918a7b;
            font-size: 12.5px;
        }

        .register-link {
            color: #1a5c42;
            font-weight: 600;
            text-decoration: none;
        }

        .register-link:hover {
            text-decoration: underline;
        }

        .error-message {
            margin-bottom: 18px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #fff1f1;
            color: #b42318;
            font-size: 12.5px;
        }

        .success-message {
            margin-bottom: 18px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #edf8f1;
            color: #176b42;
            font-size: 12.5px;
        }

        @media (max-width: 480px) {

            body {
                padding: 18px;
            }

            .login-card {
                padding: 28px 22px;
            }

            .brand {
                font-size: 28px;
            }

            .tagline {
                font-size: 12.5px;
            }

        }
    </style>

</head>

<body>

    <main class="login-container">

        <header class="login-header">

            <h1 class="brand">
                Sistem Informasi Pengelolaan Data Notaris
            </h1>

            <p class="tagline">
                Pemilik notaris Dr. Arpa Syura Tambuno, S.H., M.Kn.
            </p>

        </header>


        <section class="login-card">

            <h2 class="login-title">
                Register
            </h2>


            @if ($errors->any())

                <div class="error-message">
                    {{ $errors->first() }}
                </div>

            @endif


            <form action="{{ route('register.process') }}" method="POST">

                @csrf


                <div class="form-group">

                    <label for="name" class="form-label">
                        Username
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-user input-icon"></i>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-input"
                            placeholder="Masukkan username"
                            value="{{ old('name') }}"
                            autocomplete="name"
                            required
                        >

                    </div>

                </div>

                <div class="form-group">

                    <label for="password" class="form-label">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-lock input-icon"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-input"
                            placeholder="Masukkan password"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                            aria-label="Tampilkan password"
                        >
                            <i class="fa-solid fa-eye"></i>
                        </button>

                    </div>

                </div>

                <button type="submit" class="login-button">
                    Register
                </button>

            </form>


            <p class="register-text">

                Sudah punya akun?

                <a href="{{ route('login') }}" class="register-link">
                    Login
                </a>

            </p>

        </section>

    </main>


    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {

            const isPassword = passwordInput.type === 'password';

            passwordInput.type = isPassword ? 'text' : 'password';

            const icon = this.querySelector('i');

            icon.classList.toggle('fa-eye', !isPassword);
            icon.classList.toggle('fa-eye-slash', isPassword);

        });
    </script>

</body>

</html>