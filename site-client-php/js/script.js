// Change la couleur du menu nav
let pageUrl = window.location.pathname;
let getNav = document.querySelectorAll("nav div ul li a");

for (let i = 0; i < getNav.length; i++) {
    // Récupération des infos de l'URL
    let pageUrlName = pageUrl.split("/");
    let pageUrlLength = pageUrlName.length - 1;

    // Récupération des infos sur les lien
    let pageNav = getNav[i].pathname;
    let pageNavName = pageNav.split("/");
    let pageNavLength = pageNavName.length - 1;
    let pageFinalName = pageNavName[pageNavLength];
    let pageNavExists = pageUrl.includes(pageFinalName);

    // Change le lien des couleurs
    if (pageNavExists == true) {
        getNav[i].style.cssText = "color: rgba(167, 58, 194, 1);";
    }
    else if (pageUrlName[pageUrlLength] == "") {
        getNav[0].style.cssText = "color: rgba(167, 58, 194, 1);";
    }
}
