<template>
  <div class="login-block" id="login_form_card" :style="cssVariables">
    <div class="error">

      <!-- LOGIN -->
      <template v-if="mode === 'login'">
        <input
          v-model="user.username"
          id="username"
          autocomplete="off"
          type="text"
          placeholder="Username"
          class="form-control input-username"
          name="username"
          v-on:keyup.enter="login"
        />
        <input
          v-model="user.password"
          id="password"
          type="password"
          class="form-control input-password"
          placeholder="Password"
          name="password"
          v-on:keyup.enter="login"
        />
        <span class="has-error">
          <strong class="text-danger">{{ error_message }}</strong>
        </span>
        <button type="submit" class="btn-submit" id="login_btn" :onclick="login">
          Login
        </button>
        <div class="forgot-password">
          <a href="#" @click.prevent="showForgot">Forgot password?</a>
        </div>
      </template>

      <!-- RESET PASSWORD -->
      <template v-else-if="mode === 'reset'">
        <p class="forgot-help">Choose a new password for your account.</p>

        <div class="input-icon-wrap">
          <input
            v-model="reset.email"
            type="email"
            class="form-control input-email"
            placeholder="Email address"
            autocomplete="email"
            :readonly="reset.locked"
            :class="{ 'input-locked': reset.locked }"
          />
          <i class="fas fa-envelope field-icon"></i>
        </div>

        <input
          v-model="reset.password"
          type="password"
          class="form-control input-password"
          placeholder="New password (min. 8 characters)"
          autocomplete="new-password"
        />
        <input
          v-model="reset.password_confirmation"
          type="password"
          class="form-control input-password"
          placeholder="Confirm new password"
          autocomplete="new-password"
          v-on:keyup.enter="submitReset"
        />

        <span class="has-error"><strong class="text-danger">{{ reset.error }}</strong></span>
        <span><strong class="text-success">{{ reset.success }}</strong></span>
        <button type="button" class="btn-submit" :disabled="reset.loading" @click="submitReset">
          {{ reset.loading ? "Saving..." : "Reset Password" }}
        </button>
        <div class="forgot-password">
          <a href="#" @click.prevent="showLogin">&larr; Back to login</a>
        </div>
      </template>

      <!-- FORGOT PASSWORD -->
      <template v-else>
        <p class="forgot-help">
          Enter the email address linked to your account and we'll send you a reset link.
        </p>

        <div class="input-icon-wrap">
          <input
            v-model="forgot.email"
            type="email"
            class="form-control input-email"
            placeholder="Email address"
            autocomplete="email"
            v-on:keyup.enter="sendReset"
          />
          <i class="fas fa-envelope field-icon"></i>
        </div>

        <span class="has-error">
          <strong class="text-danger">{{ forgot.error }}</strong>
        </span>
        <span>
          <strong class="text-success">{{ forgot.success }}</strong>
        </span>
        <button
          type="button"
          class="btn-submit"
          :disabled="forgot.loading"
          @click="sendReset"
        >
          {{ forgot.loading ? "Sending..." : "Send Reset Link" }}
        </button>
        <div class="forgot-password">
          <a href="#" @click.prevent="showLogin">&larr; Back to login</a>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  name: "Credentials",
  data: () => ({
    user: {
      username: "",
      password: "",
    },
    mode: "login",
    forgot: { email: "", error: "", success: "", loading: false },
    reset: {
      token: "",
      email: "",
      locked: false,
      password: "",
      password_confirmation: "",
      error: "",
      success: "",
      loading: false,
    },
    login_type: "",
    login_link: "",
    error_message: "",
    baseUrl: window.location.origin,
  }),
  computed: {
    cssVariables() {
      if (
        this.baseUrl === "https://referral-dummy.cvchd7.com" ||
        this.baseUrl === "https://cvchd7.com"
      ) {
        return {
          "--username-icon": `url('${this.baseUrl}/resources/img/username-icon.png')`,
          "--password-icon": `url('${this.baseUrl}/resources/img/password-icon.png')`,
        };
      } else {
        return {
          "--username-icon": `url('${this.baseUrl}/referral/resources/img/username-icon.png')`,
          "--password-icon": `url('${this.baseUrl}/referral/resources/img/password-icon.png')`,
        };
      }
    },
  },
  created() {
    this.setLoginType();
    const params = new URLSearchParams(window.location.search);
    if (params.get("reset_token")) {
      this.reset.token = params.get("reset_token");
      this.reset.email = params.get("email") || "";
      this.reset.locked = !!params.get("email");
      this.mode = "reset";
      // Remove the token from the address bar
      window.history.replaceState({}, document.title, window.location.pathname);
    }
  },
  methods: {
    firstError(error) {
      const data = error.response && error.response.data;
      if (data && data.errors) {
        return Object.values(data.errors)[0][0];
      }
      if (error.response && error.response.status === 419) {
        return "Session expired. Please refresh the page and try again.";
      }
      if (error.response && error.response.status === 503 && data && data.message) {
        return data.message;
      }
      return "Something went wrong. Please try again.";
    },
    submitReset() {
      this.reset.error = "";
      this.reset.success = "";
      this.reset.loading = true;
      axios
        .post("password/reset", {
          token: this.reset.token,
          email: this.reset.email,
          password: this.reset.password,
          password_confirmation: this.reset.password_confirmation,
        })
        .then((response) => {
          this.reset.success = response.data.message;
          this.reset.loading = false;
          setTimeout(() => { this.mode = "login"; }, 2500);
        })
        .catch((error) => {
          this.reset.error = this.firstError(error);
          this.reset.loading = false;
        });
    },
    showForgot() {
      this.mode = "forgot";
      this.forgot = { email: "", error: "", success: "", loading: false };
    },
    showLogin() {
      this.mode = "login";
    },
    sendReset() {
      this.forgot.error = "";
      this.forgot.success = "";
      this.forgot.loading = true;
      axios
        .post("password/email", { email: this.forgot.email })
        .then(() => {
          this.forgot.success =
            "A password reset link has been sent. Please check your email inbox (and your spam folder).";
          this.forgot.loading = false;
        })
        .catch((error) => {
          this.forgot.error = this.firstError(error);
          this.forgot.loading = false;
        });
    },
    login() {
      $("#login_btn").html(
        '<i class="fa fa-spinner fa-spin"></i> Validating...'
      );
      axios
        .post("login", {
          username: this.user.username,
          password: this.user.password,
          login_type: this.login_type,
          login_link: this.login_link,
        })
        .then((response) => {
          if (response.data["error_notif"] === true) {
            this.error_message = response.data["error_msg"];
            $("#login_btn").html("Login");
          } else {
            this.error_message = "";
            window.location.href = response.data;
          }
        });
    },

    setLoginType() {
      let path = window.location.origin;
      if (path === "https://cvchd7.com") {
        this.login_type = "cloud";
      } else {
        this.login_type = "doh";
      }
      this.login_link = path;
    },
  },
};
</script>

<style scoped>
.input-username {
  background: #fff var(--username-icon) 20px top no-repeat !important;
  background-size: 16px 80px !important;
}

.input-username:focus {
  background: #fff var(--username-icon) 20px bottom no-repeat !important;
  background-size: 16px 80px !important;
}

.input-password {
  background: #fff var(--password-icon) 20px top no-repeat !important;
  background-size: 16px 80px !important;
}

.input-password:focus {
  background: #fff var(--password-icon) 20px bottom no-repeat !important;
  background-size: 16px 80px !important;
}

/* Email icon (Font Awesome envelope) */
.input-icon-wrap {
  position: relative;
}

.input-email {
  padding-left: 50px !important;
}

.field-icon {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 14px;
  color: #999;
  pointer-events: none;
}

.input-email:focus + .field-icon {
  color: #2a5298;
}

/* Locked email (when the link supplied it) */
.input-locked {
  background-color: #f1f3f5 !important;
  color: #6c757d;
  cursor: not-allowed;
}

.forgot-password {
  margin: 10px 0;
  text-align: center;
  font-size: 13px;
}

.forgot-help {
  font-size: 13px;
  margin-bottom: 10px;
}
</style>