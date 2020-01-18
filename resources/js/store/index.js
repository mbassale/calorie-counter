
import createPersistedState from 'vuex-persistedstate';

import {
    SET_TOKEN,
    SET_USER,
    SET_USERS
} from './mutations';

import {
    LOGIN,
    LOGOUT,
    LOAD_USERS
} from './actions';

import { ROLE_ADMIN, ROLE_MANAGER, ROLE_USER } from './roles';

export default {
    plugins: [
        createPersistedState({
            rehydrated: store => {
                if (store.state.token) {
                    window.axios.defaults.headers.common['Authorization'] = `Bearer ${store.state.token}`;
                }
            }
        })
    ],
    state: {
        user: null,
        token: null,
        users: []
    },
    getters: {
        isGuest(state) {
            return !state.token;
        },
        isAdmin(state) {
            return state.user ? state.user.role_id === ROLE_ADMIN : false;
        },
        isManager(state) {
            return state.user ? state.user.role_id === ROLE_MANAGER : false;
        },
        isUser(state) {
            return state.user ? state.user.role_id === ROLE_USER : false;
        }
    },
    mutations: {
        [SET_TOKEN](state, token) {
            state.token = token;
        },
        [SET_USER](state, user) {
            state.user = user;
        },
        [SET_USERS](state, users) {
            state.users = users || [];
        }
    },
    actions: {
        async [LOGIN]({ commit, state }, { token }) {
            commit(SET_TOKEN, token);
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            const { data } = await axios.get('/api/user');
            commit(SET_USER, data);
        },
        [LOGOUT]({ commit }) {
            commit(SET_TOKEN, null);
            commit(SET_USER, null);
            return Promise.resolve();
        },
        async [LOAD_USERS]({ commit }) {
            const { data } = await axios.get('/api/users');
            commit(SET_USERS, data);
        }
    }
};
