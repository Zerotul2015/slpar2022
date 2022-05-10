<template>
  <div class="wholesale-register">
    <h2>{{title}}</h2>
    <div class="input-block input-block-dh">
      <label class="label" for="name">Ваше имя<span class="required">*</span>:</label>
      <input class="input" type="text" placeholder="Как к вам обращаться" v-model="regName">
    </div>
    <div class="input-block input-block-dh">
      <label class="label" for="name">Название организации<span class="required">*</span>:</label>
      <input class="input" type="text" v-model="regCompany">
    </div>
    <div class="input-block input-block-dh">
      <label class="label" for="name">Мобильный телефон<span class="required">*</span>:</label>
      <input class="input" type="text" v-mask="'+7-###-###-##-##'" placeholder="+7-923-555-55-55" v-model="regPhone">
      <div v-if="phoneUsed" class="error">
        Покупатель таким телефоном уже зарегистрирован.<br>Авторизуйтесь что бы отправить заявку.
      </div>
    </div>
    <div class="input-block input-block-dh">
      <label class="label" for="name">Почта<span class="required">*</span>:</label>
      <input class="input" type="text" v-model="regMail" placeholder="vasha@pochta.ru" :class="{'error-input':regMailError}">
      <div v-if="mailUsed" class="error">
        Покупатель такой почтой уже зарегистрирован.<br>Авторизуйтесь что бы отправить заявку.
      </div>
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
</template>

<script>
import IconSvg from "../Icon-svg/icon-svg";
import debounce from "lodash/debounce";
import apiCustomer from "../../common/apiCustomer";
export default {
  name: "CustomerWholesaleRegister",
  components: {IconSvg},
  props:{
    title:{
      type:String,
      default:'Регистрация оптового покупателя',
    }
  },
  data(){
    return{
      regName: '',
      regCompany: '',
      regPhone: '',
      regMail: '',
      regComment: '',
      phoneUsed: false,
      phoneChecked: false,
      mailUsed: false,
      mailChecked: false,
      regSendStatus: null, // 1 -  успешно, 2 - ошибка 3 - не заполнены нужные поля, 0 | null - без изменений
      regSendButtonDefault: 'отправить',
      regSendButtonSuccess: 'Ваше сообщение отправлено',
      regSendButtonError: 'ошибка сервера',
      regSendButtonErrorInput: 'заполните все поля',
    }
  },
  watch: {
    regMail(newVal) {
      this.mailChecked = false;
      if(newVal.length > 7){
        this.checkedAlreadyRegistered();
      }
    },
    regPhone(newVal) {
      this.phoneChecked = false;
      if(newVal.length > 7){
        this.checkedAlreadyRegistered();
      }
    },
    regSendStatus() {
    },
    requestRegisterSend(newVal) {
      if (newVal === false) {
        this.regSendStatus = 2;
      }
      if (newVal === true) {
        this.regSendStatus = 1;
      }
    }
  },
  computed: {
    phoneCheckIcon() {
      let iconName = '';
      if (this.phoneChecked) {
        iconName = 'check';
        if (this.phoneUsed) {
          iconName = 'xmark';
        }
      }
      return iconName;
    },
    mailCheckIcon() {
      let iconName = '';
      if (this.mailChecked) {
        iconName = 'check';
        if (this.mailUsed) {
          iconName = 'xmark';
        }
      }
      return iconName;
    },
    regButtonActive: function () {
      if (this.regFormChecked) {
        this.regSendStatus = 0;
        return true
      } else {
        this.regSendStatus = 3;
        return false;
      }
    },
    regIconButton: function () {
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
    regTextButton: function () {
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
    requestRegisterSend() {
      return this.$store.getters['customer/requestRegisterWholesaleSend'];
    },
    regMailError() {
      return this.regMail.length > 7 && this.$root.validateMail(this.regMail);
    },
    regFormChecked() {
      return !this.mailUsed && !this.phoneUsed && this.regName.length > 1 && this.regCompany.length > 2 && this.regPhone.length > 5 && this.regMail.length > 7;
    },
  },
  methods: {
    checkedAlreadyRegistered: debounce(async function () {
      let sendData = {
        'mail': this.regMail,
        'phone': this.regPhone,
      };
      let that = this;
      apiCustomer.action('checkAlreadyRegistered', sendData).then(r=>{
        if (r.result === true) {
          console.log(r);
          that.mailChecked = true;
          that.mailUsed = r.returnData['mailUsed'] ? r.returnData['mailUsed'] :false;
          that.phoneChecked = true;
          that.phoneUsed = r.returnData['phoneUsed'] ? r.returnData['phoneUsed'] : false;
        } else {
          if (r.error) {

          }
        }
      })
          .catch()
    }, 1500),
    regSendForm() {
      let formData = {
        'name': this.regName,
        'phone': this.regPhone,
        'mail': this.regMail,
        'company': this.regCompany,
        'comment': this.regComment,
      }
      this.$store.dispatch('customer/registerRequestWholesale', formData);
    },
  }
}
</script>

<style scoped>

</style>