<template>
  <div class="flex flex-col flex-1 h-screen overflow-y-hidden">
    <Nav />

    <div class="flex overflow-y-hidden flex-1">
      <Sidebar />

      <div class="overflow-x-hidden w-2/3">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script setup>
import Nav from "./Nav.vue";
import Sidebar from "./Sidebar.vue";
import { useStore } from "vuex";
import { onMounted, watchEffect } from "vue";
import { useRoute } from "vue-router";
const store = useStore();
const route = useRoute();
watchEffect(() => {
  store.dispatch("setPageTitle", route.meta.title);
});
onMounted(() => {
  store.dispatch("fetchAuthUser");
});
</script>

<style scoped>
</style>
