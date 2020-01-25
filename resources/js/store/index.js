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
        [LOGIN]({ commit, state }, { token }) {
            commit(SET_TOKEN, token);
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
            return axios.get('/api/user').then(response => {
                commit(SET_USER, response.data || []);
            });
        },
        [LOGOUT]({ commit }) {
            commit(SET_TOKEN, null);
            commit(SET_USER, null);
            return Promise.resolve();
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
        [UPDATE_USER]({ commit, state }, payload) {
            return axios.put('/api/users/' + payload.id, payload).then(response => {
                const user = response.data || null;
                if (user) {
                    const userIndex = state.users.findIndex(u => u.id === user.id);
                    if (userIndex >= 0) {
                        const updatedUsers = _.cloneDeep(state.users);
                        updatedUsers.splice(userIndex, 1, user);
                        commit(SET_USERS, updatedUsers);
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
        [CREATE_MEAL]({ commit, state }, payload) {
            return axios.post('/api/meals', payload).then(response => {
                const meal = response.data || null;
                if (meal) {
                    const updatedMeals = _.cloneDeep(state.meals);
                    updatedMeals.push(meal);
                    commit(SET_MEALS, updatedMeals);
                }
            });
        },
        [UPDATE_MEAL]({ commit, state }, payload) {
            return axios.put('/api/meals/' + payload.id, payload).then(response => {
                const meal = response.data || null;
                if (meal) {
                    const mealIndex = state.meals.findIndex(m => m.id === meal.id);
                    if (mealIndex >= 0) {
                        const updatedMeals = _.cloneDeep(state.meals);
                        updatedMeals.splice(mealIndex, 1, meal);
                        commit(SET_MEALS, updatedMeals);
                    }
                }
            });
        },
        [DELETE_MEAL]({ commit, state }, payload) {
            return axios.delete('/api/meals/' + payload.id).then(response => {
                const meal = response.data || null;
                if (meal) {
                    const mealIndex = state.meals.findIndex(m => m.id === meal.id);
                    if (mealIndex >= 0) {
                        const updatedMeals = _.cloneDeep(state.meals);
                        updatedMeals.splice(mealIndex, 1);
                        commit(SET_MEALS, updatedMeals);
                    }
                }
            });
        }
    }
};
