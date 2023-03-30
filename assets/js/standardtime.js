function standardtime(value, data, type, component, row) {


    value = value.split(':'); // convert to array

    // fetch
    var hours = Number(value[0]);
    var minutes = Number(value[1]);
    var seconds = Number(value[2]);

    // calculate
    var timeValue;

    if (hours > 0 && hours <= 12) {
        timeValue = "" + hours;
    } else if (hours > 12) {
        timeValue = "" + (hours - 12);
    } else if (hours == 0) {
        timeValue = "12";
    }

    timeValue += (minutes < 10) ? ":0" + minutes : ":" + minutes; // get minutes
    timeValue += (hours >= 12) ? " PM" : " AM"; // get AM/PM
    return timeValue;
}