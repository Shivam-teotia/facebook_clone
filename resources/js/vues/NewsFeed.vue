<template>
  <div class="flex flex-col items-center py-4">
    <NewPost />
    <p v-if="loading">Loading posts.....</p>
    <Post
      v-else
      v-for="post in posts.data"
      :key="post.data.post_id"
      :post="post"
    />
  </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import NewPost from "../components/NewPost.vue";
import Post from "../components/Post.vue";
const loading = ref(true);
const posts = ref([]);
onMounted(() => {
  axios
    .get("/api/posts")
    .then((res) => {
      posts.value = res.data;
      loading.value = false;
    })
    .catch((error) => {
      console.log("Unable to get posts");
      loading.value = false;
    });
});
</script>

<style scoped>
</style>
