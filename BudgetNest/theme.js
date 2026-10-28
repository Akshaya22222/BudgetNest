// Toggle between light and dark theme
function toggleTheme() {
    let body = document.body;

    if (body.classList.contains("light")) {
        body.classList.remove("light");
        body.classList.add("dark");
        localStorage.setItem("theme", "dark");
    } else {
        body.classList.remove("dark");
        body.classList.add("light");
        localStorage.setItem("theme", "light");
    }
}

// Apply saved theme on page load
window.onload = function() {
    let savedTheme = localStorage.getItem("theme") || "light";
    document.body.classList.add(savedTheme);
}
