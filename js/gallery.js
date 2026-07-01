// ===== SCROLL REVEAL =====
const revealObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('in-view');
      revealObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.08, rootMargin: '0px 0px -20px 0px' });

document.querySelectorAll(
  '.behind-title, .behind-desc, .cta-box-content, .behind-card'
).forEach(el => revealObserver.observe(el));

// ===== FILTER =====
const filterBtns = document.querySelectorAll('.filter-btn');
const allItems = document.querySelectorAll('.gallery-item');

filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    filterBtns.forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const filter = btn.dataset.filter;

    allItems.forEach(item => {
      const cat = item.dataset.category;
      item.style.display = (filter === 'all' || cat === filter) ? '' : 'none';
    });

    shownCount = INITIAL_COUNT;
    applyLoadMore();
  });
});

// ===== LOAD MORE / SHOW LESS =====
const INITIAL_COUNT = 14;
const LOAD_STEP = 10;
let shownCount = INITIAL_COUNT;
const loadMoreBtn = document.getElementById('loadMoreBtn');
const showLessBtn = document.getElementById('showLessBtn');

function getVisibleItems() {
  return Array.from(allItems).filter(item => item.style.display !== 'none');
}

function applyLoadMore() {
  const visible = getVisibleItems();
  visible.forEach((item, i) => {
    if (i < shownCount) {
      item.classList.remove('hidden');
      revealObserver.observe(item);
    } else {
      item.classList.add('hidden');
    }
  });
  loadMoreBtn.style.display = shownCount < visible.length ? 'inline-block' : 'none';
  showLessBtn.style.display = shownCount > INITIAL_COUNT ? 'inline-block' : 'none';
}

loadMoreBtn.addEventListener('click', () => { shownCount += LOAD_STEP; applyLoadMore(); });
showLessBtn.addEventListener('click', () => {
  shownCount = INITIAL_COUNT;
  applyLoadMore();
  document.getElementById('gallery').scrollIntoView({ behavior: 'smooth' });
});

applyLoadMore();

// ===== BEHIND CARDS STAGGERED REVEAL =====
const behindCards = document.querySelectorAll('.behind-card');
const behindObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry, idx) => {
    if (entry.isIntersecting) {
      setTimeout(() => {
        entry.target.classList.add('in-view');
      }, idx * 100);
      behindObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.1 });

behindCards.forEach(card => behindObserver.observe(card));

// == BEHIND THE JOURNEY ==//


const revealEls = document.querySelectorAll('.behind-title, .behind-desc');
const revObs = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.classList.add('in-view');
      revObs.unobserve(e.target);
    }
  });
}, { threshold: 0.1 });

revealEls.forEach(el => revObs.observe(el));
