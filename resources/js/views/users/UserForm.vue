<template>
    <b-form @submit="onSubmit" @reset="onReset">
        <b-form-group label="Role" label-for="role_id">
            <b-form-select id="role_id" v-model="role_id" :options="roles" required />
        </b-form-group>

        <b-form-group label="First Name" label-for="first_name">
            <b-form-input id="first_name" v-model="first_name" required placeholder="Enter first name" />
        </b-form-group>

        <b-form-group label="Last Name" label-for="last_name">
            <b-form-input id="last_name" v-model="last_name" required placeholder="Enter last name" />
        </b-form-group>

        <b-button type="submit" variant="primary">
            <fa-icon icon="save" /> Save
        </b-button>
        <b-button type="reset" variant="danger">
            <fa-icon icon="eraser" /> Reset
        </b-button>
    </b-form>
</template>

<script>
    export default {
        name: 'UserForm',
        data() {
            return {
                isLoading: false,
                isProcessing: false,
                role_id: null,
                first_name: null,
                last_name: null
            };
        },
        methods: {

        }
    }
</script>

<style scoped>

</style>
