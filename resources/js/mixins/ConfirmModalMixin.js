
export default {
    methods: {
        showConfirmDeletionModal(message) {
            return this.$bvModal.msgBoxConfirm(message, {
                title: 'Confirm Deletion',
                okVariant: 'danger',
                okTitle: 'Delete',
                cancelTitle: 'Cancel',
                footerClass: 'p-2',
                hideHeaderClose: false,
                centered: true
            });
        }
    }
}
