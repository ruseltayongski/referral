<template>
  <div class="login-block" id="login_form_card" :style="cssVariables">
    <div class="error">
      <input
        v-model="user.username"
        id="username"
        autocomplete="off"
        type="text"
        :placeholder="'Username'"
        class="form-control input-username"
        name="username"
        v-on:keyup.enter="login"
      />
      <input
        v-model="user.password"
        id="password"
        type="password"
        class="form-control input-password"
        :placeholder="'Password'"
        name="password"
        v-on:keyup.enter="login"
      />
      <span class="has-error">
        <strong class="text-danger">{{ error_message }}</strong>
      </span>
      <button type="submit" class="btn-submit" id="login_btn" :onclick="login">
        Login
      </button>
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
    login_type: "",
    login_link: "",
    error_message: "",
    baseUrl: window.location.origin,
  }),
  computed: {
    cssVariables() {
        if (this.baseUrl != "https://cvchd7.com" || this.baseUrl != "https://referral-dummy.cvchd7.com"){
            return { 
                "--username-icon": `url('${this.baseUrl}/referral/resources/img/username-icon.png')`,
                "--password-icon": `url('${this.baseUrl}/referral/resources/img/password-icon.png')`,
            };
        }else {
            return { 
                "--username-icon": `url('${this.baseUrl}/resources/img/username-icon.png')`,
                "--password-icon": `url('${this.baseUrl}/resources/img/password-icon.png')`,
            };
        }
    },
  },
  created() {
    this.setLoginType();
  },
  methods: {
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
</style>
