<template>
    <div>
        <navbar v-if="!isGuest" />
        <router-view></router-view>

    </div>
</template>

<script>
    import Navbar from './components/Navbar.vue';

    export default {
        name: 'App',
        components: {
            Navbar
        },
        computed: {
            isGuest() {
                return this.$store.getters.isGuest;
            }
        }
    }
</script>

<style scoped>

</style>
