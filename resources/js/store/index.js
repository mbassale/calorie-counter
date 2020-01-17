
import {
    SET_TOKEN,
    SET_USER
} from './mutations';

import {
    LOGIN,
    LOGOUT
} from './actions';

export default {
    state: {
        user: null,
        token: null
    },
    getters: {
        isGuest(state) {
            return !state.token;
        }
    },
    mutations: {
        [SET_TOKEN](state, token) {
            state.token = token;
        },
        [SET_USER](state, user) {
            state.user = user;
        }
    },
    actions: {
        async [LOGIN]({ commit, state }, { token, remember }) {
            commit(SET_TOKEN, token);
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            const { data } = await axios.get('/api/user');
            commit(SET_USER, data);
            // Cookies.set('token', token, { expires: remember ? 365 : null })
        },
        [LOGOUT]({ commit }) {
            commit(SET_TOKEN, null);
            commit(SET_USER, null);
            return Promise.resolve();
        }
    }
};
