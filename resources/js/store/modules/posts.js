import axios from "axios";
const state = () => ({
    posts: null,
    newsPostsStatus: null,
    postMessage: '',
});
const getters = {
    posts(state) {
        return state.posts;
    },
    newsPostsStatus(state) {
        return state.newsPostsStatus;
    },
    postMessage(state) {
        return state.postMessage;
    }
}
const mutations = {
    setPosts(state, posts) {
        state.posts = posts;
    },
    setPostsStatus(state, status) {
        state.newsPostsStatus = status;
    },
    updateMessage(state, message) {
        state.postMessage = message;
    },
    pushPost(state, post) {
        state.posts.data.unshift(post);
    },
    pushLikes(state, data) {
        state.posts.data[data.postKey].data.attributes.likes = data.likes;
    },
    pushComments(state, data) {
        state.posts.data[data.postKey].data.attributes.comments = data.comments;
    }
}
const actions = {
    fetchNewsPosts({ commit, state }) {
        commit('setPostsStatus', 'loading')
        axios
            .get("/api/posts")
            .then((res) => {
                commit("setPosts", res.data);
                commit('setPostsStatus', 'success')
            })
            .catch((err) => {
                commit('setPostsStatus', 'error')
            });
    },
    fetchUserPost({ commit }, userId) {
        commit('setPostsStatus', 'loading')
        axios
            .get("/api/users/" + userId + "/posts")
            .then((res) => {
                commit('setPosts', res.data);
                commit('setPostsStatus', 'success');
            })
            .catch((error) => {
                console.log("unable to fetch posts");
                commit('setPostStatus', 'errors');
            })
    },
    postMessage({ commit, state }) {
        commit('setPostsStatus', 'loading')
        axios
            .post("/api/posts", { body: state.postMessage })
            .then((res) => {
                commit("pushPost", res.data);
                commit('updateMessage', '')
                commit("setPostsStatus", "success");
            })
            .catch((err) => {
                // commit('setPostsStatus', 'error')
                commit("setPostsStatus", "error");
            });
    },
    likePost({ commit, state }, data) {
        axios.post('/api/posts/' + data.postId + '/likes')
            .then(res => {
                commit('pushLikes', { likes: res.data, postKey: data.postKey });
            })
            .catch(error => {

            })
    },
    commentPost({ commit, state }, data) {
        axios.post('/api/posts/' + data.postId + '/comment', { body: data.body })
            .then(res => {
                commit('pushComments', { comments: res.data, postKey: data.postKey });
            })
            .catch(error => {

            })
    }
}
export default {
    state,
    getters,
    actions,
    mutations,
};