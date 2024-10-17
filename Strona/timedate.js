function gettheDate() {
    ThisDays = new Date();
    ThisDate = "" + (ThisDays.getMonth() + 1) + "/" + ThisDays.getDate() + "/" + (ThisDays.getYear() - 100);
    document.getElementById("data").innerHTML = ThisDate;
}

var ID_timer = null;
var TimeIsRunning = false;

function stopclock() {
    if (TimeIsRunning) {
        clearTimeout(ID_timer);
        TimeIsRunning = false;
    }
}

function startclock() {
    stopclock();
    gettheDate();
    showtime();
}

function showtime() {
    var DateNow = new Date();
    var TimeHours = DateNow.getHours();
    var TimeMinutes = DateNow.getMinutes();
    var TimeSeconds = DateNow.getSeconds();
    var Time = "" + ((TimeHours > 12) ? TimeHours - 12 : TimeHours);
    Time += ((TimeMinutes < 10) ? ":0" : ":") + TimeMinutes;
    Time += ((TimeSeconds < 10) ? ":0" : ":") + TimeSeconds;
    Time += (TimeHours >= 12) ? " P.M." : " A.M.";
    document.getElementById("zegarek").innerHTML = Time;
    ID_timer = setTimeout("showtime()", 1000);
    TimeIsRunning = true;
}