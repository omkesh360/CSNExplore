/**
 * search-custom.js
 * Handles date selection with Flatpickr and compact traveler selector
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Flatpickr for Date Inputs with proper date range
    const dateInputs = document.querySelectorAll('input[type="date"]');
    if (dateInputs.length > 0 && typeof flatpickr !== 'undefined') {
        dateInputs.forEach(input => {
            flatpickr(input, {
                mode: "range",
                dateFormat: "Y-m-d",
                minDate: "today",
                altInput: true,
                altFormat: "M j, Y",
                allowInput: false,
                showMonths: window.innerWidth >= 768 ? 2 : 1,
                disableMobile: false,
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        console.log('Date range selected:', dateStr);
                    }
                }
            });
        });
    }

    // 2. Initialize Flatpickr for Time Inputs
    const timeInputs = document.querySelectorAll('input[type="time"]');
    if (timeInputs.length > 0 && typeof flatpickr !== 'undefined') {
        flatpickr(timeInputs, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: false,
            disableMobile: false
        });
    }

    // 3. Compact Travelers Popover Logic with Child Option
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

        // Check if it's restaurant page (party selector)
        const countParty = document.getElementById('count-party');
        if (countParty) {
            counts = { party: 2 };
        }

        const updateDisplay = () => {
            if (countParty) {
                // Restaurant form
                document.getElementById('count-party').innerText = counts.party;
                displayElement.innerText = `${counts.party} ${counts.party === 1 ? 'Person' : 'People'}`;
                document.getElementById('btn-party-min').disabled = counts.party <= 1;
            } else {
                // Stays/Others form
                document.getElementById('count-adults').innerText = counts.adults;
                document.getElementById('count-children').innerText = counts.children;
                document.getElementById('count-rooms').innerText = counts.rooms;

                // Disable minimum buttons
                document.getElementById('btn-adults-min').disabled = counts.adults <= 1;
                document.getElementById('btn-children-min').disabled = counts.children <= 0;
                document.getElementById('btn-rooms-min').disabled = counts.rooms <= 1;

                // Compact display text
                let parts = [];
                parts.push(`${counts.adults} Adult${counts.adults > 1 ? 's' : ''}`);
                if (counts.children > 0) {
                    parts.push(`${counts.children} Child${counts.children > 1 ? 'ren' : ''}`);
                }
                parts.push(`${counts.rooms} Room${counts.rooms > 1 ? 's' : ''}`);
                displayElement.innerText = parts.join(' · ');
            }
        };

        // Expose global updater function
        window.updateTraveler = (type, change) => {
            if (type === 'adults') counts.adults = Math.max(1, Math.min(20, counts.adults + change));
            if (type === 'children') counts.children = Math.max(0, Math.min(10, counts.children + change));
            if (type === 'rooms') counts.rooms = Math.max(1, Math.min(10, counts.rooms + change));
            if (type === 'party') counts.party = Math.max(1, Math.min(50, counts.party + change));
            updateDisplay();
        };

        // Click outside to close
        document.addEventListener('click', (e) => {
            if (!popoverContainer.contains(e.target)) {
                closePopover();
            }
        });

        // Click to toggle
        displayElement.parentElement.addEventListener('click', (e) => {
            e.stopPropagation();
            
            if (popoverDiv.classList.contains('hidden')) {
                openPopover();
            } else {
                closePopover();
            }
        });

        function openPopover() {
            popoverDiv.classList.remove('hidden');
            setTimeout(() => {
                popoverDiv.classList.remove('opacity-0', 'scale-95', '-translate-y-2');
                popoverDiv.classList.add('opacity-100', 'scale-100', 'translate-y-0');
            }, 10);
        }

        function closePopover() {
            popoverDiv.classList.remove('opacity-100', 'scale-100', 'translate-y-0');
            popoverDiv.classList.add('opacity-0', 'scale-95', '-translate-y-2');
            setTimeout(() => popoverDiv.classList.add('hidden'), 200);
        }

        // Init display
        updateDisplay();
    }
});
