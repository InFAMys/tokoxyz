// import Alpine from "alpinejs";

// window.Alpine = Alpine;

// Alpine.start();

function copyResi() {
    const text = document.getElementById("NoResi").innerText;

    navigator.clipboard
        .writeText(text)
        .catch((err) => {
            console.error(err);
        });
}
//
