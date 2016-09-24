function getRandomInt(min, max) {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}
function func1() {
    var val = getRandomInt(100, 150);
    $(".digit").text(val);
}

/*
 *Description:
 *function hide adssens free hosting 
*/
function hideAdsens() {
    if (document.readyState === "complete") {
        document.getElementById("freewha").style.display = "none";
    }
}