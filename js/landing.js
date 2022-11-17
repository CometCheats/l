var textPlace = 0;
var text = "esonance Cheats";

function doLogoText(){
    var logoText = document.getElementById("logo");
    if (textPlace < 15) {
        logoText.innerHTML += text[textPlace];
        textPlace++;
        setTimeout(doLogoText, 100);
    }
}