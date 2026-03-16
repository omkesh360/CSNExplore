/**
 * Booking Popup Component
 * Handles "Book Now" button clicks and booking form submission.
 * Auto-reads location, date, and time from the page search box.
 * Uses 100% inline styles — no Tailwind dependency.
 */

(function() {
    'use strict';

    let currentBookingContext = { servicePage: '', listingId: null, listingName: '' };

    document.addEventListener('DOMContentLoaded', function() {
        createBookingPopup();
        attachBookNowListeners();
    });

    // ── Read search box values from the current page ──────────────────────────
    function getSearchContext() {
        const locationEl = document.getElementById('search-location');
        // If the input is hidden (locked destination mode), use the locked label text
        let location = '';
        if (locationEl) {
            if (locationEl.style.display === 'none') {
                const lockedLabel = document.getElementById('dest-locked-label');
                location = lockedLabel ? lockedLabel.textContent.trim() : '';
            } else {
                location = locationEl.value.trim();
            }
        }
        // Default to CSN if no location found on detail pages (no search bar)
        if (!location) {
            location = 'Aurangabad / Chhatrapati Sambhajinagar';
        }

        let dateFrom = '', dateTo = '', time = '';

        const dateInputs = document.querySelectorAll('.date-range-picker, .date-single-picker, #search-date, .search-date-overlay');
        for (const el of dateInputs) {
            if (el._flatpickr && el._flatpickr.selectedDates.length) {
                const fp = el._flatpickr;
                dateFrom = fp.formatDate(fp.selectedDates[0], 'Y-m-d');
                if (fp.selectedDates.length > 1) dateTo = fp.formatDate(fp.selectedDates[1], 'Y-m-d');
                break;
            }
        }

        if (!dateFrom) {
            const checkinDisplay = document.querySelector('.checkin-display');
            if (checkinDisplay && !checkinDisplay.classList.contains('placeholder')) dateFrom = checkinDisplay.innerText.trim();
        }
        if (!dateTo) {
            const checkoutDisplay = document.querySelector('.checkout-display');
            if (checkoutDisplay && !checkoutDisplay.classList.contains('placeholder')) dateTo = checkoutDisplay.innerText.trim();
        }

        const timeDisplay = document.querySelector('.time-display');
        if (timeDisplay) time = timeDisplay.innerText.trim();
        const timeInput = document.getElementById('search-time');
        if (timeInput && timeInput._flatpickr && timeInput._flatpickr.selectedDates.length) {
            time = timeInput._flatpickr.formatDate(timeInput._flatpickr.selectedDates[0], 'h:i K');
        }

        return { location, dateFrom, dateTo, time };
    }

    // ── Highlight missing search fields ──────────────────────────────────────
    function highlightMissingSearchField(type) {
        if (type === 'location') {
            const el = document.getElementById('search-location');
            if (el) {
                el.style.outline = '2px solid #f87171';
                el.focus();
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => { el.style.outline = ''; }, 3000);
            }
        }
        if (type === 'date') {
            const dateField = document.querySelector('.search-date-field');
            if (dateField) {
                dateField.style.outline = '2px solid #f87171';
                dateField.style.borderRadius = '12px';
                dateField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => { dateField.style.outline = ''; dateField.style.borderRadius = ''; }, 3000);
            }
        }
    }

    // ── Build popup HTML ──────────────────────────────────────────────────────
    function createBookingPopup() {
        const inp = 'width:100%;padding:8px 11px;border:1.5px solid #e5e7eb;border-radius:9px;font-size:13px;outline:none;background:#f9fafb;box-sizing:border-box;font-family:inherit;';
        const lbl = 'display:block;font-size:10px;font-weight:700;color:#6b7280;text-transform:uppercase;letter-spacing:0.7px;margin-bottom:4px;';
        const sub = 'width:100%;background:#0035a0;color:#fff;font-weight:700;font-size:14px;padding:11px 18px;border:none;border-radius:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:7px;box-shadow:0 4px 16px rgba(0,53,160,0.28);';

        const html = `
<div id="booking-popup" style="position:fixed;inset:0;z-index:9999;display:none;">
  <div style="position:absolute;inset:0;background:rgba(0,0,0,0.55);" onclick="closeBookingPopup()"></div>
  <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;padding:10px;">

    <div style="background:#fff;border-radius:18px;box-shadow:0 20px 60px rgba(0,0,0,0.26);width:100%;max-width:520px;position:relative;display:flex;flex-direction:column;max-height:calc(100vh - 20px);overflow:hidden;">

      <!-- Close -->
      <button onclick="closeBookingPopup()" title="Close"
        style="position:absolute;top:10px;right:10px;width:30px;height:30px;border-radius:50%;background:#f3f4f6;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:30;">
        <span class="material-symbols-outlined" style="font-size:16px;color:#374151;line-height:1;">close</span>
      </button>

      <!-- Header: listing image OR plain title -->
      <div id="bss-listing-header" style="display:none;flex-shrink:0;">
        <div style="position:relative;width:100%;height:110px;overflow:hidden;border-radius:18px 18px 0 0;">
          <img id="bss-listing-img" src="" alt="" style="width:100%;height:100%;object-fit:cover;">
          <div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.7) 0%,transparent 60%);"></div>
          <div style="position:absolute;bottom:10px;left:14px;right:44px;">
            <div style="font-size:9px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:rgba(255,255,255,0.65);margin-bottom:2px;">Booking Request</div>
            <div id="bss-listing-title" style="font-size:16px;font-weight:900;color:#fff;line-height:1.2;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"></div>
          </div>
        </div>
      </div>

      <div id="bss-no-listing-header" style="flex-shrink:0;padding:16px 18px 0;">
        <div style="font-size:9px;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#9ca3af;margin-bottom:2px;">Booking Request</div>
        <div style="font-size:18px;font-weight:900;color:#111827;line-height:1.2;" id="bss-no-listing-title">Book Now</div>
      </div>

      <!-- Scrollable form body -->
      <div style="overflow-y:auto;flex:1;padding:14px 18px 18px;">

        <!-- Missing inputs section (location only) -->
        <div id="booking-missing-inputs" style="display:none;margin-bottom:10px;padding:10px 12px;background:#f0f5ff;border-radius:10px;border:1px solid #dbeafe;">
          <div style="font-size:10px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:0.7px;margin-bottom:6px;">Add Missing Details</div>
          <div id="bmi-location-wrap" style="display:none;">
            <label style="${lbl}">Location</label>
            <div style="position:relative;">
              <span class="material-symbols-outlined" style="position:absolute;left:9px;top:50%;transform:translateY(-50%);font-size:15px;color:#9ca3af;pointer-events:none;">location_on</span>
              <input type="text" id="popup-location-input" placeholder="e.g. Ellora Caves" autocomplete="off"
                style="width:100%;padding:8px 10px 8px 30px;background:#fff;border:1.5px solid #d1d5db;border-radius:9px;color:#111827;font-size:13px;outline:none;box-sizing:border-box;font-family:inherit;"
                onfocus="this.style.borderColor='#0035a0';" onblur="this.style.borderColor='#d1d5db';"
                oninput="window._bmiLocationInput(this.value)">
              <div id="popup-location-dropdown" style="display:none;position:absolute;top:calc(100% + 4px);left:0;right:0;background:#fff;border-radius:9px;box-shadow:0 8px 24px rgba(0,0,0,0.15);max-height:160px;overflow-y:auto;z-index:100;border:1px solid #e5e7eb;"></div>
            </div>
          </div>
        </div>

        <form id="booking-form">
          <!-- Row 1: Name full width -->
          <div style="margin-bottom:10px;">
            <label style="${lbl}">Full Name *</label>
            <input type="text" id="booking-name" required placeholder="Your full name" style="${inp}"
              onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
              onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
          </div>

          <!-- Row 2: Phone + Email -->
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
            <div>
              <label style="${lbl}">Phone *</label>
              <input type="tel" id="booking-phone" required pattern="^\\+[0-9\\s\\-]{7,20}$" placeholder="+91 98765 43210" style="${inp}"
                onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
              <p id="booking-phone-error" style="display:none;font-size:10px;color:#dc2626;font-weight:600;margin-top:2px;"></p>
            </div>
            <div>
              <label style="${lbl}">Email *</label>
              <input type="email" id="booking-email" required placeholder="your@email.com" style="${inp}"
                onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
            </div>
          </div>

          <!-- Row 3: Date(s) + People -->
          <div id="booking-date-row" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:10px;">
            <!-- Single date (default — replaced for stays/car) -->
            <div id="booking-single-date-wrap">
              <label style="${lbl}">Preferred Date *</label>
              <input type="date" id="booking-date" style="${inp}"
                onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
            </div>
            <!-- Check-in (stays / car rentals) -->
            <div id="booking-checkin-wrap" style="display:none;">
              <label style="${lbl}">Check-in / Pick-up *</label>
              <input type="date" id="booking-checkin" style="${inp}"
                onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
            </div>
            <!-- Check-out (stays / car rentals) -->
            <div id="booking-checkout-wrap" style="display:none;">
              <label style="${lbl}">Check-out / Drop-off *</label>
              <input type="date" id="booking-checkout" style="${inp}"
                onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
            </div>
            <div>
              <label style="${lbl}">No. of People *</label>
              <input type="number" id="booking-people" required min="1" value="1" style="${inp}"
                onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
                onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';">
            </div>
          </div>

          <!-- Rental type (car pages only) -->
          <div id="booking-rental-type-wrap" style="display:none;margin-bottom:10px;">
            <label style="${lbl}">Rental Type *</label>
            <div style="display:flex;gap:8px;margin-top:3px;">
              <label style="flex:1;display:flex;align-items:center;gap:6px;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:9px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;background:#f9fafb;">
                <input type="radio" name="rental-type" id="rental-self-drive" value="Self Drive" style="accent-color:#0035a0;" checked>
                <span class="material-symbols-outlined" style="font-size:16px;color:#0035a0;">directions_car</span>
                Self Drive
              </label>
              <label style="flex:1;display:flex;align-items:center;gap:6px;padding:8px 12px;border:1.5px solid #e5e7eb;border-radius:9px;cursor:pointer;font-size:13px;font-weight:600;color:#374151;background:#f9fafb;">
                <input type="radio" name="rental-type" id="rental-with-driver" value="With Driver" style="accent-color:#0035a0;">
                <span class="material-symbols-outlined" style="font-size:16px;color:#0035a0;">person</span>
                With Driver
              </label>
            </div>
          </div>

          <!-- Notes -->
          <div style="margin-bottom:12px;">
            <label style="${lbl}">Notes <span style="font-weight:400;text-transform:none;letter-spacing:0;">(optional)</span></label>
            <textarea id="booking-notes" rows="2" placeholder="Any special requests?" style="${inp}resize:none;"
              onfocus="this.style.borderColor='#0035a0';this.style.background='#fff';"
              onblur="this.style.borderColor='#e5e7eb';this.style.background='#f9fafb';"></textarea>
          </div>

          <!-- Submit -->
          <button type="submit" id="booking-submit-btn" style="${sub}"
            onmouseover="this.style.background='#002a80';" onmouseout="this.style.background='#0035a0';">
            <span class="material-symbols-outlined" style="font-size:17px;">send</span>
            Submit Booking Request
          </button>
          <p style="font-size:11px;color:#9ca3af;text-align:center;margin-top:8px;">We'll contact you within 4 hours to confirm</p>
        </form>
      </div>

    </div>
  </div>
</div>`;

        document.body.insertAdjacentHTML('beforeend', html);
        document.getElementById('booking-form').addEventListener('submit', handleBookingSubmit);
        document.getElementById('booking-phone').addEventListener('input', function() {
            this.style.borderColor = '#e5e7eb';
            const err = document.getElementById('booking-phone-error');
            if (err) err.style.display = 'none';
        });
    }

    function attachBookNowListeners() {
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-book-now]');
            if (btn) {
                e.preventDefault();
                e.stopPropagation();
                // Grab listing name/image from page at click time (populated by detail-loader.js)
                const pageTitle = document.getElementById('item-title') || document.getElementById('item-title-desktop');
                const pageImg   = document.getElementById('item-main-image');
                const pageDesc  = document.getElementById('item-description');
                currentBookingContext = {
                    servicePage:  btn.dataset.servicePage || window.location.pathname,
                    listingId:    btn.dataset.listingId   || null,
                    listingName:  btn.dataset.listingName || (pageTitle ? pageTitle.textContent.trim() : ''),
                    listingImage: btn.dataset.listingImage || (pageImg ? pageImg.src : ''),
                    listingDesc:  btn.dataset.listingDesc  || (pageDesc ? pageDesc.textContent.trim().slice(0, 120) + '…' : '')
                };
                openBookingPopup();
            }
        });
    }

    function openBookingPopup() {
        const popup = document.getElementById('booking-popup');
        if (!popup) return;

        popup.style.display = 'block';
        document.body.style.overflow = 'hidden';

        // Listing image header
        const listingHeader    = document.getElementById('bss-listing-header');
        const noListingHeader  = document.getElementById('bss-no-listing-header');
        const listingImg       = document.getElementById('bss-listing-img');
        const listingTitle     = document.getElementById('bss-listing-title');
        const listingDesc      = document.getElementById('bss-listing-desc');

        const imgSrc  = currentBookingContext.listingImage || '';
        const name    = currentBookingContext.listingName  || '';
        const desc    = currentBookingContext.listingDesc  || '';

        if (imgSrc && listingHeader) {
            if (listingImg)  { listingImg.src = imgSrc; listingImg.alt = name; }
            if (listingTitle) listingTitle.textContent = name || 'Booking Request';
            if (listingDesc)  listingDesc.textContent  = desc;
            listingHeader.style.display   = 'block';
            if (noListingHeader) noListingHeader.style.display = 'none';
        } else {
            if (listingHeader)   listingHeader.style.display   = 'none';
            if (noListingHeader) {
                noListingHeader.style.display = 'block';
                const titleEl = document.getElementById('bss-no-listing-title');
                if (titleEl) titleEl.textContent = name ? 'Booking for: ' + name : 'Book Now';
            }
        }

        // Read search context — pre-fill dates if available from search bar
        const ctx = getSearchContext();

        // Determine if this is a stays or car-rental page (needs check-in + check-out)
        const sp = currentBookingContext.servicePage || window.location.pathname;
        const isDualDate = /stay|car-rental|bike-rental/i.test(sp);

        const singleWrap   = document.getElementById('booking-single-date-wrap');
        const checkinWrap  = document.getElementById('booking-checkin-wrap');
        const checkoutWrap = document.getElementById('booking-checkout-wrap');
        const singleInput  = document.getElementById('booking-date');
        const checkinInput = document.getElementById('booking-checkin');
        const checkoutInput= document.getElementById('booking-checkout');

        if (isDualDate) {
            // Show check-in / check-out, hide single date
            if (singleWrap)   singleWrap.style.display   = 'none';
            if (checkinWrap)  checkinWrap.style.display  = 'block';
            if (checkoutWrap) checkoutWrap.style.display = 'block';
            if (singleInput)  singleInput.required = false;
            if (checkinInput) {
                checkinInput.required = true;
                if (ctx.dateFrom && /^\d{4}-\d{2}-\d{2}$/.test(ctx.dateFrom)) checkinInput.value = ctx.dateFrom;
            }
            if (checkoutInput) {
                checkoutInput.required = true;
                if (ctx.dateTo && /^\d{4}-\d{2}-\d{2}$/.test(ctx.dateTo)) checkoutInput.value = ctx.dateTo;
            }
            // Update date row to 3 columns when both date fields + people are shown
            const dateRow = document.getElementById('booking-date-row');
            if (dateRow) dateRow.style.gridTemplateColumns = '1fr 1fr 1fr';
        } else {
            // Single date mode
            if (singleWrap)   singleWrap.style.display   = 'block';
            if (checkinWrap)  checkinWrap.style.display  = 'none';
            if (checkoutWrap) checkoutWrap.style.display = 'none';
            if (singleInput)  singleInput.required = true;
            if (checkinInput) checkinInput.required = false;
            if (checkoutInput) checkoutInput.required = false;
            if (singleInput && ctx.dateFrom && /^\d{4}-\d{2}-\d{2}$/.test(ctx.dateFrom)) {
                singleInput.value = ctx.dateFrom;
            }
            const dateRow = document.getElementById('booking-date-row');
            if (dateRow) dateRow.style.gridTemplateColumns = '1fr 1fr';
        }

        // Only show missing-inputs section if location is truly unknown
        // (location now defaults to CSN so this will rarely show)
        const missingSection = document.getElementById('booking-missing-inputs');
        const locWrap = document.getElementById('bmi-location-wrap');
        if (missingSection && locWrap) {
            missingSection.style.display = 'none';
            locWrap.style.display = 'none';
        }

        currentBookingContext._searchLocation = ctx.location;
        currentBookingContext._searchDate = ctx.dateFrom + (ctx.dateTo ? ' to ' + ctx.dateTo : '');
        currentBookingContext._searchTime = ctx.time;

        setTimeout(() => document.getElementById('booking-name')?.focus(), 100);

        // Show rental type selector only on car rental pages
        const rentalTypeWrap = document.getElementById('booking-rental-type-wrap');
        if (rentalTypeWrap) {
            const isCar = /car-rental/i.test(window.location.pathname) || /car-rental/i.test(currentBookingContext.servicePage);
            rentalTypeWrap.style.display = isCar ? 'block' : 'none';
        }
    }

    window.closeBookingPopup = function() {
        const popup = document.getElementById('booking-popup');
        if (popup) {
            popup.style.display = 'none';
            document.body.style.overflow = '';
            document.getElementById('booking-form')?.reset();
            const phoneInput = document.getElementById('booking-phone');
            const phoneError = document.getElementById('booking-phone-error');
            if (phoneInput) phoneInput.style.borderColor = '#e5e7eb';
            if (phoneError) phoneError.style.display = 'none';
            // Reset listing header
            const lh = document.getElementById('bss-listing-header');
            if (lh) lh.style.display = 'none';
            const nlh = document.getElementById('bss-no-listing-header');
            if (nlh) nlh.style.display = 'none';
            const limg = document.getElementById('bss-listing-img');
            if (limg) limg.src = '';
            // Reset date fields
            const ci = document.getElementById('booking-checkin');
            if (ci) ci.value = '';
            const co = document.getElementById('booking-checkout');
            if (co) co.value = '';
            const ms = document.getElementById('booking-missing-inputs');
            if (ms) ms.style.display = 'none';
            const locWrap = document.getElementById('bmi-location-wrap');
            if (locWrap) locWrap.style.display = 'none';
            const popLoc = document.getElementById('popup-location-input');
            if (popLoc) popLoc.value = '';
            const popDrop = document.getElementById('popup-location-dropdown');
            if (popDrop) { popDrop.style.display = 'none'; popDrop.innerHTML = ''; }
        }
    };

    async function handleBookingSubmit(e) {
        e.preventDefault();

        const ctx = getSearchContext();

        // Fallback: check popup inline location input if search box was empty
        const popupLoc = (document.getElementById('popup-location-input')?.value || '').trim();
        if (!ctx.location && popupLoc) ctx.location = popupLoc;

        // Resolve dates — dual mode (stays/car) or single
        const checkinVal  = (document.getElementById('booking-checkin')?.value  || '').trim();
        const checkoutVal = (document.getElementById('booking-checkout')?.value || '').trim();
        const singleVal   = (document.getElementById('booking-date')?.value     || '').trim();
        const isDual = document.getElementById('booking-checkin-wrap')?.style.display !== 'none';

        const bookingDate    = isDual ? checkinVal  : singleVal;
        const bookingDateEnd = isDual ? checkoutVal : '';

        // Sync context
        currentBookingContext._searchLocation = ctx.location;
        currentBookingContext._searchDate = bookingDate + (bookingDateEnd ? ' to ' + bookingDateEnd : '');

        const submitBtn = document.getElementById('booking-submit-btn');
        const originalHTML = submitBtn.innerHTML;

        const fullPhone = document.getElementById('booking-phone').value.trim();
        const phoneInput = document.getElementById('booking-phone');
        const phoneError = document.getElementById('booking-phone-error');

        if (!fullPhone.startsWith('+')) {
            phoneInput.style.borderColor = '#dc2626';
            if (phoneError) {
                phoneError.textContent = 'Phone must start with + and country code (e.g. +91 98765 43210)';
                phoneError.style.display = 'block';
            }
            phoneInput.focus();
            return;
        } else {
            phoneInput.style.borderColor = '#e5e7eb';
            if (phoneError) phoneError.style.display = 'none';
        }

        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;animation:spin 1s linear infinite;">progress_activity</span> Submitting...';

        let notes = document.getElementById('booking-notes').value.trim();
        const contextParts = [];
        if (currentBookingContext._searchLocation) contextParts.push('Location: ' + currentBookingContext._searchLocation);
        if (bookingDate) contextParts.push(isDual ? 'Check-in: ' + bookingDate : 'Date: ' + bookingDate);
        if (bookingDateEnd) contextParts.push('Check-out: ' + bookingDateEnd);
        if (currentBookingContext._searchTime) contextParts.push('Time: ' + currentBookingContext._searchTime);
        // Include rental type for car bookings
        const rentalTypeWrap = document.getElementById('booking-rental-type-wrap');
        if (rentalTypeWrap && rentalTypeWrap.style.display !== 'none') {
            const selectedType = document.querySelector('input[name="rental-type"]:checked');
            if (selectedType) contextParts.push('Rental Type: ' + selectedType.value);
        }
        // Include drop-off location if present
        const dropLoc = document.getElementById('search-drop-location');
        if (dropLoc && dropLoc.value.trim()) contextParts.push('Drop-off: ' + dropLoc.value.trim());
        if (contextParts.length) notes = (notes ? notes + '\n\n' : '') + '[Search Context]\n' + contextParts.join('\n');

        const formData = {
            full_name: document.getElementById('booking-name').value.trim(),
            phone: fullPhone,
            email: document.getElementById('booking-email').value.trim(),
            booking_date: bookingDate,
            booking_date_end: bookingDateEnd || undefined,
            number_of_people: parseInt(document.getElementById('booking-people').value) || 1,
            notes: notes,
            service_page: currentBookingContext.servicePage,
            listing_id: currentBookingContext.listingId,
            listing_name: currentBookingContext.listingName
        };

        try {
            const response = await fetch('/api/bookings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            });

            let result;
            try { result = await response.json(); } catch(je) { result = {}; }

            if (response.ok && result.success) {
                showBookingToast('Booking request submitted! We\'ll contact you soon.', 'success');
                setTimeout(() => closeBookingPopup(), 1500);
            } else {
                throw new Error(result.error || result.message || 'Failed to submit booking');
            }
        } catch (error) {
            console.error('Booking error:', error);
            showBookingToast(error.message, 'error');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHTML;
        }
    }

    function showBookingToast(message, type) {
        let toast = document.getElementById('booking-toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'booking-toast';
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:10000;padding:14px 20px;border-radius:12px;color:#fff;font-weight:700;font-size:14px;box-shadow:0 8px 24px rgba(0,0,0,0.2);transition:all 0.3s;transform:translateY(80px);opacity:0;max-width:340px;line-height:1.4;';
            document.body.appendChild(toast);
        }
        toast.style.background = type === 'success' ? '#16a34a' : '#dc2626';
        toast.textContent = message;
        setTimeout(() => { toast.style.transform = 'translateY(0)'; toast.style.opacity = '1'; }, 10);
        setTimeout(() => { toast.style.transform = 'translateY(80px)'; toast.style.opacity = '0'; }, 4000);
    }

    // ── Popup inline location autocomplete ───────────────────────────────────
    const BMI_PLACES = ["Ajanta Caves","Ellora Caves","Kailasa Temple","Daulatabad Fort","Bibi Ka Maqbara","Aurangabad Caves","Sambhajinagar Caves","Panchakki","Soneri Mahal","Chhatrapati Shivaji Maharaj Museum","Aurangzeb Tomb","Grishneshwar Temple","Bhadra Maruti Temple","Shuli Bhanjan","Siddharth Garden","Siddharth Zoo","Salim Ali Lake","Salim Ali Bird Sanctuary","Himayat Bagh","Jayakwadi Dam","Nath Sagar Dam","Paithan","Pitalkhora Caves","Ghatotkacha Caves","Khuldabad","Valley of Saints","Antur Fort","Chand Minar","Bani Begum Garden","Zar Zari Zar Baksh Dargah","Goga Baba Hill","Dr Babasaheb Ambedkar Marathwada University","Mhaismal Hill Station","Verul Village","Harsul Lake","Delhi Gate","Kala Gate","Makai Gate","Rangeen Gate","Paithan Garden","Prozone Mall","City Chowk","Kranti Chowk","Connaught Garden","Rani Laxmibai Park","Qila-e-Ark","Jama Masjid Aurangabad","Jama Masjid Sambhajinagar","Begumpura","Roshan Gate","Jalna Gate","Mecca Gate","University Hill","Soneri Mahal Garden","H2O Water Park","MGM Hill","MGM Campus","Naregaon Lake","Satara Deolai Hills","Beed Bypass Road","Shendra MIDC","Waluj MIDC","Karodi","Kanchanwadi","Harsul Fort Area","Shendra Hills","Paithan Road Lake","Padegaon Lake","Sillod Road Hill","Mhaismal Valley","Verul Fort Area","Devgiri Hill","Khuldabad Gates","Mughal Silk Bazaar","Shahnoorwadi Dargah","Bhadkal Gate","Shahganj Market","Gulmandi Market","Nirala Bazaar","Aurangpura Market","Osmanpura","CIDCO Garden","CIDCO N1 Garden","CIDCO N2 Garden","CIDCO N4 Garden","Deogiri Institute Campus","MIT Aurangabad Campus","MIT Sambhajinagar Campus","MGM Polytechnic Campus","Seven Hills","Garkheda","Town Center","Jalgaon Road Hills","Satara Parisar","Harsul Ghat","Mhaismal Ghat","Ellora View Point","Ajanta View Point","Nath Sagar Garden","Paithan Fort Area","Daulatabad Tunnel","Khuldabad Valley","Aurangabad Railway Station","Sambhajinagar Railway Station","Lonar Lake","Lonar Crater Temple Complex","Ramkund Paithan","Sant Eknath Maharaj Samadhi","Eknath Garden Paithan","Paithan Bird Sanctuary","Ajanta View Tower","Ajanta Visitor Centre","Ellora Archaeological Museum","Grishneshwar Village","Khuldabad Dargah","Mughal Garden Khuldabad","Mhaismal Sunset Point","Mhaismal Forest Area","Goga Baba Tekdi Temple","Harsul Dam","Sillod","Sillod Lake","Kannad","Kannad Ghat","Phulambri","Phulambri Lake","Paithan Industrial Area","Shendra Industrial Park","Waluj Industrial Area","Karmad","Karmad Railway Station Area","Bidkin","Bidkin Industrial Area","Lasur Station","Lasur Lake","Gangapur Dam","Gangapur","Vaijapur","Vaijapur Lake","Shendurwada","Shendurwada Lake","Khadkeshwar Temple","Kachner Hanuman Temple","Bhavani Temple Satara","Ganesh Temple Kranti Chowk","Hanuman Tekdi","Pawan Ganesh Temple","Satara Hill View","Garkheda Lake","Beed Bypass Lake","Deolai Lake","Naregaon Hills","Pisadevi Hills","Pisadevi Temple"];

    window._bmiLocationInput = function(val) {
        const drop = document.getElementById('popup-location-dropdown');
        if (!drop) return;
        const q = val.trim().toLowerCase();
        if (!q) { drop.style.display = 'none'; drop.innerHTML = ''; return; }
        const matches = BMI_PLACES.filter(p => p.toLowerCase().includes(q)).slice(0, 8);
        if (!matches.length) { drop.style.display = 'none'; drop.innerHTML = ''; return; }
        drop.innerHTML = matches.map(p =>
            `<div onclick="window._bmiSelectLocation('${p.replace(/'/g,"\\'")}')"`+
            ` style="padding:9px 13px;font-size:13px;color:#111827;cursor:pointer;border-bottom:1px solid #f3f4f6;"`+
            ` onmouseover="this.style.background='#eff6ff'" onmouseout="this.style.background=''">${p}</div>`
        ).join('');
        drop.style.display = 'block';
    };

    window._bmiSelectLocation = function(place) {
        const inp  = document.getElementById('popup-location-input');
        const drop = document.getElementById('popup-location-dropdown');
        if (inp)  { inp.value = place; inp.style.borderColor = 'rgba(255,255,255,0.25)'; }
        if (drop) { drop.style.display = 'none'; drop.innerHTML = ''; }
        // Also sync to main search location if it's empty
        const mainLoc = document.getElementById('search-location');
        if (mainLoc && !mainLoc.value.trim()) mainLoc.value = place;
    };

    window.openBookingPopup = openBookingPopup;
})();
