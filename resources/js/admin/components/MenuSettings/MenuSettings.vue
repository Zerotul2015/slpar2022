<template>
  <div class="content-block">
    <h1>Меню</h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="buttons-block">
      <button class="button" @click="menuCreate">
        <span class="button-icon"><i class="far fa-plus"></i></span>
        <span  class="button-icon">Создать меню</span>
      </button>
    </div>
    <MenuSettingsMenu v-for="(menu, menuKey) in fetchedData"
                        :key="$root.guid()"
                        :menu="menu"
                        v-on:remove-menu="$delete(fetchedData, menuKey)"
    ></MenuSettingsMenu>
  </div>
</template>

<script>
import axios from "axios";
import api from "../../common/api";
import MenuSettingsMenu from "./MenuSettingsMenu.vue";

export default {
  name: "MenuSettings",
  components: {
    MenuSettingsMenu
  },
  data() {
    return {
      loading: false,
      error: null,
      fetchedData: [],

    }
  },
  mounted() {
    this.fetchData();
    this.$store.dispatch('page/getAllById');
    this.$store.dispatch('pageCategory/getAllById');
  },
  watch: {
    fetchedData: {
      deep: true,
      handler: function(newVal) {
        this.fetchedData = newVal;
      }
    }
  },
  computed: {},
  methods: {
    fetchData: async function () {
      this.loading = true;
      api.getData('menu', {})
          .then((r) => {
            this.loading = false;
            if(r.result && r.result === true) {
              this.fetchedData = r.returnData;
            }else{
              this.error = r.error ? r.error : 'неизвестная ошибка при получении данных'
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e;
          })
    },
    menuCreate: function () {
      let menuNew = {
        'name': 'Новое меню',
        'position': 'header',
        'items': null,
        'priority': 0,
        'enable': true,
      }
      if (!Object.keys(this.fetchedData).length > 0) {
        this.fetchedData = [];
      }
      this.fetchedData.push(menuNew);
    }
  }
}
</script>

<style scoped>

</style>