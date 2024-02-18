<template>
  <div>
    <img
      :src="userImage?.data?.attributes?.path"
      :alt="alt"
      ref="userImage"
      :class="classes"
    />
  </div>
</template>
<script setup>
import Dropzone from "dropzone";
import { onMounted, ref, getCurrentInstance, computed } from "vue";
import { useRoute } from "vue-router";
import { useStore } from "vuex";
const dropzone = ref(null);
const { store } = useStore();
const { route } = useRoute();
onMounted(() => {
  dropzone.value = new Dropzone(
    getCurrentInstance().ctx.$refs.userImage,
    //syntax for usage of $ref in composition api
    settings.value
  );
});
const props = defineProps([
  "imageWidth",
  "imageHeight",
  "location",
  "classes",
  "alt",
  "userImage",
]);
const settings = computed(() => {
  return {
    paramName: "image",
    url: "/api/user-images",
    acceptedFiles: "image/*",
    params: {
      width: props.imageWidth,
      height: props.imageHeight,
      location: props.location,
    },
    headers: {
      "X-CSRF-TOKEN": document.head.querySelector("meta[name=csrf-token]")
        .content,
    },
    success: (e, res) => {
      store.dispatch("fetchAuthUser");
      store.dispatch("fetchProfileUser", route.params.userId);
      store.dispatch("fetchUserPost", route.params.userId);
    },
  };
});
</script>