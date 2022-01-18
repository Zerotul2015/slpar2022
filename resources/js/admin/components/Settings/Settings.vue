<template>
  <div class="wrapper-content">
    <h1>Настройки</h1>
    <div v-if="loading" class="loading">
      Загрузка...
    </div>
    <div v-if="error" class="error">
      {{ error }}
    </div>
    <section class="content-block">
      <h2>Seo заголовки</h2>
      <div class="form-section form-section_column">
        <div class="input-block  input-block_column input-block_highlight">
          <label for="prefix">Префикс заголовка:</label>
          <input id="prefix" type="text" class="input" v-model="fetchedData.title_prefix">
        </div>
        <div class="input-block  input-block_column input-block_highlight">
          <label for="postfix">Конец заголовка:</label>
          <input id="postfix" type="text" class="input" v-model="fetchedData.title_postfix">
        </div>
        <div class="input-block  input-block_column input-block_highlight">
          <label for="prefix-product">Префикс заголовка для товара:</label>
          <input id="prefix-product" class="input" type="text" v-model="fetchedData.title_prefix_product">
        </div>
        <div class="input-block  input-block_column input-block_highlight">
          <label for="postfix-product">Конец заголовка для товара:</label>
          <input id="postfix-product" class="input" type="text" v-model="fetchedData.title_postfix_product">
        </div>
      </div>
    </section>
<!--    <section class="content-block">-->
<!--      <h2>Внешний вид, шаблон</h2>-->
<!--      <div class="form-section form-section_column">-->
<!--        <div class="input-block  input-block_column input-block_highlight">-->
<!--          <div>Изображение для шапки</div>-->
<!--          <img class="image-preview" :src="imageHeader" alt="Изображение шапки">-->
<!--          <upload-image v-model="fetchedData.image_header"></upload-image>-->
<!--        </div>-->
<!--        <div class="input-block  input-block_column input-block_highlight">-->
<!--          <div>Логотип и текст</div>-->
<!--          <input class="input" type="text" v-model="fetchedData.logo_text">-->
<!--          <br>-->
<!--          <img class="image-preview" :src="imageLogo" alt="Логотип">-->
<!--          <upload-image v-model="fetchedData.image_logo"></upload-image>-->
<!--        </div>-->
<!--      </div>-->
<!--    </section>-->
    <section class="content-block">
      <h2>Подвал сайта</h2>
      <div class="grid grid-col-3">
        <div class="footer-column" v-for="(footerColumn, keyCol) in fetchedData.template_footer">
          <div class="input-block input-block_highlight">
            <label :for="'footer-col-' + keyCol">
              Тип:
            </label>
            <select @change="changeTypeFooterColumn(keyCol)" class="select" :id="'footer-col-' + keyCol"
                    v-model="fetchedData.template_footer[keyCol].type">
              <option v-for="(nameType, valType) in typeFooterColumn" :value="valType">
                {{ nameType }}
              </option>
            </select>
          </div>
          <div class="footer-column-items form-section" v-if="footerColumn.type==='pages'">
            <div class="input-block">
              <label>Добавить страницу</label>
              <select v-model="addedPageFooterColumn[keyCol]" class="select">
                <option v-for="(page, pageKey) in pages" v-if="!footerColumn.values[page.id]"
                        :value="page.id">
                  {{ page.title }}
                </option>
              </select>
            </div>
            <SettingsFooterPage v-for="(pageId, pageKey) in footerColumn.values"
                                :key="$root.guid()"
                                :col-key="keyCol"
                                :page-id="pageId"
                                @remove-page="removePageFooterColumn"
            />
          </div>
          <div class="footer-column-items form-section" v-if="footerColumn.type==='html'">
            <SettingsFooterHtml :key="$root.guid()"
                                :col-key="keyCol"
                                :contentHtml="footerColumn.values"
                                @change-html="changeHtmlFooterColumn"
            />
          </div>
        </div>
      </div>
    </section>
    <section class="content-block">
      <h2>Прочее</h2>
      <div class="form-section form-section_column">
        <div class="input-block  input-block_highlight">
          <div>Режим обслуживания</div>
          <div class="ml-1">
            <VueToggles @click="maintenanceMode = !maintenanceMode"
                        :value="maintenanceMode"
                        checkedText="вкл"
                        uncheckedText="откл"
                        checkedBg="#11994B"
            />
          </div>
        </div>
      </div>
    </section>
    <div class="buttons-block">
      <button class="button" v-html="saveButtonText" @click="saveSettings"></button>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import api from "../../common/api";
import VueToggles from "vue-toggles";
import uploadImage from "../uploaders/upload-image.vue"
import SettingsFooterHtml from "./SettingsFooterHtml.vue";
import SettingsFooterPage from "./SettingsFooterPage.vue";

export default {
  name: "Settings",
  components: {
    VueToggles,
    uploadImage,
    SettingsFooterHtml,
    SettingsFooterPage
  },
  data() {
    return {
      loading: false,
      error: null,
      fetchedData: {},
      typeFooterColumn: {
        'html': 'Произвольный html',
        'pages': 'Страницы',
      },
      addedPageFooterColumn: [null, null, null],
      maintenanceMode: false,
      //start save, delete
      saveStatus: null, // 1 -  успешно, 2 - ошибка, 0 | null - без изменений
      saveButtonDefault: '<span class="button-icon"><i class="far fa-save"></i></span><span class="button-icon">сохарнить</span>',
      saveButtonSuccess: '<span class="button-icon"><i class="far fa-check"></i></span><span class="button-icon">изменения записаны</span>',
      saveButtonError: '<span class="button-icon"><i class="far fa-times"></i></span><span class="button-icon">ошибка при сохранении</span>',
      //end save, delete
    }
  },
  mounted() {
    this.$store.dispatch('page/getAllById');
    this.fetchData();
  },
  watch: {
    addedPageFooterColumn: {
      deep: true,
      handler(newVal) {
        if (newVal[0]) {
          if (Object.keys(this.fetchedData.template_footer[0].values).length < 1) {
            this.fetchedData.template_footer[0].values = {};
          }
          this.fetchedData.template_footer[0].values[newVal[0]] = newVal[0];
        }
        if (newVal[1]) {
          if (Object.keys(this.fetchedData.template_footer[1].values).length < 1) {
            this.fetchedData.template_footer[1].values = {};
          }
          this.fetchedData.template_footer[1].values[newVal[1]] = newVal[1];
        }
        if (newVal[2]) {
          if (Object.keys(this.fetchedData.template_footer[2].values).length < 1) {
            this.fetchedData.template_footer[2].values = {};
          }
          this.fetchedData.template_footer[2].values[newVal[2]] = newVal[2];
        }
      }
    },
    fetchedData: {
      deep: true,
      handler(newVal) {

      }
    },
    maintenanceMode(newVal) {
      this.fetchedData.maintenance_mode = newVal;
    },
    //start save, delete
    saveStatus: function (newVal) {
      if (newVal) {
        setTimeout(() => {
          this.saveStatus = null
        }, 2500);
      }
    },
    //end save, delete
  },
  computed: {
    pages() {
      return this.$store.state.page.allById;
    },
    imageHeader() {
      let returnUrl = '/build/images/noimg.png';
      if (this.fetchedData.image_header) {
        if (this.fetchedData.image_header.match(/\/upload\/temp/g)) {
          returnUrl = this.fetchedData.image_header;
        } else {
          returnUrl = '/images/template/header_background/' + this.fetchedData.image_header;
        }
      }
      return returnUrl;
    },
    imageLogo() {
      let returnUrl = '/build/images/noimg.png';
      if (this.fetchedData.image_logo) {
        if (this.fetchedData.image_logo.match(/\/upload\/temp/g)) {
          returnUrl = this.fetchedData.image_logo;
        } else {
          returnUrl = '/images/template/logo_default/' + this.fetchedData.image_logo;
        }
      }
      return returnUrl;
    },
    //start save, delete
    saveButtonText() {
      if (this.saveStatus === 1) {
        return this.saveButtonSuccess;
      }
      if (this.saveStatus === 2) {
        return this.saveButtonError;
      }
      if (this.saveStatus === 0 || this.saveStatus === null) {
        return this.saveButtonDefault;
      }
    },
    //end save, delete
  },
  methods: {
    removePageFooterColumn(keyColumn, keyPage) {
      this.$delete(this.fetchedData.template_footer[keyColumn].values, keyPage);
    },
    changeTypeFooterColumn(keyColumn) {
      if (this.fetchedData.template_footer[keyColumn].type === 'html') {
        this.fetchedData.template_footer[keyColumn].values = '';
      } else {
        this.fetchedData.template_footer[keyColumn].values = {};
      }
    },
    changeHtmlFooterColumn(keyColumn, newHtml) {
      console.log(keyColumn);
      console.log(newHtml);
      if (this.fetchedData.template_footer[keyColumn].type === 'html') {
        this.fetchedData.template_footer[keyColumn].values = newHtml;
      }
    },
    fetchData: async function () {
      await api.getData('settings', {})
          .then((r) => {
            this.loading = false;
            if (r.result && r.result === true) {
              this.fetchedData = r.returnData;
              this.maintenanceMode = !!r.returnData.maintenance_mode
            }
          })
          .catch((e) => {
            this.loading = false;
            this.error = e;
          })
    },
    async saveSettings() {
      await api.applyData('settings', 'save', this.fetchedData)
          .then((r) => {
            if (r.result && r.result === true) {
              this.saveStatus = 1;
              this.fetchedData = r.returnData;
            } else {
              this.saveStatus = 2;
            }
          })
          .catch((e) => {
            this.saveStatus = 2;
            this.error = e;
          });
    }
  }
}
</script>

<style scoped>

</style>