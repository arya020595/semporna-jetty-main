import { DateTime, Settings } from "luxon";

export const checkDate = (value) => {
    if (!value || new Date(value) == "Invalid Date" || isNaN(new Date(value))) {
        return false;
    }

    return true;
};

export const formatDate = (value) => {
    if (!checkDate(value)) return value;

    Settings.defaultLocale = "en";
    return DateTime.fromISO(value).toLocaleString(DateTime.DATETIME_MED);
};


export const formatDateOnly = (value) => {
    if (!checkDate(value)) return value;

    Settings.defaultLocale = "en";
    return DateTime.fromISO(value).toLocaleString(DateTime.DATE_FULL);
};

export const formatMonth = (value) => {
    if (!value) return "";

    let d = value + "-01";
    Settings.defaultLocale = "en";
    return DateTime.fromISO(d).toFormat('LLLL yyyy');;
};

export const generateArrYear = (startDate, duration) => {
    if (!startDate || !duration) {
        return [];
    }

    let d = new Date(startDate + "-01");
    let startYear = d.getFullYear();
    d.setMonth(d.getMonth() + parseInt(duration));

    let endYear = d.getFullYear();

    let years = [];

    for (let i = startYear; i <= endYear; i++) {
        years.push(i);
    }

    return years;
};

export const generateArrYearQuarter = (startDate, duration) => {
    if (!startDate || !duration) {
        return [];
    }

    let dStart = new Date(startDate + "-01");
    let dEnd = new Date(startDate + "-01");
    dEnd.setMonth(dEnd.getMonth() + parseInt(duration));

    let arrYearQuarter = [];

    let startYear = dStart.getFullYear();
    let endYear = dEnd.getFullYear();

    let month = 0;
    let valDateStart = null;
    let valDateEnd = null;
    for (let i = startYear; i <= endYear; i++) {

        for (let q = 0; q < 4; q ++) {
            month = q * 3;
            valDateStart = new Date(`${i}-${month + 1}-01`);
            valDateEnd = new Date(`${i}-${month + 3}-01`);

            if (dStart < valDateEnd && dEnd > valDateStart) {
                arrYearQuarter.push({
                    id: `${i}-${q+1}`,
                    description: `${i} - Quarter ${q+1}`
                });
            }
        }

    }

    return arrYearQuarter;
};


export const calcCompletionDate = (startDate, duration) => {
    if (!startDate || !duration) {
        return "";
    }

    let d = new Date(startDate + "-01");
    d.setMonth(d.getMonth() + parseInt(duration));

    return d.toLocaleString("default", { month: "long", year: "numeric" });
};
