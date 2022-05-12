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
      <customer-register></customer-register>
      <div class="wholesale-register" v-if="requestRegisterWholesaleResult">
        Ваша заявка на регистрацию в качестве оптового покупателя успешно отправлена.
      </div>
      <customer-wholesale-register v-else></customer-wholesale-register>

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
    requestRegisterWholesaleResult(){
      return this.$store.getters['customer/requestRegisterWholesaleResult'];
    },
    registerResult() {
      return this.$store.getters['customer/registerResult'];
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