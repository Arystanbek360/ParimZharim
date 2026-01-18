document.addEventListener("DOMContentLoaded", function() {
    // Locate the elements we will be working with
    const novaContainer = document.getElementById("nova");
    const header = novaContainer.children[0].children[0];
    const sidebar = novaContainer.children[1].children[0];
    const content = novaContainer.children[1].children[1];

    // Create the toggle button
    const toggleButton = document.createElement("button");
    toggleButton.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    `;

    toggleButton.classList.add("toggle-sidebar-button"); // Add class for potential styling
    toggleButton.classList.add("opened"); // Initial button state is 'opened'

    header.classList.add("toggle-sidebar-button-container"); // Add class to the header

    // Add the button to the container, right after the sidebar
    novaContainer.children[0].insertBefore(toggleButton, header.nextSibling);
    // Function to toggle sidebar and content
    function toggleSidebar() {
        if (toggleButton.classList.contains("closed")) {
            // If the sidebar is closed, we open it
            sidebar.classList.remove("hidden-sidebar");
            content.classList.remove("full-width");
            toggleButton.classList.replace("closed", "opened");
        } else {
            // If the sidebar is opened, we close it
            sidebar.classList.add("hidden-sidebar");
            content.classList.add("full-width");
            toggleButton.classList.replace("opened", "closed");
        }
    }
    // Attach click event to the button
    toggleButton.addEventListener("click", toggleSidebar);
});
