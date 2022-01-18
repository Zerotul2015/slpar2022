<template>
  <div class="wrapper-content">
    <h1>Настройки баннеров</h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <div class="buttons-block">
      <button class="button" @click="addBanner"><i class="far fa-plus"></i> добавить</button>
    </div>
    <div v-if="banners" class="content-block">
      <settingsBannersItem v-for="(bannerItem, bannerID, bannerKey) in banners"
                           v-bind:key="$root.guid()"
                           v-bind:index-key="$root.guid()"
                           v-bind:banner-item="bannerItem"
                           v-on:remove-banner="banners.splice(bannerKey,1)"
                           v-on:banner-changed="bannerChanged($event, bannerKey)">
      </settingsBannersItem>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import api from '../../common/api'
import settingsBannersItem from "./settingsBannersItem.vue"

export default {
  name: "settingsBanners",
  components: {
    settingsBannersItem
  },
  data() {
    return {
      loading: false,
      error: null,
      banners: null
    }
  },
  created() {
    this.fetchData();
  },
  methods: {
    fetchData() {
      api.getData('banners', {})
          .then( (r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.banners = r.returnData;
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e;
          })
    },
    addBanner: function () {
      let newSlide = {
        href: '',
        image: '',
        description: ''
      };
      let indexForBanner = Math.random().toString(36).substr(2, 9);
      this.banners.push(newSlide);
    },
    removeBanner: function (index) {
      this.$delete(this.banners, index);
    }
  }
}
</script>

<style scoped>

</style>