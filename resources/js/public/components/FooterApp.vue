<template>
  <div class="footer-wrap">
    <footer class="body-footer">
      <img class="bf-title" src="/build/images/fooote_title.png"
           alt="Оригинальные аксессуары для бань и саун с wow-еффектом">

      <img class="bf-logo" src="/build/images/logo_footer.svg" alt="С лёгким паром! Торопиться не надо.">

      <div class="bf-column-1" v-if="footerData[0] && footerData[0].type==='html'" v-html="footerData[0].values"></div>
      <div class="bf-column-1" v-if="footerData[0] && footerData[0].type==='pages'">
        <router-link class="link link_bf" v-for="(page) in footerData[0].values" :key="$root.guid()" :to="page.url">
          {{ page.title }}
        </router-link>
      </div>
      <div class="bf-column-2" v-if="footerData[1] && footerData[1].type==='html'" v-html="footerData[1].values"></div>
      <div class="bf-column-2" v-if="footerData[1] && footerData[1].type==='pages'">
        <router-link class="link link_bf" v-for="(page) in footerData[1].values" :key="$root.guid()" :to="page.url">
          {{ page.title }}
        </router-link>
      </div>
      <div class="bf-column-3">
        <div class="link link_bf">Обратный звонок</div>
        <div class="link link_bf">Задать вопрос</div>
      </div>
      <div class="bf-column-2-end">
        <div>Следите за нами</div>
        <div>
          <a class="bf-link-icon" href="httsp://instagram.com" target="_blank" alt="Мы в Инстаграмм*">
            <icon-svg icon="instagram" scale-x="2" scale-y="2"></icon-svg>
          </a>
        </div>
      </div>
      <div class="bf-column-3-end"></div>
      <div class="bf-copyright">
        <div class="bf-c-1">
          *Instagram — принадлежит американской компании Meta, которую признали экстремистской, запрещён на территории
          РФ.* *Meta — материнская компания Facebook, WhatsApp и Instagram. Признана экстремистской и запрещена на
          территории РФ.
        </div>
        <div class="bf-c-1">
          Вся информация, представленная на сайте, носит информационный характер и не является публичной офертой,
          определяемой положениями Статьи 437 (2) Гражданского кодекса РФ.
        </div>
        <div class="bf-c-2>">

        </div>
        <div class="bf-c-3">

        </div>
      </div>
    </footer>
  </div>
</template>

<script>
import IconSvg from "./Icon-svg/icon-svg";

export default {
  name: "FooterApp",
  components: {IconSvg},
  computed: {
    footerData() {
      return this.$store.getters['templateData/footer'];
    }
  }
}
</script>

<style scoped>

</style>