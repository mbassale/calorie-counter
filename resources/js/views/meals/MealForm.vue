<template>
    <b-form @submit="onSubmit" @reset="onReset">
        <b-form-group v-if="isAdmin" label="User" label-for="user_id" :state="$v.user_id.$error ? false : null">
            <b-form-select id="user_id" v-model="user_id" :options="selectUsers"
                           :state="$v.user_id.$error ? false : null" :disabled="disabled"/>
        </b-form-group>

        <b-form-group label="Name" label-for="name" :state="$v.name.$error ? false : null">
            <b-form-input id="name" v-model="name" placeholder="Name"
                          :state="$v.name.$error ? false : null" :disabled="disabled"/>
        </b-form-group>

        <b-form-row>
            <div class="col-auto">
                <b-form-group label="Date" label-for="date" :state="$v.date.$error ? false : null">
                    <VueDatePicker v-model="date" class="form-control" format="YYYY-MM-DD" bootstrap-styling :disabled="disabled"/>
                    <b-form-invalid-feedback v-if="$v.date.$error" force-show>
                        Invalid date
                    </b-form-invalid-feedback>
                </b-form-group>
            </div>
            <div class="col-auto mr-auto">
                <b-form-group label="Time" label-for="time" :state="$v.time.$error ? false : null">
                    <masked-input v-model="time" class="form-control" mask="11:11" placeholder="Time"
                                  :disabled="disabled"/>
                    <b-form-invalid-feedback v-if="$v.time.$error" force-show>
                        Invalid time
                    </b-form-invalid-feedback>
                </b-form-group>
            </div>
        </b-form-row>

        <b-form-group label="Calories" label-for="calories" :state="$v.calories.$error ? false : null">
            <b-form-input id="calories" type="number" min="0" step="1" placeholder="Calories"
                          v-model="calories" :state="$v.calories.$error ? false : null" :disabled="disabled"/>
        </b-form-group>

        <b-button type="submit" variant="primary" :disabled="disabled || $v.$anyError">
            <fa-icon v-if="isProcessing" icon="spinner" spin/>
            <fa-icon v-else icon="save"/>
            Save
        </b-button>
        <b-button type="reset" variant="danger" :disabled="disabled">
            <fa-icon icon="eraser"/>
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
    import {mapState, mapGetters} from 'vuex';
    import {CREATE_MEAL, LOAD_USERS, UPDATE_MEAL} from '../../store/actions';
    import ToastMixin from '../../mixins/ToastMixin';
    import {VueDatePicker} from '@mathieustan/vue-datepicker';
    import MaskedInput from 'vue-masked-input';
    import {minLength, required, numeric, helpers} from 'vuelidate/lib/validators';

    const date = helpers.regex('date', /^([\d]{4})-([\d]{2})-([\d]{2})$/);

    const time = (value) => {
        const matches = /^([\d]{1,2}):([\d]{2})$/.exec(value);
        if (matches && matches.length >= 3) {
            const hour = parseInt(matches[1]);
            const minutes = parseInt(matches[2]);
            if (!_.isNaN(hour) && _.isInteger(hour) && !_.isNaN(minutes) && _.isInteger(minutes)) {
                return hour >= 0 && hour <= 23 && minutes >= 0 && minutes <= 59;
            }
        }
        return false;
    };

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
        validations() {
            const schema = {
                name: {
                    required,
                    minLength: minLength(2)
                },
                date: {
                    required,
                    date
                },
                time: {
                    required,
                    time
                },
                calories: {
                    required,
                    numeric
                }
            };
            if (this.isAdmin) {
                _.assign(schema, {
                    user_id: {
                        required
                    }
                });
            }
            return schema;
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
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return;
                }
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
