<template>
  <div class="dealer-page">
    <breadcrumb></breadcrumb>
    <div class="dealer-home">
      <h1 v-html="titlePage"></h1>
      <div class="dh-profile" v-if="isAuth">
        <h2>Профиль покупателя</h2>
        <div class="dh-profile">

        </div>
      </div>
      <div class="dh-login-register" v-else>
        <div class="dh-login">
          <h2>Вход в кабинет</h2>
          <div class="input-block input-block-dh">
            <label class="label" for="login">Почта:</label>
            <input id="login" class="input" type="email" v-model="enterLogin">
          </div>
          <div class="input-block input-block-dh">
            <label class="label" for="password">Пароль:</label>
            <input id="password" class="input" type="password" v-model="enterPass">
          </div>
          <div class="block-button block-button-dh">
            <button class="btn" :disabled="!enterButtonActive" @click="enterSendForm">
              <icon-svg class="btn-icon" :icon="enterIconButton"></icon-svg>
              <span class="btn-text" v-html="enterTextButton"></span>
            </button>
          </div>
        </div>
        <div class="dh-register">
          <h2>Заявка на регистрацию</h2>
          <div class="input-block input-block-dh">
            <label class="label" for="name">Ваше имя<span class="required">*</span>:</label>
            <input class="input" type="text" v-model="regName">
          </div>
          <div class="input-block input-block-dh">
            <label class="label" for="name">Название организации<span class="required">*</span>:</label>
            <input class="input" type="text" v-model="regCompany">
          </div>
          <div class="input-block input-block-dh">
            <label class="label" for="name">Телефон<span class="required">*</span>:</label>
            <input class="input" type="text" v-model="regPhone">
          </div>
          <div class="input-block input-block-dh">
            <label class="label" for="name">Почта<span class="required">*</span>:</label>
            <input class="input" type="text" v-model="regMail" :class="{'error-input':regMailError}">
          </div>
          <div class="input-block input-block-dh">
            <label class="label" for="name">Сообщение:</label>
            <textarea class="textarea" v-model="regComment"></textarea>
          </div>
          <div class="block-button block-button-dh">
            <button class="btn" :disabled="!regButtonActive" @click="regSendForm">
              <icon-svg class="btn-icon" :icon="regIconButton"></icon-svg>
             <span class="btn-text" v-html="regTextButton"></span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Breadcrumb from "../Breadcrumb";
import IconSvg from "../Icon-svg/icon-svg";

export default {
  name: "DealerHomePage",
  components: {IconSvg, Breadcrumb},
  data(){
    return {
      regName:'',
      regCompany:'',
      regPhone:'',
      regMail:'',
      regComment:'',
      regSendStatus: null, // 1 -  успешно, 2 - ошибка 3 - не заполнены нужные поля, 0 | null - без изменений
      regSendButtonDefault: 'отправить',
      regSendButtonSuccess: 'Ваше сообщение отправлено',
      regSendButtonError: 'ошибка сервера',
      regSendButtonErrorInput: 'заполните все поля',
      enterLogin:'',
      enterPass:'',
      enterSendStatus:null, // 1 -  успешно, 2 - ошибка 3 - не заполнены нужные поля, 0 | null - без изменений
      enterSendButtonDefault: 'войти',
      enterSendButtonSuccess: 'вход выполнен',
      enterSendButtonError: 'не удалось войти',
      enterSendButtonErrorInput: 'введите почту и пароль',
    }
  },
  watch:{
    regSendStatus(){},
    requestRegisterSend(newVal){
      if(newVal === false){
        this.regSendStatus = 2;
      }
      if(newVal === true){
        this.regSendStatus = 1;
      }
    }
  },
  computed: {
    customer(){
      return this.$store.getters['customer/customer'];
    },
    wholesale(){
      return this.$store.getters['customer/wholesale'];
    },
    enterButtonActive(){
      if(this.enterLogin.length > 6 && this.$root.validateMail(this.enterLogin) && this.enterPass.length > 2){
        this.enterSendStatus = 0;
        return true;
      }else{
        this.enterSendStatus = 3;
        return false;
      }
    },
    enterIconButton:function () {
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
    enterTextButton:function () {
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
    regButtonActive:function(){
      if(this.regFormChecked){
        this.regSendStatus = 0;
        return true
      }else{
        this.regSendStatus = 3;
        return false;
      }
    },
    regIconButton:function () {
      if (this.regSendStatus === 1) {
        return 'check';
      }
      if (this.regSendStatus === 2) {
        return 'xmark';
      }
      if (this.regSendStatus === 3) {
        return '';
      }
      if (this.regSendStatus === 0 || this.regSendStatus === null) {
        return 'send';
      }
    },
    regTextButton:function () {
      if (this.regSendStatus === 1 || this.requestRegisterSend === true) {
        return this.regSendButtonSuccess;
      }
      if (this.regSendStatus === 2) {
        return this.regSendButtonError;
      }
      if (this.regSendStatus === 3) {
        return this.regSendButtonErrorInput;
      }
      if (this.regSendStatus === 0 || this.regSendStatus === null) {
        return this.regSendButtonDefault;
      }
    },
    requestRegisterSend(){
      return this.$store.getters.requestRegisterSend;
    },
    regMailError(){
      return this.regMail.length > 7 && this.$root.validateMail(this.regMail);
    },
    regFormChecked(){
      return this.regName.length >1 && this.regCompany.length >2 && this.regPhone.length >5 && this.regMail.length >7;
    },
    titlePage() {
      let text = 'Кабинет дилера'
      return text;
    },
    isAuth() {
      return this.$store.getters['customer/isAuth'];
    }
  },
  methods:{
    regSendForm(){
      let formData ={
        'name':this.regName,
        'phone':this.regPhone,
        'mail':this.regMail,
        'company':this.regCompany,
        'comment':this.regComment,
      }
      this.$store.dispatch('customer/registerRequest',formData);
    },
    enterSendForm(){
      let formData ={
        'login':this.enterLogin,
        'pass':this.enterPass
      }
      this.$store.dispatch('customer/auth',formData);
    }
  }
}
</script>

<style scoped>

</style>