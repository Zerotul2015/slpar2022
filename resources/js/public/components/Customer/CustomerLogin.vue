<template>
  <div class="customer-login">
    <h2>{{ title }}</h2>
    <div class="input-block input-block-dh">
      <label class="label" for="login">Почта:</label>
      <input id="login" class="input" type="email" v-model="enterLogin">
    </div>
    <div class="input-block input-block-dh">
      <label class="label" for="password">Пароль:</label>
      <input id="password" class="input" type="password" v-model="enterPass">
    </div>
    <div class="block-button block-button-dh">
      <button class="btn" :disabled="!enterButtonActive" @click="sendForm">
        <icon-svg class="btn-icon" :icon="enterIconButton"></icon-svg>
        <span class="btn-text" v-html="enterTextButton"></span>
      </button>
    </div>
  </div>
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";

export default {
  name: "CustomerLogin",
  components: {IconSvg},
  props:{
    title:{
      type:String,
      default:'Авторизация',
    }
  },
  data(){
    return {
      enterLogin: '',
      enterPass: '',
      enterSendStatus: null, // 0 | null - без изменений, 1 -  успешно, 2 - ошибка 3 - не заполнены нужные поля
      enterSendButtonDefault: 'войти',
      enterSendButtonSuccess: 'вход выполнен',
      enterSendButtonError: 'не удалось войти',
      enterSendButtonErrorInput: 'введите почту и пароль',
    }
  },
  computed:{
    enterButtonActive() {
      if (this.enterLogin.length > 6 && this.$root.validateMail(this.enterLogin) && this.enterPass.length > 2) {
        this.enterSendStatus = 0;
        return true;
      } else {
        this.enterSendStatus = 3;
        return false;
      }
    },
    enterIconButton: function () {
      if (this.enterSendStatus === 1) {
        return 'check';
      }
      if (this.enterSendStatus === 2) {
        return 'xmark';
      }
      if (this.enterSendStatus === 3) {
        return '';
      }
      if (this.enterSendStatus === 0 || this.enterSendStatus === null) {
        return 'send';
      }
    },
    enterTextButton: function () {
      if (this.enterSendStatus === 1) {
        return this.enterSendButtonSuccess;
      }
      if (this.enterSendStatus === 2) {
        return this.enterSendButtonError;
      }
      if (this.enterSendStatus === 3) {
        return this.enterSendButtonErrorInput;
      }
      if (this.enterSendStatus === 0 || this.enterSendStatus === null) {
        return this.enterSendButtonDefault;
      }
    },
  },
  methods:{
    sendForm() {
      let formData = {
        'login': this.enterLogin,
        'pass': this.enterPass
      }
      this.$store.dispatch('customer/auth', formData);
    }
  }
}
</script>

<style scoped>

</style>