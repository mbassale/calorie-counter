
export default {
    state: {
        user: null
    },
    getters: {
        isGuest(state) {
            return !state.user;
        }
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        }
    }
};
