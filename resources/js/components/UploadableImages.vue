<template>
  <div>
    <img
      :src="imageObject?.data?.attributes?.path"
      alt="user background image"
      ref="userImage"
      class="object-cover w-full"
    />
  </div>
</template>
<script setup>
import Dropzone from "dropzone";
import { onMounted, ref, getCurrentInstance, computed } from "vue";
const dropzone = ref(null);
let uploadedImage = ref(null);
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
  // "classes",
  // "alt",
  "userImage",
]);
const imageObject = computed(() => {
  return uploadedImage || props.userImage;
});
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
      alert("uploaded!");
      console.log(res);
      uploadedImage = res;
      //   store.dispatch("fetchAuthUser");
      //   store.dispatch("fetchProfileUser", route.params.userId);
      //   store.dispatch("fetchUserPost", route.params.userId);
    },
  };
});
</script>