import { debounce } from "lodash";

export const getIntValue = (value) => {
    if (value == '') return 0;
    return !isNaN(value) ? parseInt(value) : 0;
};

export const sumCost = (costYears) => {
    return costYears.reduce((a, b) => getIntValue(a) + getIntValue(b), 0);
};

export const formatNumber = (value) => {
    if (value == '') return 0;

    return !isNaN(value) ? value.toLocaleString() : 0;
}


export const formatCreditCard = debounce((creditCardNumber) => {
    // Remove all non-digit characters
    let formatted = creditCardNumber.replace(/\D/g, "");

    formatted = formatted.substring(0, 16);

    // Add space after every 4 digits
    formatted = formatted.replace(/(.{4})/g, "$1 ").trim("");

    formatted = formatted.replaceAll(" ", "-");

    // Update the model
    form.credit_card_no = formatted;
}, 1);

export const formatNumberOnly = (value) => {
    // Remove all non-digit characters
    if (!value) return "";
    return value.replace(/\D/g, "");
};

export const formatAlphaNumericOnly = (value) => {
    // Remove all non-alphanumeric characters
    if (!value) return "";
    return value.replace(/[^a-zA-Z0-9]/g, "");
};
