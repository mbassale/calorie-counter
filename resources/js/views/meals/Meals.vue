<template>
    <b-container>
        <div class="row">
            <div class="col-auto mr-auto">
                <h1><fa-icon icon="utensils" /> Meals</h1>
            </div>
            <div class="col-auto">
                <b-button type="button" variant="primary" @click="handleCreate">
                    <fa-icon icon="plus" /> New Meal
                </b-button>
                <b-button type="button" variant="secondary" title="Refresh" @click="loadData">
                    <fa-icon v-if="isLoading" icon="spinner" spin />
                    <fa-icon v-else icon="sync" />
                </b-button>
            </div>
        </div>
        <b-table striped hover show-empty responsive="md" :items="orderedMeals" :fields="fields" :busy="isLoading">
            <template v-slot:cell(actions)="data">
                <b-button variant="primary" size="sm" title="Edit" @click="data.toggleDetails"
                          :disabled="disabled || isDeletingId === data.item.id">
                    <fa-icon icon="edit"/>
                </b-button>
                <b-button variant="danger" size="sm" title="Delete"
                          @click="handleDelete(data.item)" :disabled="disabled || isDeletingId === data.item.id">
                    <fa-icon v-if="data.item.id && isDeletingId === data.item.id" icon="spinner" spin/>
                    <fa-icon v-else icon="trash"/>
                </b-button>
            </template>
            <template v-slot:row-details="row">
                <b-card>
                    <meal-form :meal="row.item" @cancel="handleFormCancel(row)" />
                </b-card>
            </template>
            <template v-slot:empty="scope">
                <p class="text-center"><em>No meals to show</em></p>
            </template>
            <template v-slot:emptyfiltered="scope">
                <p class="text-center"><em>No meals found</em></p>
            </template>
        </b-table>
    </b-container>
</template>

<script>
    import _ from 'lodash';
    import moment from 'moment';
    import { mapState } from 'vuex';
    import { DELETE_MEAL, LOAD_MEALS } from '../../store/actions';
    import MealForm from './MealForm.vue';
    import ToastMixin from '../../mixins/ToastMixin';
    import ConfirmModalMixin from '../../mixins/ConfirmModalMixin';

    export default {
        name: 'Meals',
        components: {
            MealForm
        },
        mixins: [ToastMixin, ConfirmModalMixin],
        data() {
            return {
                isLoading: false,
                isCreating: false,
                isDeletingId: null,
                fields: [
                    {
                        key: 'day',
                        label: 'Day',
                        sortable: true,
                        formatter: (value, key, item) => {
                            return moment(item.date).format('dddd');
                        }
                    },
                    {
                        key: 'date',
                        label: 'Date',
                        sortable: true,
                        formatter: (value, key, item) => {
                            return moment(item.date).format('YYYY-MM-DD');
                        }
                    },
                    {
                        key: 'time',
                        label: 'Time',
                        sortable: true,
                        formatter: (value, key, item) => {
                            return moment(item.date).format('H:mm');
                        }
                    },
                    {
                        key: 'name',
                        label: 'Name',
                        sortable: true
                    },
                    {
                        key: 'calories',
                        label: 'Calories',
                        sortable: true
                    },
                    {
                        key: 'actions',
                        label: '',
                        sortable: false
                    }
                ]
            };
        },
        computed: {
            disabled() {
                return this.isLoading;
            },
            orderedMeals() {
                let meals = _.orderBy(this.meals.map(meal => {
                    meal = _.cloneDeep(meal);
                    meal._showDetails = false;
                    return meal;
                }), ['date'], ['desc']);
                if (this.isCreating) {
                    meals.unshift({
                        _showDetails: true,
                        id: null,
                        user_id: null,
                        name: null,
                        date: moment().format('YYYY-MM-DD HH:mm:ss'),
                        calories: null
                    });
                }
                return meals;
            },
            ...mapState(['meals'])
        },
        watch: {
            meals() {
                this.isCreating = false;
            }
        },
        mounted() {
            this.$nextTick(() => this.loadData());
        },
        methods: {
            loadData() {
                this.isLoading = true;
                return this.$store.dispatch(LOAD_MEALS)
                    .catch(error => this.showNetworkError(error))
                    .finally(() => this.isLoading = false);
            },
            handleCreate() {
                this.isCreating = true;
            },
            handleDelete(meal) {
                return this.showConfirmDeletionModal('Are you sure to delete this meal?')
                    .then(value => {
                        if (value) {
                            this.isDeletingId = meal.id;
                            return this.$store.dispatch(DELETE_MEAL, meal)
                                .then(() => this.showSuccess('Meal Deleted'))
                                .catch(error => this.showNetworkError(error))
                                .finally(() => this.isDeletingId = null);
                        }
                    });
            },
            handleFormCancel(row) {
                row.toggleDetails();
                if (!row.item.id && this.isCreating) {
                    this.isCreating = false;
                }
            }
        }
    }
</script>

<style scoped>

</style>
