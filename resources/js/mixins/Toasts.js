
export default {
    methods: {
        showToast(variant = null, title, message) {
            this.$bvToast.toast(message, {
                title: title,
                variant: variant,
                solid: true
            });
        },
        showSuccess(message) {
            this.showToast('success', 'Success', message);
        },
        showWarning(message) {
            this.showToast('warning', 'Warning', message);
        },
        showError(message) {
            this.showToast('danger', 'Error', message);
        },
        showNetworkError(error) {
            this.showToast('danger', 'Network Error', error.body || 'Please try again later.');
        }
    }
}
