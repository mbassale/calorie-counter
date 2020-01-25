<template>
    <b-card>
        <b-form @submit="onSubmit" @reset="onReset">
            <b-form-row>
                <div class="col-auto">
                    <b-form-group label="Start Date" label-for="startDate">
                        <VueDatePicker v-model="startDate" class="form-control" format="YYYY-MM-DD" bootstrap-styling
                                       :disabled="disabled"/>
                    </b-form-group>
                </div>
                <div class="col-auto">
                    <b-form-group label="Start Time" label-for="time" :state="$v.startTime.$error ? false : null">
                        <masked-input v-model="startTime" class="form-control" mask="11:11" placeholder="Time"
                                      :disabled="disabled" :state="$v.startTime.$error ? false : null"/>
                        <b-form-invalid-feedback v-if="$v.startTime.$error" force-show>
                            Invalid time
                        </b-form-invalid-feedback>
                    </b-form-group>
                </div>
                <div class="col-auto">
                    <b-form-group label="End Date" label-for="startDate">
                        <VueDatePicker v-model="endDate" class="form-control" format="YYYY-MM-DD" bootstrap-styling
                                       :disabled="disabled"/>
                    </b-form-group>
                </div>
                <div class="col-auto">
                    <b-form-group label="End Time" label-for="time" :state="$v.endTime.$error ? false : null">
                        <masked-input v-model="endTime" class="form-control" mask="11:11" placeholder="Time"
                                      :disabled="disabled" :state="$v.endTime.$error ? false : null"/>
                        <b-form-invalid-feedback v-if="$v.endTime.$error" force-show>
                            Invalid time
                        </b-form-invalid-feedback>
                    </b-form-group>
                </div>
            </b-form-row>
            <b-button type="submit" variant="primary" :disabled="disabled || $v.$anyError">
                <fa-icon icon="search" /> Search
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
    import {VueDatePicker} from '@mathieustan/vue-datepicker';
    import MaskedInput from 'vue-masked-input';

    const time = (value) => {
        if (_.isEmpty(value)) return true;
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
        name: 'MealSearch',
        components: {
            VueDatePicker,
            MaskedInput
        },
        props: {
            disabled: {
                required: false,
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                startDate: null,
                startTime: null,
                endDate: null,
                endTime: null
            };
        },
        validations: {
            startTime: {
                time
            },
            endTime: {
                time
            }
        },
        methods: {
            onSubmit(evt) {
                evt.preventDefault();
                this.$v.$touch();
                if (this.$v.$invalid) {
                    return;
                }
                this.$emit('search', _.pick(this, ['startDate', 'startTime', 'endDate', 'endTime']));
            },
            onReset(evt) {
                evt.preventDefault();
                _.assign(this, {
                    startDate: null,
                    startTime: null,
                    endDate: null,
                    endTime: null
                });
                this.$emit('reset');
            }
        }
    }
</script>

<style scoped>

</style>
