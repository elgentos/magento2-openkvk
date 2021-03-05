function initializeOpenKvk() {
    "use strict";

    return {
        suggestUrl: BASE_URL + 'openkvk/fetch/suggest/',
        detailsUrl: BASE_URL + 'openkvk/fetch/detail/',
        headers: {
            'x-requested-with': 'XMLHttpRequest'
        },
        open: false,
        errorMessage: null,
        suggestions: [],

        /**
         * Fetch the suggestions based on the entered data.
         */
        fetchSuggestions: function() {
            const formElement = document.querySelector("#openkvk-fetch-form"),
                postData      = new FormData(formElement);

            fetch(
                this.suggestUrl,
                {
                    method: 'POST',
                    body: postData,
                    headers: this.headers
                }
            )
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    this.errorMessage = data.error;
                } else if (data.length) {
                    this.suggestions  = data;
                    this.open         = true;
                    this.errorMessage = null;
                }
            })
            .catch(function(error) {
                console.warn(error);
            });
        },

        /**
         * Set the values of the form based on the clicked
         * suggestion.
         *
         * @param {Object} item
         */
        selectSuggestion: function(item) {
            this.open = false;

            for (let key in item) {
                let inputField = document.querySelector('.form-address-edit input#' + key);

                if (inputField) {
                    inputField.readOnly = true;
                    inputField.value    = item[key];
                }
            }
        }
    }
}
