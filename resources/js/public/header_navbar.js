// Header Navbar
const navbarDropdown = document.getElementById("navbarDropdown");
const navbarDropdownList = document.getElementById("navbarDropdownList");

if (navbarDropdownList) {
    // Navbar dropdown listener
    document.addEventListener("click", (e) => {
        let eventTarget = e.target;
        do {
            // If the event target is the dropdown button
            if (eventTarget == navbarDropdown) {
                navbarDropdownList.classList.toggle("hidden");
                return;
            }
            eventTarget = eventTarget.parentNode;
        } while (eventTarget)
        // If the event target is not the dropdown button
        navbarDropdownList.classList.add("hidden");
    });
}
