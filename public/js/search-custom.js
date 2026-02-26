/**
 * search-custom.js
 * Handles the logic for Flatpickr modern date/time selectors
 * and Custom Traveler popovers across the site.
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Flatpickr for Date Inputs
    // Flatpickr makes date inputs look native, sleek, and modern cross-browser.
    const dateInputs = document.querySelectorAll('input[type="date"]');
    if (dateInputs.length > 0 && typeof flatpickr !== 'undefined') {
        flatpickr(dateInputs, {
            mode: "range",
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            altInput: true,
            altFormat: "M j - h:i K",
            allowInput: true,
            showMonths: window.innerWidth >= 768 ? 2 : 1,
            disableMobile: "true"
        });
    }

    // 2. Initialize Flatpickr for Time Inputs
    const timeInputs = document.querySelectorAll('input[type="time"]');
    if (timeInputs.length > 0 && typeof flatpickr !== 'undefined') {
        flatpickr(timeInputs, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            disableMobile: "true"
        });
    }

    // 3. Custom Travelers Popover Logic
    const popoverContainer = document.getElementById('traveler-selector-container');
    const popoverDiv = document.getElementById('traveler-popover');
    const displayElement = document.getElementById('search-travelers-display');

    if (popoverContainer && popoverDiv && displayElement) {
        // State variables
        let counts = {
            adults: 2,
            children: 0,
            rooms: 1
        };

        // If it's the restaurant popover instead, state is just party
        const countParty = document.getElementById('count-party');
        if (countParty) {
            counts.party = 2;
        }

        const updateDisplay = () => {
            if (countParty) {
                // Restaurant form
                document.getElementById('count-party').innerText = counts.party;
                displayElement.innerText = `${counts.party} People`;
                document.getElementById('btn-party-min').disabled = counts.party <= 1;
            } else {
                // Stays/Others form
                document.getElementById('count-adults').innerText = counts.adults;
                document.getElementById('count-children').innerText = counts.children;
                document.getElementById('count-rooms').innerText = counts.rooms;

                // Disable minimum buttons appropriately
                document.getElementById('btn-adults-min').disabled = counts.adults <= 1;
                document.getElementById('btn-children-min').disabled = counts.children <= 0;
                document.getElementById('btn-rooms-min').disabled = counts.rooms <= 1;

                // Sync UI string
                let text = `${counts.adults} Adult${counts.adults > 1 ? 's' : ''}`;
                if (counts.children > 0) {
                    text += `, ${counts.children} Child${counts.children > 1 ? 'ren' : ''}`;
                }
                text += ` · ${counts.rooms} Room${counts.rooms > 1 ? 's' : ''}`;
                displayElement.innerText = text;
            }
        };

        // Expose global updater function
        window.updateTraveler = (type, change) => {
            if (type === 'adults') counts.adults = Math.max(1, counts.adults + change);
            if (type === 'children') counts.children = Math.max(0, counts.children + change);
            if (type === 'rooms') counts.rooms = Math.max(1, counts.rooms + change);
            if (type === 'party') counts.party = Math.max(1, counts.party + change);
            updateDisplay();
        };

        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!popoverContainer.contains(e.target)) {
                popoverDiv.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                popoverDiv.classList.add('opacity-0', 'scale-95', '-translate-y-2');
                setTimeout(() => popoverDiv.classList.add('hidden'), 200);
            }
        });

        // Click to toggle
        displayElement.parentElement.addEventListener('click', (e) => {
            // Prevent close body listener
            if (e.target.closest('#traveler-popover') && !e.target.closest('button')) return;

            if (popoverDiv.classList.contains('hidden')) {
                popoverDiv.classList.remove('hidden');
                // Force reflow
                void popoverDiv.offsetWidth;
                popoverDiv.classList.remove('opacity-0', 'scale-95', '-translate-y-2');
                popoverDiv.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            } else {
                popoverDiv.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
                popoverDiv.classList.add('opacity-0', 'scale-95', '-translate-y-2');
                setTimeout(() => popoverDiv.classList.add('hidden'), 200);
            }
        });

        // Init display
        updateDisplay();
    }
});
