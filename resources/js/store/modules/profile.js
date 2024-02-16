import axios from "axios";
const state = () => ({
    profileuser: null,
    userStatus: null,
});
const getters = {
    profileuser(state) {
        return state.profileuser;
    },
    status(state) {
        return state.postStatus
    },
    userStatus(state) {
        return state.userStatus;
    },
    friendship(state) {
        return state.profileuser?.data.attributes.friendship
    },
    friendButtonText(state, getters, rootState) {
        if (rootState.User.user?.data?.user_id === state.profileuser?.data?.user_id) {
            return "";
        }
        else if (getters.friendship === null) {
            return 'Add Friend';
        } else if (getters.friendship?.data?.attributes?.confirmed_at == null
            && getters.friendship?.data?.attributes?.friend_id !== rootState.User?.user?.data?.user_id
        ) {
            return 'Pending Friend Request';
        }
        else if (getters.friendship.data.attributes.confirmed_at !== null) {
            return "";
        }
        return "Accept"
    }
}
const mutations = {
    setProfileUser(state, user) {
        state.profileuser = user;
    },
    setUserStatus(state, status) {
        state.userStatus = status;
    },
    setUserFriendship(state, friendship) {
        state.profileuser.data.attributes.friendship = friendship
    }
}
const actions = {
    fetchUser({ commit, dispatch }, usrId) {
        commit('setUserStatus', 'loading')
        axios
            .get("/api/users/" + usrId)
            .then((res) => {
                commit('setProfileUser', res.data);
                commit('setUserStatus', 'success');
            })
            .catch((error) => {
                console.log("unable to fetch users");
                commit('setUserStatus', 'error');
            })
    },
    sendFriendRequest(context, friendId) {
        if (context.getters.friendButtonText !== 'Add Friend') {
            return;
        }
        axios.post('/api/friend-request', { 'friend_id': friendId })
            .then(res => {
                context.commit('setUserFriendship', res.data);
            }).
            catch(err => {
                // context.commit('setButtonText', 'Add Friend');
            })
    },
    acceptFriendRequest(context, userId) {
        axios.post('/api/friend-request-response', { 'user_id': userId, 'status': 1 })
            .then(res => {
                context.commit('setUserFriendship', res.data);
            }).
            catch(err => {
                // context.commit('setButtonText', 'Add Friend');
            })
    },
    ignoreFriendRequest(context, userId) {
        axios.delete('/api/friend-request-response/delete', { data: { 'user_id': userId } })
            .then(res => {
                context.commit('setUserFriendship', null);
            }).
            catch(err => {
                // context.commit('setButtonText', 'Add Friend');
            })
    },

}
export default {
    state,
    getters,
    actions,
    mutations,
};