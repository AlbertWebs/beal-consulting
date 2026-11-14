<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#edf2f7">
    <title>Change password · BEAL CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cms-shell cms-login-page">
    <main class="cms-login-card" aria-labelledby="password-title">
        <div class="cms-login-accent" aria-hidden="true"></div>
        <div class="cms-login-content">
            <div class="cms-login-heading">
                <div>
                    <p class="cms-login-eyebrow">One-time setup</p>
                    <h1 id="password-title">Choose a new password</h1>
                    <p class="cms-login-subtitle">Set a private password before you continue to the CMS.</p>
                </div>
                <span class="cms-login-emblem" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none"><rect x="4" y="10" width="16" height="11" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 10V7a4 4 0 1 1 8 0v3m-4 4v3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                </span>
            </div>

            @if($errors->any())
                <div class="cms-banner cms-banner-error cms-login-error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ route('admin.password.update') }}">
                @csrf
                @method('PUT')
                <div class="cms-login-field">
                    <label for="current_password">Temporary password</label>
                    <input class="cms-input cms-login-plain-input" type="password" name="current_password" id="current_password" required autocomplete="current-password">
                </div>
                <div class="cms-login-field">
                    <label for="password">New password</label>
                    <input class="cms-input cms-login-plain-input" type="password" name="password" id="password" required minlength="16" autocomplete="new-password" aria-describedby="password-help">
                </div>
                <div class="cms-login-field">
                    <label for="password_confirmation">Confirm new password</label>
                    <input class="cms-input cms-login-plain-input" type="password" name="password_confirmation" id="password_confirmation" required minlength="16" autocomplete="new-password">
                </div>
                <p class="cms-login-note" id="password-help">Use at least 16 characters, including uppercase and lowercase letters, a number, and a symbol.</p>
                <button class="cms-login-submit" type="submit">Update password <span aria-hidden="true">↗</span></button>
            </form>
            <form method="post" action="{{ route('admin.logout') }}" class="cms-login-signout">@csrf<button type="submit">Sign out</button></form>
        </div>
    </main>
</body>
</html>
