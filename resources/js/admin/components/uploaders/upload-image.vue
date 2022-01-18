<template>
  <div class="input-element input-element_no-label">
    <input class="input input_hidden" :ref="'files'" type="file" @change="handleFilesUpload">
    <button @click="addFiles()" class="button button_small button_white">
      <span data-v-063ef5c6="" class="button-icon"><i class="far fa-folder-open"></i></span>
      <span data-v-063ef5c6="" class="button-text">{{ textButton }}</span>
    </button>
    <div class="color-red" v-if="errorSize" v-html="errorSizeText"></div>
  </div>
</template>

<script>
import axios from 'axios'

//TODO переписать на api
export default {
  name: "upload-image",
  props: {
    multiple: {
      type: Boolean,
    },
    button: {
      type: String,
    },
    maxSize: {
      type: Number // in Mb
    }
  },
  data: function () {
    return {
      uploadedFile: [],
      config: {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      },
      maxSize_: null,
      errorSize: false,
    }
  },
  created: function () {
    if (this.maxSize) {
      this.maxSize_ = this.maxSize;
    }
  },
  computed: {
    errorSizeText: function () {
      let textError = '';
      if (this.maxSize_) {
        textError = 'не более ' + this.maxSize_ + 'Мб.';
      }
      return textError;
    },
    textButton: function () {
      let textButton = 'загрузить изображение';
      if (this.button) {
        textButton = this.button;
      }
      return textButton;
    }
  },
  methods: {
    addFiles: function () {
      this.$refs.files.click();
    },
    handleFilesUpload: function () {
      let uploadedFile;
      let that = this;
      that.errorSize = false;

      if (this.multiple) {
        uploadedFile = [];
        let tempFiles = this.$refs.files.files;
        for (var i = 0; i < tempFiles.length; i++) {
          if (that.maxSize_ && !that.errorSize) {
            that.errorSize = (1024 * 1024 * that.maxSize_) < tempFiles[i].size;
          }
          uploadedFile.push(tempFiles[i]);
        }
      } else {
        if (that.maxSize_) {
          console.log(this.$refs['files'].files[0]);
          console.log(1024 * 1024 * that.maxSize_);
          that.errorSize = ((1024 * 1024 * that.maxSize_) < this.$refs['files'].files[0].size);
        }
        uploadedFile = this.$refs['files'].files[0];
      }

      if (!that.errorSize) {
        let formData = new FormData();
        formData.append('temp', uploadedFile);
        axios.post('/admin/uploader', formData, that.config)
            .then(r => {
              if (r.data.result === true) {
                that.returnImages(r.data.url)
              }
              that.$refs.files.files = [];
            })
            .catch(e => {
              that.$root.responseServer = e;
            });
      }
    },
    returnImages: function (images) {
      this.$emit('input', images);
    }
  },
}

</script>

<style scoped>
.input_hidden{
  display:none;
}
</style>