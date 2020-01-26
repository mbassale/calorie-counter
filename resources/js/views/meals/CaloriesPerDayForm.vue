<template>
    <b-card>
        <b-form @submit="onSubmit" @reset="onReset">
            <b-form-group label="Calories Per Day" label-for="caloriesPerDay"
                          :state="$v.calories_per_day.$error ? false : null">
                <b-form-input id="caloriesPerDay" type="number" min="0" step="1" placeholder="Calories Per Day"
                              v-model="calories_per_day" :state="$v.calories_per_day.$error ? false : null"
                              :disabled="disabled"/>
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
    </b-card>
</template>

<script>
    import _ from 'lodash';
    import { required, numeric } from 'vuelidate/lib/validators';
    import { mapState } from 'vuex';
    import ToastMixin from '../../mixins/ToastMixin';
    import {GET_CURRENT_USER, UPDATE_USER} from '../../store/actions';

    export default {
        name: 'CaloriesPerDayForm',
        mixins: [ToastMixin],
        data() {
            return {
                isProcessing: null,
                calories_per_day: null
            };
        },
        computed: {
            disabled() {
                return this.isProcessing;
            },
            ...mapState(['user'])
        },
        validations: {
            calories_per_day: {
                required,
                numeric
            }
        },
        mounted() {
            this.$nextTick(() => this.resetForm());
        },
        methods: {
            resetForm() {
                this.calories_per_day = null;
                if (this.user) {
                    this.calories_per_day = this.user.calories_per_day || null;
                }
            },
            onSubmit(evt) {
                evt.preventDefault();
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return;
                }
                this.isProcessing = true;
                const updatedUser = _.cloneDeep(this.user);
                updatedUser.calories_per_day = parseInt(this.calories_per_day);
                this.$store.dispatch(UPDATE_USER, updatedUser)
                    .then(() => this.$store.dispatch(GET_CURRENT_USER).then(() => this.$emit('updated')))
                    .catch(error => this.showNetworkError(error))
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
