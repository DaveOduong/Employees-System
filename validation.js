document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("employeeForm");

    form.addEventListener("submit", function (event) {
        // Retrieve field values
        const id = document.getElementById("id").value.trim();
        const name = document.getElementById("employee_name").value.trim();
        const nationalId = document.getElementById("national_id").value.trim();
        const phone = document.getElementById("phone_number").value.trim();
        const email = document.getElementById("email").value.trim();
        const department = document.getElementById("department").value.trim();

        // 1. Check for empty fields
        if (!id || !name || !nationalId || !phone || !email || !department) {
            alert("Error: All fields are required. Please fill in the missing information.");
            event.preventDefault();
            return false;
        }

        // 2. Validate Email Address Format using Regular Expression
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            alert("Error: Please provide a valid email address (e.g., user@example.com).");
            event.preventDefault();
            return false;
        }

        return true;
    });
});