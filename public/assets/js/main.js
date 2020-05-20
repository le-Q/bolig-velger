window.addEventListener('load', (event) => {
    const tableRow = document.querySelectorAll('#aprt-info tr')
        .forEach(e => e.addEventListener("click", function () {
            console.log("clicked");
            //document.querySelector('.hotspot-info, .visible').id = "hotspot-hotspot-6-1";
        }));
});