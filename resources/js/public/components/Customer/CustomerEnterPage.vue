<template>
  <div class="customer-page">
    <h1 v-html="titlePage"></h1>
    <div class="customer-profile" v-if="isAuth">
      <div class="customer-profile-data">
        <h2>Ваши данные</h2>
        <customer-profile></customer-profile>
      </div>
      <div class="customer-wholesale-data" v-if="isWholesale">
        <h2>Данные для оптовых покупок</h2>
        <customer-wholesale-profile></customer-wholesale-profile>
      </div>
    </div>
    <div class="customer-login-register" v-else>
      <customer-login></customer-login>
      <customer-register v-if="!requestRegisterSend"></customer-register>
      <div class="customer-register" v-else>
        Поздравляем с успешной регистрацией. Теперь вы можете войти в личный кабинет.
      </div>
      <customer-wholesale-register v-if="!requestRegisterWholesaleSend"></customer-wholesale-register>
      <div class="wholesale-register" v-else>
        Ваша заявка на регистрацию в качестве оптового покупателя успешно отправлена.
      </div>
    </div>
  </div>
</template>
<script>
import IconSvg from "../Icon-svg/icon-svg";
import CustomerLogin from "./CustomerLogin";
import CustomerWholesaleRegister from "./CustomerWholesaleRegister";
import CustomerProfile from "./CustomerProfile";
import CustomerWholesaleProfile from "./CustomerWholesaleProfile";
import CustomerRegister from "./CustomerRegister";

export default {
  name: "CustomerEnterPage",
  components: {
    CustomerRegister,
    CustomerWholesaleProfile, CustomerProfile, CustomerWholesaleRegister, CustomerLogin, IconSvg},
  data() {
    return {}
  },
  computed: {
    titlePage() {
      let text = 'Кабинет пользователя';
      if (this.isWholesale) {
        text = 'Кабинет оптового покупателя';
      } else {
        if (this.isAuth) {
          text = 'Кабинет покупателя';
        }
      }
      return text;
    },
    requestRegisterWholesaleSend(){
      return this.$store.getters['customer/requestRegisterWholesaleSend'];
    },
    requestRegisterSend(){
      return this.$store.getters['customer/requestRegisterSend'];
    },
    isAuth() {
      return this.$store.getters['customer/isAuth'];
    },
    isWholesale() {
      return this.$store.getters['customer/isWholesale'];
    }
  }


}
</script>

<style scoped>

</style>