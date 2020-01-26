import _ from 'lodash';

export default {
    methods: {
        getValidationErrors(error) {
            if (error.response.data) {
                const errorData = error.response.data;
                if (errorData.errors) {
                    return _.reduce(errorData.errors, (result, value, key) => {
                        result[key] = _.isArray(value) ? _.first(value) : value;
                        return result;
                    }, {});
                }
            }
            return null;
        }
    }
}
