<?php
/**
 * Dedicated login and registration screen.
 */
?>
<section id="screen-login" class="app-screen" aria-labelledby="login-screen-title">
  <div class="auth-wrapper">
    <div class="auth-container app-card" style="padding: 1.75rem; border-radius: var(--radius-xl);">
      <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
        <button type="button" id="login-btn-logout" class="btn btn-ghost btn-sm hidden" data-i18n="btn_logout">Log Out</button>
      </div>

      <div class="auth-header">
        <div class="auth-brand-badge"><span aria-hidden="true">⚡</span><span data-i18n="app_name">SYNAPSE AI</span></div>
        <h1 id="login-screen-title" class="auth-title" data-i18n="auth_modal_title">Synapse AI Account</h1>
      </div>

      <div class="auth-tabs" role="tablist">
        <button type="button" id="tab-btn-login" class="auth-tab-btn active" role="tab" aria-selected="true" data-i18n="login_tab">Log In</button>
        <button type="button" id="tab-btn-signup" class="auth-tab-btn" role="tab" aria-selected="false" data-i18n="signup_tab">Sign Up</button>
      </div>

      <div id="view-login-form">
        <form id="form-login" novalidate>
          <div class="form-group">
            <label for="login-email" class="form-label" data-i18n="email_label">Email or Username</label>
            <div class="input-wrap">
              <input type="text" id="login-email" class="input-text" data-i18n-placeholder="email_placeholder" placeholder="name@domain.com or username" autocomplete="username" required>
            </div>
            <div id="login-email-error" class="input-error hidden"></div>
          </div>

          <div class="form-group">
            <label for="login-password" class="form-label" data-i18n="password_label">Password</label>
            <div class="input-wrap">
              <input type="password" id="login-password" class="input-password" data-i18n-placeholder="password_placeholder" placeholder="Enter your password" autocomplete="current-password" required>
              <button type="button" class="toggle-pass-visibility" data-target="login-password" title="Toggle password visibility" data-i18n-title="auth_toggle_password">👁️</button>
            </div>
            <div id="login-password-error" class="input-error hidden"></div>
          </div>

          <div class="auth-actions-row">
            <label class="custom-checkbox-wrap">
              <input type="checkbox" id="login-remember" checked>
              <span data-i18n="remember_me">Remember me</span>
            </label>
            <a href="#" id="btn-forgot-password" data-i18n="forgot_password">Forgot password?</a>
          </div>

          <button type="submit" id="btn-submit-login" class="btn btn-primary btn-auth-submit" style="width: 100%; margin-top: 1rem;" data-i18n="login_btn">Log In to Platform</button>
        </form>

        <div class="auth-divider"><span data-i18n="or_divider">OR</span></div>
        <button type="button" class="btn btn-demo btn-demo-login" style="width: 100%; margin-bottom: 0.5rem;" data-i18n="demo_login_btn">⚡ Continue as Demo Learner (Instant Access)</button>
        <button type="button" class="btn btn-secondary btn-continue-guest-action" style="width: 100%;" data-i18n="btn_continue_guest">🎒 Continue in Guest Mode</button>

        <div class="auth-footer">
          <span data-i18n="no_account">Don't have an account yet?</span>
          <a href="#" id="link-to-signup" data-i18n="create_account_link" style="color: var(--color-cyan-light); margin-left: 0.25rem;">Sign Up here</a>
        </div>
      </div>

      <div id="view-signup-form" class="hidden">
        <form id="form-signup" novalidate>
          <div class="form-group">
            <label for="signup-name" class="form-label" data-i18n="name_label">Full Name</label>
            <div class="input-wrap">
              <input type="text" id="signup-name" class="input-text" data-i18n-placeholder="name_placeholder" placeholder="e.g. Alex Morgan or Aarav Patel" autocomplete="name" required>
            </div>
            <div id="signup-name-error" class="input-error hidden"></div>
          </div>

          <div class="form-group">
            <label for="signup-email" class="form-label" data-i18n="email_label">Email Address</label>
            <div class="input-wrap">
              <input type="email" id="signup-email" class="input-text" data-i18n-placeholder="email_placeholder" placeholder="name@domain.com" autocomplete="email" required>
            </div>
            <div id="signup-email-error" class="input-error hidden"></div>
          </div>

          <div class="form-group">
            <label for="signup-password" class="form-label" data-i18n="password_label">Password</label>
            <div class="input-wrap">
              <input type="password" id="signup-password" class="input-password" data-i18n-placeholder="password_placeholder" placeholder="Create a password (min 6 chars)" autocomplete="new-password" required>
              <button type="button" class="toggle-pass-visibility" data-target="signup-password" title="Toggle password visibility" data-i18n-title="auth_toggle_password">👁️</button>
            </div>
            <div id="signup-password-error" class="input-error hidden"></div>
          </div>

          <div class="password-strength-wrap">
            <div class="strength-bar-track"><div id="password-strength-bar" class="strength-bar"></div></div>
            <span id="password-strength-indicator" class="strength-text"></span>
          </div>

          <div class="form-group">
            <label for="signup-confirm-password" class="form-label" data-i18n="confirm_password_label">Confirm Password</label>
            <div class="input-wrap">
              <input type="password" id="signup-confirm-password" class="input-password" data-i18n-placeholder="confirm_password_placeholder" placeholder="Re-enter your password" autocomplete="new-password" required>
              <button type="button" class="toggle-pass-visibility" data-target="signup-confirm-password" title="Toggle password visibility" data-i18n-title="auth_toggle_password">👁️</button>
            </div>
            <div id="signup-confirm-error" class="input-error hidden"></div>
          </div>

          <button type="submit" id="btn-submit-signup" class="btn btn-primary btn-auth-submit" style="width: 100%; margin-top: 1rem;" data-i18n="signup_btn">Create Learner Account</button>
        </form>

        <div class="auth-divider"><span data-i18n="or_divider">OR</span></div>
        <button type="button" class="btn btn-demo btn-demo-login" style="width: 100%;" data-i18n="demo_login_btn">⚡ Continue as Demo Learner (Instant Access)</button>
        <div class="auth-footer">
          <span data-i18n="have_account">Already registered?</span>
          <a href="#" id="link-to-login" data-i18n="login_link" style="color: var(--color-cyan-light); margin-left: 0.25rem;">Log In here</a>
        </div>
      </div>
    </div>
  </div>
</section>
