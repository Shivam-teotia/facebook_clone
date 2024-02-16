<template>
  <div class="flex flex-col items-center py-4">
    <NewPost />
    <p
      v-if="
        !$store.getters.newsPostsStatus ||
        $store.getters.newsPostsStatus == 'loading'
      "
    >
      Loading posts.....
    </p>
    <Post
      v-else
      v-for="(post, postKey) in posts.data"
      :key="postKey"
      :post="post"
    />
  </div>
</template>

<script setup>
import { computed, onMounted } from "vue";
import { useStore } from "vuex";
import NewPost from "../components/NewPost.vue";
import Post from "../components/Post.vue";

const posts = computed(() => {
  return store.getters.posts;
});
const store = useStore();
onMounted(() => {
  store.dispatch("fetchNewsPosts");
});
</script>

<style scoped>
</style>
