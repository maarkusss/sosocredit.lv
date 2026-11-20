const dropdownToggle = document.getElementById("dropdown-toggle");
const dropdownBackground = document.getElementById("sidebar__language-dropdown");
const dropdownContainer = document.getElementById("dropdown-container");
const dropdown = document.getElementById("dropdown");

if (dropdownToggle && dropdownBackground && dropdownContainer && dropdown) {
    if (dropdownContainer && dropdown && dropdownBackground) {
        dropdownContainer.onclick = () => {
            dropdownToggle.classList.toggle("toggled");

            dropdown.classList.toggle("hidden");
            dropdownBackground.classList.toggle("bg-gray-100");
            dropdownContainer.classList.toggle("border-b");
            dropdownContainer.classList.toggle("text-gray-600");
        };
    }

    const sidebar = document.getElementById("sidebar");
    const sidebarToggle = document.getElementById("sidebar-toggle");
    const sidebarOverlay = document.getElementById("sidebar-overlay");

    if (sidebar && sidebarToggle && sidebarOverlay) {
        sidebarToggle.onclick = () => {
            sidebar.classList.toggle("hidden");
            sidebarOverlay.classList.toggle("hidden");
            document.querySelector("body").classList.toggle("overflow-hidden");
        };

        sidebarOverlay.onclick = () => {
            sidebar.classList.toggle("hidden");
            sidebarOverlay.classList.toggle("hidden");
            document.querySelector("body").classList.toggle("overflow-hidden");
        };

    }
}
