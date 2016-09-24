/*
 * Функция скрывает рекламу хостинга
 * через
 */
function hideAdsens() {
    if (document.readyState === "complete") {
        document.getElementById("freewha").style.display = "none";
    }
}

