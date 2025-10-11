{{-- resources/views/auth/login.blade.php --}}
@extends('auth.layouts.app')

@section('content')
<div class="container d-flex justify-content-center align-items-center min-vh-100">
  <div class="card shadow-lg p-4" style="width: 100%; max-width: 400px; border-radius: 1rem;">
    {{-- Logo --}}
    <div class="text-center mb-4">
      <a href="/">
        <img src="{{ asset('boutiqueassets/img/logo-image.jpg') }}" alt="Paradis Déco" class="img-fluid" style="height: 4.5rem;">
      </a>
    </div>

    <h2 class="text-center mb-2" style="font-weight: 700; font-size: 1.75rem;">Bienvenue ! 👋</h2>
    <p class="text-center text-muted mb-4">Connectez-vous à votre compte pour commencer</p>

    <form id="loginForm" action="{{ route('login') }}" method="POST" class="mb-3">
      @csrf

      {{-- Email --}}
      <div class="mb-3">
        <label for="email" class="form-label">Adresse Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-envelope"></i></span>
          <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            required
            autofocus
            class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
            placeholder="votre@email.com"
          >
        </div>
        @error('email')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      {{-- Password --}}
      <div class="mb-3">
        <div class="d-flex justify-content-between mb-1">
          <label for="password" class="form-label">Mot de passe</label>
          <a href="{{ route('password.request') }}" class="text-decoration-none" style="color: #6c757d;">Mot de passe oublié ?</a>
        </div>
        <div class="input-group">
          <span class="input-group-text"><i class="fas fa-lock"></i></span>
          <input
            id="password"
            name="password"
            type="password"
            required
            class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
            placeholder="••••••••"
          >
          <button
            type="button"
            class="btn btn-outline-secondary"
            id="togglePassword"
          >
            <i class="fas fa-eye-slash" id="toggleIcon"></i>
          </button>
        </div>
        @error('password')
          <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
      </div>

      {{-- Remember --}}
      <div class="mb-3 form-check">
        <input id="remember" name="remember" type="checkbox" class="form-check-input" {{ old('remember') ? 'checked' : '' }}>
        <label for="remember" class="form-check-label">Se souvenir de moi</label>
      </div>

      {{-- Submit --}}
      <div class="d-grid gap-2">
        <button
          type="submit"
          id="loginButton"
          class="btn btn-primary"
          style="font-weight: 600;"
        >
          <span id="buttonText">Se connecter</span>
          <span id="buttonSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
        </button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  $(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
      const $pwd = $('#password');
      const $icon = $('#toggleIcon');
      if ($pwd.attr('type') === 'password') {
        $pwd.attr('type', 'text');
        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
      } else {
        $pwd.attr('type', 'password');
        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
      }
    });

    // Show spinner on submit
    $('#loginForm').submit(function() {
      $('#buttonText').text('Chargement…');
      $('#buttonSpinner').removeClass('d-none');
      $('#loginButton').prop('disabled', true);
    });
  });
</script>
@endpush
@endsection
