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
    UPDATE_USER,
    DELETE_USER,
    LOAD_MEALS,
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
        async [LOAD_ROLES]({ commit }) {
            const { data } = await axios.get('/api/roles');
            commit(SET_ROLES, data);
        },
        async [LOAD_USERS]({ commit }) {
            const { data } = await axios.get('/api/users');
            commit(SET_USERS, data);
        },
        async [UPDATE_USER]({ commit, state }, payload) {
            const { data: user } = await axios.put('/api/users/' + payload.id, payload);
            let userIndex = state.users.findIndex(u => u.id === user.id);
            if (userIndex >= 0) {
                let updatedUsers = _.cloneDeep(state.users);
                updatedUsers.splice(userIndex, 1, user);
                commit(SET_USERS, updatedUsers);
            }
        },
        async [DELETE_USER]({ dispatch, commit, state }, payload) {
            const { data: user } = await axios.delete('/api/users/' + payload.id);
            let userIndex = state.users.findIndex(u => u.id === user.id);
            if (userIndex >= 0) {
                let updatedUsers = _.cloneDeep(state.users);
                updatedUsers.splice(userIndex, 1);
                commit(SET_USERS, updatedUsers);
            }
        },
        async [LOAD_MEALS]({ commit }) {
            const { data } = await axios.get('/api/meals');
            commit(SET_MEALS, data);
        },
        async [CREATE_MEAL]({ commit, state }, payload) {
            const { data: meal } = await axios.post('/api/meals', payload);
            let updatedMeals = _.cloneDeep(state.meals);
            updatedMeals.push(meal);
            commit(SET_MEALS, updatedMeals);
        },
        async [UPDATE_MEAL]({ commit, state }, payload) {
            const { data: meal } = await axios.put('/api/meals/' + payload.id, payload);
            let mealIndex = state.meals.findIndex(m => m.id === meal.id);
            if (mealIndex >= 0) {
                let updatedMeals = _.cloneDeep(state.meals);
                updatedMeals.splice(mealIndex, 1, meal);
                commit(SET_MEALS, updatedMeals);
            }
        },
        async [DELETE_MEAL]({ commit, state }, payload) {
            const { data: meal } = await axios.delete('/api/meals/' + payload.id);
            let mealIndex = state.meals.findIndex(m => m.id === meal.id);
            if (mealIndex >= 0) {
                let updatedMeals = _.cloneDeep(state.meals);
                updatedMeals.splice(mealIndex, 1);
                commit(SET_MEALS, updatedMeals);
            }
        }
    }
};
