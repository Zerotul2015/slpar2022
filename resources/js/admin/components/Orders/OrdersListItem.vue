<template>
  <div class="order-list-item grid-col-3">
    <div>{{order.date}}</div>
    <div>{{orderStatus}}</div>
    <div>
      <router-link :to="'/orders/details/' + order.id" class="button button_green">подробнее</router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: "OrdersListItem",
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  beforeMount(){
    this.$store.dispatch("customers/getCustomerById");
  },
  computed:{
    customer(){
      return this.$store.getters.customers.byId(this.order.customer_id);
    },
    orderStatus(){
      return this.$store.getters["ordersStatus/byId"](this.order.status_id);
    }
  }
}
</script>

<style scoped>

</style>