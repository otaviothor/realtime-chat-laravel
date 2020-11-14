import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import createPersistedState from 'vuex-persistedstate';

Vue.use(Vuex);

const store = new Vuex.Store({
    state: {
        user: {}
    },
    mutations: {
        setUserState: (state, value) => state.user = value
    },
    actions: {
        userStateAction: ({commit}) => {
            axios.get('api/users/me')
                .then(response => {
                    const userResponse = response.data.user
                    commit('setUserState', userResponse)
                })
        }
    },
    plugins: [ createPersistedState() ]
});

export default store;
