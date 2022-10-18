export function authError(error) {
    let errorMessages = [];
    let detailedMessages = [];
    if (error.response.data.message) {
        detailedMessages = [].concat.apply(
            [],
            Object.values(error.response.data.message)
        );
        errorMessages = errorMessages.concat(detailedMessages);
    }

    errorMessages.forEach((message) => {
        let toast = Vue.toasted.show(message, {
            theme: "toasted-primary",
            position: "top-right",
            duration: 5000,
        });
    });
}
