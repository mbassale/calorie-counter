import _ from 'lodash';

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
            const message = _.isObject(error.response.data) ? (error.response.data.message || null) : error.response.data;
            this.showToast('danger', 'Network Error', message || 'Please try again later.');
        }
    }
}
