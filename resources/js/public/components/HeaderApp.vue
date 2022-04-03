<template>
  <header class="page-header">
    <div class="header-content">
      <a class="logo" href="/">
        <img src="/build/images/logo.svg" alt="С легким паром">
      </a>
      <div class="header-link"></div>
      <div class="header-search-block">
        <input id="h-search" type="text" placeholder="поиск по сайту">
        <button class="btn btn_text">
          <icon-svg icon="svg-search"></icon-svg>
        </button>
      </div>
      <div class="header-icon-block"></div>
    </div>
  </header>
</template>

<script>
import IconSvg from "./Icon-svg/icon-svg";

export default {
  name: "HeaderApp",
  components: {IconSvg}
}
</script>

<style scoped>

</style>