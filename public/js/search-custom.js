/**
 * search-custom.js
 * Handles date selection with Flatpickr and compact traveler selector
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Flatpickr for Date Inputs (Range)
    const rangeInputs = document.querySelectorAll('.date-range-picker');
    if (rangeInputs.length > 0 && typeof flatpickr !== 'undefined') {
        rangeInputs.forEach(input => {
            flatpickr(input, {
                mode: "range",
                dateFormat: "Y-m-d",
                minDate: "today",
                showMonths: 1,
                disableMobile: true,
                onChange: function (selectedDates, dateStr, instance) {
                    const block = input.closest('.search-date-field') || input.closest('.search-field') || input.parentElement;
                    if (!block) return;
                    const checkinDisplay = block.querySelector('.checkin-display');
                    const checkoutDisplay = block.querySelector('.checkout-display');

                    if (selectedDates.length > 0 && checkinDisplay) {
                        checkinDisplay.innerText = instance.formatDate(selectedDates[0], "M j");
                        checkinDisplay.classList.remove('placeholder');
                    }
                    if (selectedDates.length === 2 && checkoutDisplay) {
                        checkoutDisplay.innerText = instance.formatDate(selectedDates[1], "M j");
                        checkoutDisplay.classList.remove('placeholder');
                    } else if (checkoutDisplay) {
                        checkoutDisplay.innerText = 'Add date';
                        checkoutDisplay.classList.add('placeholder');
                    }
                }
            });
        });
    }

    // 1b. Initialize Flatpickr for Date Inputs (Single)
    const singleInputs = document.querySelectorAll('.date-single-picker');
    if (singleInputs.length > 0 && typeof flatpickr !== 'undefined') {
        singleInputs.forEach(input => {
            flatpickr(input, {
                mode: "single",
                dateFormat: "Y-m-d",
                minDate: "today",
                showMonths: 1,
                disableMobile: true,
                onChange: function (selectedDates, dateStr, instance) {
                    const block = input.closest('.search-date-field') || input.closest('.search-field') || input.parentElement;
                    if (!block) return;
                    const display = block.querySelector('.checkin-display');
                    if (selectedDates.length > 0 && display) {
                        display.innerText = instance.formatDate(selectedDates[0], "M j");
                        display.classList.remove('placeholder');
                    }
                }
            });
        });
    }

    // Also handle hp-date-range (homepage uses id="search-date")
    const hpDateInput = document.getElementById('search-date');
    if (hpDateInput && typeof flatpickr !== 'undefined') {
        flatpickr(hpDateInput, {
            mode: "range",
            dateFormat: "Y-m-d",
            minDate: "today",
            showMonths: 1,
            disableMobile: true,
            onChange: function (selectedDates, dateStr, instance) {
                const block = hpDateInput.closest('.search-date-field') || hpDateInput.parentElement;
                if (!block) return;
                const checkinDisplay = block.querySelector('.checkin-display');
                const checkoutDisplay = block.querySelector('.checkout-display');

                if (selectedDates.length > 0 && checkinDisplay) {
                    checkinDisplay.innerText = instance.formatDate(selectedDates[0], "M j");
                    checkinDisplay.classList.remove('placeholder');
                }
                if (selectedDates.length === 2 && checkoutDisplay) {
                    checkoutDisplay.innerText = instance.formatDate(selectedDates[1], "M j");
                    checkoutDisplay.classList.remove('placeholder');
                } else if (checkoutDisplay) {
                    checkoutDisplay.innerText = 'Add date';
                    checkoutDisplay.classList.add('placeholder');
                }
            }
        });
    }



    // 2. Initialize Flatpickr for Time Inputs
    const timeInputs = document.querySelectorAll('#search-time, .time-picker');
    if (timeInputs.length > 0 && typeof flatpickr !== 'undefined') {
        flatpickr(timeInputs, {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: false,
            disableMobile: true,
            onChange: function (selectedDates, dateStr, instance) {
                const block = instance.element.closest('.search-date-field') || instance.element.parentElement;
                if (!block) return;
                const display = block.querySelector('.time-display');
                if (dateStr && display) {
                    display.innerText = instance.formatDate(selectedDates[0], "h:i K");
                    display.classList.remove('placeholder');
                }
            }
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

        // Click to toggle — listen on the whole container (displayElement is deeper in the DOM now)
        popoverContainer.addEventListener('click', (e) => {
            if (popoverDiv.contains(e.target)) return; // don't toggle when clicking inside the popover
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
