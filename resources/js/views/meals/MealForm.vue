<template>
    <b-form @submit="onSubmit" @reset="onReset">
        <b-form-group v-if="isAdmin" label="User" label-for="user_id">
            <b-form-select id="user_id" v-model="user_id" :options="selectUsers" required
                           :disabled="disabled" />
        </b-form-group>

        <b-form-group label="Name" label-for="name">
            <b-form-input id="name" v-model="name" placeholder="Name"
                          :disabled="disabled" />
        </b-form-group>

        <b-form-row>
            <div class="col-auto">
                <b-form-group label="Date" label-for="date">
                    <VueDatePicker v-model="date" class="form-control" format="YYYY-MM-DD" :disabled="disabled" />
                </b-form-group>
            </div>
            <div class="col-auto mr-auto">
                <b-form-group label="Time" label-for="time">
                    <masked-input v-model="time" class="form-control" mask="11:11" placeholder="Time" :disabled="disabled" />
                </b-form-group>
            </div>
        </b-form-row>

        <b-form-group label="Calories" label-for="calories">
            <b-form-input id="calories" type="number" min="0" step="1" placeholder="Calories"
                          v-model="calories" :disabled="disabled"/>
        </b-form-group>

        <b-button type="submit" variant="primary" :disabled="disabled">
            <fa-icon v-if="isProcessing" icon="spinner" spin/>
            <fa-icon v-else icon="save" />
            Save
        </b-button>
        <b-button type="reset" variant="danger" :disabled="disabled">
            <fa-icon icon="eraser" />
            Reset
        </b-button>
        <b-button variant="secondary" @click="$emit('cancel')" :disabled="disabled">
            Cancel
        </b-button>
    </b-form>
</template>

<script>
    import _ from 'lodash';
    import moment from 'moment';
    import { mapState, mapGetters } from 'vuex';
    import {CREATE_MEAL, LOAD_USERS, UPDATE_MEAL} from '../../store/actions';
    import ToastMixin from '../../mixins/ToastMixin';
    import { VueDatePicker } from '@mathieustan/vue-datepicker';
    import MaskedInput from 'vue-masked-input';

    export default {
        name: 'MealForm',
        components: {
            VueDatePicker,
            MaskedInput
        },
        mixins: [ToastMixin],
        props: {
            meal: {
                required: false,
                validator: val => _.isObject(val) || _.isNull(val),
                default: () => null
            }
        },
        data() {
            return {
                isLoading: false,
                isProcessing: false,
                id: null,
                user_id: null,
                name: null,
                date: null,
                time: null,
                calories: null
            };
        },
        computed: {
            disabled() {
                return this.isLoading || this.isProcessing;
            },
            selectUsers() {
                return this.users.map(user => {
                    return {
                        value: user.id,
                        text: user.first_name + ' ' + user.last_name + ' <' + user.email + '>'
                    };
                });
            },
            ...mapState(['users']),
            ...mapGetters(['isAdmin'])
        },
        watch: {
            meal() {
                this.resetForm();
            }
        },
        mounted() {
            this.resetForm();
            if (this.isAdmin) {
                this.$nextTick(() => {
                    this.loadUsers();
                });
            }
        },
        methods: {
            loadUsers() {
                this.isLoading = true;
                this.$store.dispatch(LOAD_USERS)
                    .catch(error => this.showNetworkError(error))
                    .finally(() => this.isLoading = false);
            },
            resetForm() {
                this.id = null;
                this.user_id = null;
                this.name = null;
                this.date = null;
                this.time = null;
                this.calories = null;
                if (this.meal) {
                    this.id = this.meal.id;
                    this.user_id = this.meal.user_id;
                    this.name = this.meal.name;
                    this.calories = this.meal.calories;
                    let dateTime = moment(this.meal.date);
                    if (dateTime.isValid()) {
                        this.date = dateTime.format('YYYY-MM-DD');
                        this.time = dateTime.format('HH:mm');
                    }
                }
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.isProcessing = true;
                const data = _.pick(this, ['id', 'user_id', 'name', 'calories']);
                data.date = this.date + ' ' + this.time + ':00';
                let request = null;
                if (this.meal && this.meal.id) {
                    request = this.$store.dispatch(UPDATE_MEAL, data)
                        .then(() => this.showSuccess('Meal Updated'));
                } else {
                    request = this.$store.dispatch(CREATE_MEAL, data)
                        .then(() => this.showSuccess('Meal Created'));
                }
                request.catch(error => this.showNetworkError(error))
                    .finally(() => this.isProcessing = false);
            },
            onReset(evt) {
                evt.preventDefault();
                this.resetForm();
            }
        }
    }
</script>

<style scoped>

</style>
