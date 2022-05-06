<template>
  <div>
    <h2 v-html="titleText"></h2>
    <div v-if="!orders || orders.length <1">
      Покупатель еще ничего не заказывал.
    </div>
    <div v-for="(orderItem) in orders" v-else>
      <orders-list-item :order="orderItem"></orders-list-item>
    </div>
  </div>
</template>

<script>
//TODO Написать компонент отображения заказов
import OrdersListItem from "../Orders/OrdersListItem";
export default {
  name: "CustomersOrdersList",
  components: {OrdersListItem},
  props:{
    customerId:{
      required:true,
    },
    allOrders:{
      type:Boolean,
      default:false,
    }
  },
  computed:{
    titleText(){
      let text = 'Последние 10 заказов';
      if(this.allOrders === true){
        text ='Заказы'
      }
      return text;
    },
    orders(){
      return this.$store.getters['customer/orders'];
    },
  },
}
</script>

<style scoped>

</style>