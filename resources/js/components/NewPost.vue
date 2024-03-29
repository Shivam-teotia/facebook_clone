<template>
  <div class="bg-white rounded shadow w-2/3 p-4">
    <div class="flex justify-between items-center">
      <div>
        <div class="w-8">
          <img
            src="https://cdn.pixabay.com/photo/2014/07/09/10/04/man-388104_960_720.jpg"
            alt="profile image for user"
            class="w-8 h-8 object-cover rounded-full"
          />
        </div>
      </div>
      <div class="flex-1 flex mx-4">
        <input
          type="text"
          name="body"
          v-model="postMessage"
          class="w-full pl-4 h-8 bg-gray-200 rounded-full focus:outline-none focus:shadow-outline text-sm"
          placeholder="Add a post"
        />
        <transition name="fade">
          <button
            v-if="postMessage"
            @click="postHandler"
            class="bg-gray-200 ml-2 px-3 py-1 rounded-full"
          >
            Post
          </button>
        </transition>
      </div>
      <div>
        <button
          ref="postImage"
          class="dz-clickable flex justify-center items-center rounded-full w-10 h-10 bg-gray-200"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="fill-current w-5 h-5"
          >
            <path
              d="M21.8 4H2.2c-.2 0-.3.2-.3.3v15.3c0 .3.1.4.3.4h19.6c.2 0 .3-.1.3-.3V4.3c0-.1-.1-.3-.3-.3zm-1.6 13.4l-4.4-4.6c0-.1-.1-.1-.2 0l-3.1 2.7-3.9-4.8h-.1s-.1 0-.1.1L3.8 17V6h16.4v11.4zm-4.9-6.8c.9 0 1.6-.7 1.6-1.6 0-.9-.7-1.6-1.6-1.6-.9 0-1.6.7-1.6 1.6.1.9.8 1.6 1.6 1.6z"
            />
          </svg>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted, ref, getCurrentInstance } from "vue";
import { debounce } from "lodash";
import { useStore } from "vuex";
import Dropzone from "dropzone";
const store = useStore();
const dropzone = ref(null);
let authUser = ref(null);
authUser.value = store.getters.authUser;
const postMessage = computed({
  get: () => store.getters.postMessage,
  set: debounce(function (val) {
    store.commit("updateMessage", val);
  }, 1000),
});
const settings = computed(() => ({
  paramName: "image",
  url: "/api/posts",
  acceptedFiles: "image/*",
  clickable: ".dz-clickable",
  previewsContainer: ".dropzone-previews",
  previewTemplate: document.querySelector("#dz-template").innerHTML,
  autoProcessQueue: false,
  maxFiles: 1,
  params: {
    width: 1000,
    height: 1000,
  },
  headers: {
    "X-CSRF-TOKEN": document.head.querySelector("meta[name=csrf-token]")
      .content,
  },
  sending: (file, xhr, formData) => {
    formData.append("body", store.getters.postMessage);
  },
  success: (event, res) => {
    // alert("success");
    dropzone.value.removeAllFiles();
    store.commit("pushPost", res);
  },
  maxfilesexceeded: (file) => {
    dropzone.value.removeAllFiles();
    dropzone.value.addFile(file);
  },
}));
onMounted(() => {
  dropzone.value = new Dropzone(
    getCurrentInstance().ctx.$refs.postImage,
    settings.value
  );
});
function postHandler() {
  store.dispatch("postMessage");
}
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>