<template>
  <div class="flex flex-col items-center">
    <div class="relative mb-8">
      <div class="w-100 h-64 overflow-hidden z-10">
        <UploadableImages
          image-width="1500"
          image-height="300"
          location="cover"
          :user-image="user?.data?.attributes.cover_image"
        />
      </div>
      <div class="absolute flex items-center bottom-0 left-0 -mb-8 ml-12 z-20">
        <div class="w-32">
          <img
            src="https://cdn.pixabay.com/photo/2014/07/09/10/04/man-388104_960_720.jpg"
            alt=""
            class="object-cover w-32 h-32 border-4 border-gray-200 rounded-full shadow-lg"
          />
        </div>
        <p class="text-2xl text-gray-100 ml-4">
          {{ user?.data?.attributes?.name }}
        </p>
      </div>
      <div class="absolute flex items-center bottom-0 right-0 mb-4 mr-12 z-20">
        <button
          v-if="
            $store.getters.friendButtonText &&
            $store.getters.friendButtonText !== 'Accept'
          "
          class="py-1 px-3 bg-gray-400 rounded"
          @click="$store.dispatch('sendFriendRequest', $route.params.userId)"
        >
          {{ $store.getters.friendButtonText }}
        </button>
        <button
          v-if="
            $store.getters.friendButtonText &&
            $store.getters.friendButtonText === 'Accept'
          "
          class="mr-2 py-1 px-3 bg-blue-500 rounded"
          @click="$store.dispatch('acceptFriendRequest', $route.params.userId)"
        >
          Accept
        </button>
        <button
          v-if="
            $store.getters.friendButtonText &&
            $store.getters.friendButtonText === 'Accept'
          "
          class="mr-2 py-1 px-3 bg-gray-500 rounded"
          @click="$store.dispatch('ignoreFriendRequest', $route.params.userId)"
        >
          Ignore
        </button>
      </div>
    </div>
    <p v-if="postLoading">Loading posts.....</p>
    <Post
      v-else
      v-for="(post, postKey) in posts.data"
      :key="postKey"
      :post="post"
    />
    <p v-if="!postLoading && posts.data.length < 1">No Posts Yet</p>
  </div>
</template>
<script setup>
import { computed, onMounted, ref } from "vue";
import { useRoute } from "vue-router";
import Post from "../../components/Post.vue";
import UploadableImages from "../../components/UploadableImages.vue";
import { useStore } from "vuex";
const route = useRoute();
const store = useStore();
const posts = computed(() => store.getters.posts);

onMounted(() => {
  store.dispatch("fetchUser", route.params.userId);
  store.dispatch("fetchUserPost", route.params.userId);
});
const user = computed(() => store.getters.profileuser);
const postLoading = computed(() => {
  if (
    store.getters.newsPostsStatus === null ||
    store.getters.newsPostsStatus === "loading"
  ) {
    return true;
  } else {
    return false;
  }
});
</script>