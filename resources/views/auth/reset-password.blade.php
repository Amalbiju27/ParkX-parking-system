<h2>Reset Password</h2>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="New password" required><br><br>
    <input type="password" name="password_confirmation" placeholder="Confirm password" required><br><br>

    <button type="submit">Reset Password</button>
</form>

@error('email')
    <p style="color:red">{{ $message }}</p>
@enderror
