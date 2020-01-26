import _ from 'lodash';
import axios from 'axios';
import createPersistedState from 'vuex-persistedstate';

import {
    SET_TOKEN,
    SET_ROLES,
    SET_USER,
    SET_USERS,
    SET_MEALS
} from './mutations';

import {
    LOGIN,
    LOGOUT,
    GET_CURRENT_USER,
    LOAD_ROLES,
    LOAD_USERS,
    CREATE_USER,
    UPDATE_USER,
    DELETE_USER,
    LOAD_MEALS,
    SEARCH_MEALS,
    CREATE_MEAL,
    UPDATE_MEAL,
    DELETE_MEAL
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
        roles: [],
        users: [],
        meals: [],
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
        [SET_ROLES](state, roles) {
            state.roles = roles || [];
        },
        [SET_USERS](state, users) {
            state.users = users || [];
        },
        [SET_MEALS](state, meals) {
            state.meals = meals || [];
        }
    },
    actions: {
        [LOGIN]({ dispatch, commit, state }, { token }) {
            commit(SET_TOKEN, token);
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            return dispatch(GET_CURRENT_USER);
        },
        [LOGOUT]({ commit }) {
            commit(SET_TOKEN, null);
            commit(SET_USER, null);
            commit(SET_USERS, []);
            commit(SET_MEALS, []);
            return Promise.resolve();
        },
        [GET_CURRENT_USER]({ commit }) {
            return axios.get('/api/user').then(response => {
                commit(SET_USER, response.data || null);
            });
        },
        [LOAD_ROLES]({ commit }) {
            return axios.get('/api/roles').then(response => {
                commit(SET_ROLES, response.data || []);
            });
        },
        [LOAD_USERS]({ commit }) {
            return axios.get('/api/users').then(response => {
                commit(SET_USERS, response.data || []);
            });
        },
        [CREATE_USER]({ commit, state }, payload) {
            return axios.post('/api/users', payload).then(response => {
                const user = response.data || null;
                if (user) {
                    const updatedUsers = _.cloneDeep(state.users);
                    updatedUsers.push(user);
                    commit(SET_USERS, updatedUsers);
                }
            });
        },
        [UPDATE_USER]({ dispatch, commit, state }, payload) {
            return axios.put('/api/users/' + payload.id, payload).then(response => {
                const user = response.data || null;
                if (user) {
                    const userIndex = state.users.findIndex(u => u.id === user.id);
                    if (userIndex >= 0) {
                        const updatedUsers = _.cloneDeep(state.users);
                        updatedUsers.splice(userIndex, 1, user);
                        commit(SET_USERS, updatedUsers);
                    }
                    if (user.id === state.user.id) {
                        commit(SET_USER, user);
                    }
                }
            });
        },
        [DELETE_USER]({ dispatch, commit, state }, payload) {
            return axios.delete('/api/users/' + payload.id).then(response => {
                const user = response.data || null;
                if (user) {
                    const userIndex = state.users.findIndex(u => u.id === user.id);
                    if (userIndex >= 0) {
                        const updatedUsers = _.cloneDeep(state.users);
                        updatedUsers.splice(userIndex, 1);
                        commit(SET_USERS, updatedUsers);
                    }
                }
            });
        },
        [LOAD_MEALS]({ commit }) {
            return axios.get('/api/meals').then(response => {
                commit(SET_MEALS, response.data || []);
            });
        },
        [SEARCH_MEALS]({ commit }, payload) {
            const searchParams = _.reduce(payload, (params, value, key) => {
                if (!_.isEmpty(value) && !_.isEmpty(key)) {
                    params.push(key + '=' + value);
                }
                return params;
            }, []);
            const params = searchParams.join('&');
            return axios.get('/api/meals?' + params).then(response => {
                commit(SET_MEALS, response.data || []);
            });
        },
        [CREATE_MEAL]({ dispatch }, payload) {
            return axios.post('/api/meals', payload).then(() => dispatch(LOAD_MEALS));
        },
        [UPDATE_MEAL]({ dispatch }, payload) {
            return axios.put('/api/meals/' + payload.id, payload).then(() => dispatch(LOAD_MEALS));
        },
        [DELETE_MEAL]({ dispatch }, payload) {
            return axios.delete('/api/meals/' + payload.id).then(() => dispatch(LOAD_MEALS));
        }
    }
};
