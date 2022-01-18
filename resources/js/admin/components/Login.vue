<template>
  <div class="login-form">
    <h1>Авторизация</h1>
    <div class="form-section">
      <div class="input-block">
        <label for="login">Логин:</label>
        <input id="login" class="input" type="text" v-model="login" @keypress.enter="loginStart">
      </div>
      <div class="input-block">
        <label for="pass">Пароль:</label>
        <input id="pass" class="input" type="password" v-model="password" @keypress.enter="loginStart">
      </div>
      <div>
        <button class="button button_small" v-html="textButtonLogin" @click="loginStart"
                :disabled="!formChecked"></button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import store from "../store";

export default {
  name: "Login",
  data: function () {
    return {
      login: '',
      password: '',
      resultAuth: null,
    }
  },
  watch: {},
  computed: {
    formChecked() {
      return this.login.length > 3 && this.password.length > 5
    },
    textButtonLogin() {
      let textButton = 'войти';
      if (this.resultAuth !== null) {
        if (this.resultAuth === true) {
          let textButton = 'вход выполнен';
        } else {
          let textButton = 'не удалось войти';
        }
      }
      return textButton;
    }
  },
  methods: {
    async loginStart() {
      await axios.post('/admin/api/authorization', {'action': 'login', 'login': this.login, 'password': this.password})
          .then((r) => {
            if (r.data.result && r.data.result === true) {
              this.$store.commit('authChange',
                  {'isAuth': r.data.isAuth, 'accessLevel': r.data.accessLevel, 'tokenAuth': r.data.tokenAuth,})
              if (r.data.isAuth === true) {
                this.$router.push('Home');
              }
            } else {
              this.resultAuth = false
            }
          })
          .catch();
    }
  }
}
</script>

<style scoped>

</style>