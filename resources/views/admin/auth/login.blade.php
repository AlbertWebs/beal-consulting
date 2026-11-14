<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#edf2f7">
    <title>Admin access · BEAL Consulting</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cms-shell cms-login-page">
    <main class="cms-login-card" aria-labelledby="login-title">
        <div class="cms-login-accent" aria-hidden="true"></div>
        <div class="cms-login-content">
            <div class="cms-login-heading">
                <div>
                    <p class="cms-login-eyebrow">Admin access</p>
                    <h1 id="login-title">BEAL Consulting</h1>
                    <p class="cms-login-subtitle">Sign in to manage content and settings.</p>
                </div>
                <span class="cms-login-emblem" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><path d="M20 21v-2a6 6 0 0 0-12 0v2M14 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
            </div>

            @if($errors->any())
                <div class="cms-banner cms-banner-error cms-login-error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ route('admin.login.store') }}">
                @csrf
                <div class="cms-login-field">
                    <label for="email">Email</label>
                    <div class="cms-login-input-wrap">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="m4 7 8 6 8-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username" autofocus>
                    </div>
                </div>

                <div class="cms-login-field">
                    <label for="password">Password</label>
                    <div class="cms-login-input-wrap">
                        <svg aria-hidden="true" viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 4v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                        <input type="password" name="password" id="password" required autocomplete="current-password">
                        <button class="cms-login-password-toggle" type="button" aria-controls="password" aria-label="Show password" data-password-toggle>Show</button>
                    </div>
                </div>

                <div class="cms-login-options">
                    <label class="cms-login-remember"><input type="checkbox" name="remember" value="1" @checked(old('remember'))><span>Remember me</span></label>
                    <a href="{{ route('home') }}">Back to site</a>
                </div>

                <button class="cms-login-submit" type="submit">Login <span aria-hidden="true">↗</span></button>
                <p class="cms-login-note">This area is for authorized BEAL Consulting CMS users. If you need access, contact your administrator.</p>
            </form>
        </div>
    </main>
    <script>
        document.querySelector('[data-password-toggle]')?.addEventListener('click', (event) => {
            const button = event.currentTarget;
            const password = document.getElementById(button.getAttribute('aria-controls'));
            const isVisible = password.type === 'text';
            password.type = isVisible ? 'password' : 'text';
            button.textContent = isVisible ? 'Show' : 'Hide';
            button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
        });
    </script>
</body>
</html>
