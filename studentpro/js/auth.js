/**
 * Authentication Module
 * Manages demo authentication, validation, session persistence, and UI events.
 */
const Auth = {
  activeTab: 'login', // 'login' | 'signup'
  returnStep: null,

  init() {
    this.bindEvents();
    this.updateHeaderUI();
    window.addEventListener('synapse:auth_changed', () => this.updateHeaderUI());
    window.addEventListener('synapse:profile_updated', () => this.updateHeaderUI());
    window.addEventListener('synapse:language_changed', () => this.updateHeaderUI());
  },

  bindEvents() {
    // Open authentication as a dedicated screen from the header account icon.
    const userMenuBtn = document.getElementById('header-user-menu-btn');
    if (userMenuBtn) {
      userMenuBtn.addEventListener('click', () => {
        const current = Onboarding ? Onboarding.currentStep : 'hub';
        this.openLogin(current !== 'login' ? current : 'hub');
      });
    }

    // Return to the previous screen from the standalone auth page.
    const closeAuthBtn = document.getElementById('login-btn-back');
    if (closeAuthBtn) {
      closeAuthBtn.addEventListener('click', () => this.closeLoginModal());
    }

    const logoutBtn = document.getElementById('login-btn-logout');
    if (logoutBtn) logoutBtn.addEventListener('click', () => this.logout());

    // Continue as Guest buttons
    const guestBtns = document.querySelectorAll('.btn-continue-guest-action');
    guestBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.closeLoginModal();
      });
    });

    // Tab buttons
    const tabLogin = document.getElementById('tab-btn-login');
    const tabSignup = document.getElementById('tab-btn-signup');

    if (tabLogin) tabLogin.addEventListener('click', () => this.switchTab('login'));
    if (tabSignup) tabSignup.addEventListener('click', () => this.switchTab('signup'));

    // Switch links inside cards
    const toSignupLink = document.getElementById('link-to-signup');
    const toLoginLink = document.getElementById('link-to-login');
    if (toSignupLink) toSignupLink.addEventListener('click', (e) => { e.preventDefault(); this.switchTab('signup'); });
    if (toLoginLink) toLoginLink.addEventListener('click', (e) => { e.preventDefault(); this.switchTab('login'); });

    // Forms
    const loginForm = document.getElementById('form-login');
    const signupForm = document.getElementById('form-signup');
    if (loginForm) loginForm.addEventListener('submit', (e) => this.handleLogin(e));
    if (signupForm) signupForm.addEventListener('submit', (e) => this.handleSignup(e));

    // Demo fast login buttons
    const demoBtns = document.querySelectorAll('.btn-demo-login');
    demoBtns.forEach(btn => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        this.loginDemoUser();
      });
    });

    // Forgot password button
    const forgotBtn = document.getElementById('btn-forgot-password');
    if (forgotBtn) {
      forgotBtn.addEventListener('click', (e) => {
        e.preventDefault();
        this.handleForgotPassword();
      });
    }

    // Password strength check on signup password input
    const signupPassword = document.getElementById('signup-password');
    if (signupPassword) {
      signupPassword.addEventListener('input', (e) => this.checkPasswordStrength(e.target.value));
    }

    // Password visibility toggles
    const togglePassBtns = document.querySelectorAll('.toggle-pass-visibility');
    togglePassBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetId = btn.getAttribute('data-target');
        const input = document.getElementById(targetId);
        if (input) {
          const isPass = input.type === 'password';
          input.type = isPass ? 'text' : 'password';
          btn.textContent = isPass ? '👁️‍🗨️' : '👁️';
        }
      });
    });
  },

  openLogin(targetStep = 'hub') {
    this.returnStep = targetStep;
    this.activeTab = 'login';
    this.switchTab('login');
    Onboarding.goToStep('login');
  },

  closeLoginModal() {
    const targetStep = this.returnStep && this.returnStep !== 'login' ? this.returnStep : 'hub';
    this.returnStep = null;
    Onboarding.goToStep(targetStep);
  },

  updateHeaderUI() {
    const isAuth = StorageService.isAuthenticated();
    const logoutBtn = document.getElementById('login-btn-logout');
    if (logoutBtn) logoutBtn.classList.toggle('hidden', !isAuth);
  },

  switchTab(tab) {
    this.activeTab = tab;
    const loginView = document.getElementById('view-login-form');
    const signupView = document.getElementById('view-signup-form');
    const tabLogin = document.getElementById('tab-btn-login');
    const tabSignup = document.getElementById('tab-btn-signup');

    if (tab === 'login') {
      if (loginView) loginView.classList.remove('hidden');
      if (signupView) signupView.classList.add('hidden');
      if (tabLogin) tabLogin.classList.add('active');
      if (tabSignup) tabSignup.classList.remove('active');
    } else {
      if (loginView) loginView.classList.add('hidden');
      if (signupView) signupView.classList.remove('hidden');
      if (tabLogin) tabLogin.classList.remove('active');
      if (tabSignup) tabSignup.classList.add('active');
    }

    this.clearErrors();
  },

  handleLogin(e) {
    e.preventDefault();
    this.clearErrors();

    const emailInput = document.getElementById('login-email');
    const passInput = document.getElementById('login-password');
    const rememberInput = document.getElementById('login-remember');

    const email = emailInput ? emailInput.value.trim() : '';
    const pass = passInput ? passInput.value.trim() : '';
    const remember = rememberInput ? rememberInput.checked : true;

    let hasError = false;

    if (!email) {
      this.showInputError('login-email-error', I18n.t('err_required'));
      hasError = true;
    }

    if (!pass) {
      this.showInputError('login-password-error', I18n.t('err_required'));
      hasError = true;
    } else if (pass.length < 6) {
      this.showInputError('login-password-error', I18n.t('err_password_short'));
      hasError = true;
    }

    if (hasError) return;

    // Successful Demo Authentication
    const userName = email.includes('@') ? email.split('@')[0] : email;
    const capitalizedName = userName.charAt(0).toUpperCase() + userName.slice(1);

    const session = {
      isLoggedIn: true,
      user: {
        name: capitalizedName,
        email: email.includes('@') ? email : `${email}@example.com`,
        rememberMe: remember
      },
      loginTimestamp: new Date().toISOString()
    };

    StorageService.setAuth(session);
    StorageService.saveProfile({
      name: session.user.name,
      email: session.user.email
    });

    const targetStep = (this.returnStep && this.returnStep !== 'login') ? this.returnStep : 'hub';
    this.returnStep = null;
    Onboarding.goToStep(targetStep);
  },

  handleSignup(e) {
    e.preventDefault();
    this.clearErrors();

    const nameInput = document.getElementById('signup-name');
    const emailInput = document.getElementById('signup-email');
    const passInput = document.getElementById('signup-password');
    const confirmInput = document.getElementById('signup-confirm-password');

    const name = nameInput ? nameInput.value.trim() : '';
    const email = emailInput ? emailInput.value.trim() : '';
    const pass = passInput ? passInput.value.trim() : '';
    const confirmPass = confirmInput ? confirmInput.value.trim() : '';

    let hasError = false;

    if (!name) {
      this.showInputError('signup-name-error', I18n.t('err_required'));
      hasError = true;
    }

    if (!email) {
      this.showInputError('signup-email-error', I18n.t('err_required'));
      hasError = true;
    } else if (!this.validateEmail(email)) {
      this.showInputError('signup-email-error', I18n.t('err_email_invalid'));
      hasError = true;
    }

    if (!pass) {
      this.showInputError('signup-password-error', I18n.t('err_required'));
      hasError = true;
    } else if (pass.length < 6) {
      this.showInputError('signup-password-error', I18n.t('err_password_short'));
      hasError = true;
    }

    if (!confirmPass) {
      this.showInputError('signup-confirm-error', I18n.t('err_required'));
      hasError = true;
    } else if (pass !== confirmPass) {
      this.showInputError('signup-confirm-error', I18n.t('err_password_mismatch'));
      hasError = true;
    }

    if (hasError) return;

    // Successful Signup (Preserves local progress)
    const session = {
      isLoggedIn: true,
      user: {
        name: name,
        email: email,
        rememberMe: true
      },
      loginTimestamp: new Date().toISOString()
    };

    StorageService.setAuth(session);
    StorageService.saveProfile({
      name: name,
      email: email
    });

    const targetStep = (this.returnStep && this.returnStep !== 'login') ? this.returnStep : 'hub';
    this.returnStep = null;
    Onboarding.goToStep(targetStep);
  },

  loginDemoUser() {
    const demoNames = ['Aarav Patel', 'Maya Sharma', 'Liam Vance', 'Zara Chen'];
    const chosenName = demoNames[Math.floor(Math.random() * demoNames.length)];

    const session = {
      isLoggedIn: true,
      user: {
        name: chosenName,
        email: 'demo.learner@synapse.edu',
        isDemo: true,
        rememberMe: true
      },
      loginTimestamp: new Date().toISOString()
    };

    StorageService.setAuth(session);
    StorageService.saveProfile({
      name: chosenName,
      email: session.user.email
    });

    const targetStep = (this.returnStep && this.returnStep !== 'login') ? this.returnStep : 'hub';
    this.returnStep = null;
    Onboarding.goToStep(targetStep);
  },

  handleForgotPassword() {
    const emailPrompt = prompt(I18n.t('forgot_password_prompt'), 'learner@example.com');
    if (emailPrompt) {
      alert(I18n.t('forgot_password_success'));
    }
  },

  checkPasswordStrength(password) {
    const indicator = document.getElementById('password-strength-indicator');
    const bar = document.getElementById('password-strength-bar');
    if (!indicator || !bar) return;

    if (!password) {
      indicator.textContent = '';
      bar.style.width = '0%';
      bar.className = 'strength-bar';
      return;
    }

    let strength = 0;
    if (password.length >= 6) strength++;
    if (password.length >= 9) strength++;
    if (/[A-Z]/.test(password) && /[0-9]/.test(password)) strength++;
    if (/[^A-Za-z0-9]/.test(password)) strength++;

    if (strength <= 1) {
      indicator.textContent = I18n.t('pass_strength_weak');
      indicator.style.color = 'var(--color-rose)';
      bar.style.width = '30%';
      bar.className = 'strength-bar weak';
    } else if (strength === 2) {
      indicator.textContent = I18n.t('pass_strength_medium');
      indicator.style.color = 'var(--color-warning)';
      bar.style.width = '65%';
      bar.className = 'strength-bar medium';
    } else {
      indicator.textContent = I18n.t('pass_strength_strong');
      indicator.style.color = 'var(--color-emerald)';
      bar.style.width = '100%';
      bar.className = 'strength-bar strong';
    }
  },

  validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  },

  showInputError(elementId, message) {
    const el = document.getElementById(elementId);
    if (el) {
      el.textContent = message;
      el.classList.remove('hidden');
    }
  },

  clearErrors() {
    const errorEls = document.querySelectorAll('.input-error');
    errorEls.forEach(el => {
      el.textContent = '';
      el.classList.add('hidden');
    });
  },

  logout() {
    StorageService.clearAuth();
    StorageService.saveProfile({ name: I18n.t('auth_guest_student'), email: '' });
    const current = Onboarding ? Onboarding.currentStep : 'hub';
    const targetStep = current !== 'login' ? current : 'hub';
    Onboarding.goToStep(targetStep);
  }
};

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { Auth };
}
