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
    <div class="recovery-pass-block" @click="recoveryPassword">
      <div class="recovery-pass-link" v-html="recoveryButtonText"></div>
    </div>
    <div class="error" v-if="authResult === false">Нет такого пользователя или ошибка в пароле</div>
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
import {isNull} from "lodash";

export default {
  name: "CustomerLogin",
  components: {IconSvg},
  props: {
    title: {
      type: String,
      default: 'Авторизация',
    }
  },
  data() {
    return {
      enterLogin: '',
      enterPass: '',
      enterSendStatus: null, // 0 | null - без изменений, 1 -  успешно, 2 - ошибка 3 - не заполнены нужные поля
      enterSendButtonDefault: 'войти',
      enterSendButtonSuccess: 'вход выполнен',
      enterSendButtonError: 'не удалось войти',
      enterSendButtonErrorInput: 'введите почту и пароль',
      recoveryPassClick: null, // bool
    }
  },
  watch: {
    recoveryStatus(newVal) {
      if(!isNull(newVal)){
        setTimeout(function(){this.recoveryStatus = null}.bind(this),5000);
      }
    },
    recoveryPassClick(newVal) {
      if(!isNull(newVal)){
        setTimeout(function(){this.recoveryPassClick = null}.bind(this),5000);
      }
    },
    authResult(newVal){
      if(newVal === false){
        this.enterSendStatus = 2;
      }
    },
    enterSendStatus(newVal){
      if(!isNull(newVal)){
        setTimeout(function(){this.enterSendStatus = null}.bind(this),5000);
      }
    }
  },
  computed: {
    authResult(){
      return this.$store.getters['customer/authResult'];
    },
    inputMailState(newVal) {
      let stateInput = null;
      if (this.enterLogin.length === 0) {
        stateInput = null;
      } else {
        stateInput = !!this.$root.validateMail(this.enterLogin);
      }
      return stateInput
    },
    recoveryPasswordResult(){
      return this.$store.getters['customer/recoveryPasswordResult'];
    },
    recoveryButtonText() {
      let text = 'Восстановить пароль';
      if (this.recoveryPasswordResult === true) {
        text = 'Новый пароль выслан на указанную почту';
      }
      if(this.recoveryPasswordResult ===false){
        text = 'Новый пароль выслан на указанную почту'; // даже при ошибке пишем что отправили.
      }
      if(this.recoveryPassClick && this.inputMailState === false){
        text = 'Сначала введите почту!';
      }
      return text;
    },
    enterButtonActive() {
      if (this.enterLogin.length > 6 && this.inputMailState && this.enterPass.length > 2) {
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
        return 'right-to-bracket';
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
  methods: {
    sendForm() {
      let formData = {
        'login': this.enterLogin,
        'pass': this.enterPass
      }
      this.$store.dispatch('customer/auth', formData);
    },
    recoveryPassword(){
      this.recoveryPassClick = true;
      if(this.inputMailState){
        this.$store.dispatch('customer/recoveryPassword', this.enterLogin);
      }
    }
  }
}
</script>

<style scoped>

</style>