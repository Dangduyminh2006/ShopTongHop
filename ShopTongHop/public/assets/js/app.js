/**
 * ShopXpress - Modern JavaScript Engine
 * Features: Dark/Light Mode Switcher, Hero Banner Slider, Flash Sale Countdown, AJAX Cart, Toast Notifications
 */

document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initHeroSlider();
    initFlashSaleCountdown();
    initAddToCart();
});

/**
 * 1. KHỞI TẠO & ĐIỀU KHIỂN CHỦ ĐỀ SÁNG / TỐI (DARK / LIGHT MODE)
 */
function initTheme() {
    const savedTheme = localStorage.getItem('shopxpress_theme') || 'dark';
    document.documentElement.setAttribute('data-theme', savedTheme);

    const toggleBtn = document.getElementById('themeToggleBtn');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('shopxpress_theme', newTheme);
        });
    }
}

/**
 * 2. TỰ ĐỘNG CHUYỂN SLIDE BANNER HERO (HERO BANNER SLIDER)
 */
function initHeroSlider() {
    const slider = document.querySelector('.hero-slider');
    if (!slider) return;

    const slides = slider.querySelectorAll('.hero-slide');
    if (slides.length <= 1) return;

    let currentIndex = 0;
    let autoSlideInterval = null;

    // Tìm hoặc tạo dots container
    let dotsContainer = slider.querySelector('.slider-dots');
    if (!dotsContainer) {
        dotsContainer = document.createElement('div');
        dotsContainer.className = 'slider-dots';
        slides.forEach((_, idx) => {
            const dot = document.createElement('div');
            dot.className = `slider-dot ${idx === 0 ? 'active' : ''}`;
            dot.addEventListener('click', () => goToSlide(idx));
            dotsContainer.appendChild(dot);
        });
        slider.appendChild(dotsContainer);
    }

    // Nút Prev / Next
    const prevBtn = slider.querySelector('.slider-prev');
    const nextBtn = slider.querySelector('.slider-next');

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            prevSlide();
            resetAutoSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            nextSlide();
            resetAutoSlide();
        });
    }

    function goToSlide(index) {
        slides[currentIndex].classList.remove('active');
        const dots = dotsContainer.querySelectorAll('.slider-dot');
        if (dots[currentIndex]) dots[currentIndex].classList.remove('active');

        currentIndex = (index + slides.length) % slides.length;

        slides[currentIndex].classList.add('active');
        if (dots[currentIndex]) dots[currentIndex].classList.add('active');
    }

    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    function startAutoSlide() {
        if (!autoSlideInterval) {
            autoSlideInterval = setInterval(nextSlide, 4500);
        }
    }

    function stopAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
    }

    function resetAutoSlide() {
        stopAutoSlide();
        startAutoSlide();
    }

    slider.addEventListener('mouseenter', stopAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);

    startAutoSlide();
}

/**
 * 3. ĐỒNG HỒ ĐẾM NGƯỢC FLASH SALE THỜI GIAN THỰC (COUNTDOWN TIMER)
 */
function initFlashSaleCountdown() {
    const countdownEl = document.getElementById('flashCountdown');
    if (!countdownEl) return;

    const endTimeStr = countdownEl.getAttribute('data-endtime');
    if (!endTimeStr) return;

    const targetDate = new Date(endTimeStr).getTime();

    function updateTimer() {
        const now = new Date().getTime();
        const distance = targetDate - now;

        if (distance <= 0) {
            const h = document.getElementById('cdHours');
            const m = document.getElementById('cdMinutes');
            const s = document.getElementById('cdSeconds');
            if (h) h.textContent = '00';
            if (m) m.textContent = '00';
            if (s) s.textContent = '00';
            return;
        }

        const hours = Math.floor(distance / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        const hoursEl = document.getElementById('cdHours');
        const minutesEl = document.getElementById('cdMinutes');
        const secondsEl = document.getElementById('cdSeconds');

        if (hoursEl) hoursEl.textContent = String(hours).padStart(2, '0');
        if (minutesEl) minutesEl.textContent = String(minutes).padStart(2, '0');
        if (secondsEl) secondsEl.textContent = String(seconds).padStart(2, '0');
    }

    updateTimer();
    setInterval(updateTimer, 1000);
}

/**
 * 4. XỬ LÝ THÊM NHANH VÀO GIỎ HÀNG (AJAX ADD TO CART)
 */
function initAddToCart() {
    document.querySelectorAll('.btn-add-cart').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const productId = btn.getAttribute('data-id');
            if (!productId) return;

            btn.disabled = true;
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            try {
                const formData = new FormData();
                formData.append('product_id', productId);
                formData.append('quantity', 1);

                const response = await fetch(`${BASE_URL}/cart/add`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showToast('success', data.message || 'Đã thêm sản phẩm vào giỏ hàng!');
                    const badge = document.getElementById('cartBadge');
                    if (badge && data.cartCount !== undefined) {
                        badge.textContent = data.cartCount;
                        badge.classList.remove('bounce');
                        void badge.offsetWidth; // Force reflow
                        badge.classList.add('bounce');
                    }
                } else {
                    if (data.require_login) {
                        showToast('warning', data.message || 'Vui lòng đăng nhập tài khoản!');
                        setTimeout(() => {
                            const currentPath = encodeURIComponent(window.location.pathname + window.location.search);
                            window.location.href = `${BASE_URL}/login?redirect=${currentPath}`;
                        }, 1000);
                    } else {
                        showToast('error', data.message || 'Không thể thêm vào giỏ hàng.');
                    }
                }
            } catch (err) {
                console.error('Cart error:', err);
                showToast('error', 'Có lỗi khi thao tác với giỏ hàng.');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        });
    });
}

/**
 * 5. HỆ THỐNG THÔNG BÁO NỔI TOAST NOTIFICATION
 */
function showToast(type, message) {
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = `
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 99999;
            display: flex;
            flex-direction: column;
            gap: 12px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const isSuccess = type === 'success';
    toast.style.cssText = `
        background: ${isSuccess ? 'rgba(16, 21, 30, 0.95)' : 'rgba(42, 18, 21, 0.95)'};
        color: ${isSuccess ? '#00E676' : '#FF3838'};
        border: 1px solid ${isSuccess ? '#00E676' : '#FF3838'};
        backdrop-filter: blur(8px);
        padding: 14px 22px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
        font-family: 'Inter', sans-serif;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 12px;
        pointer-events: auto;
        opacity: 0;
        transform: translateY(-12px);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    `;

    const icon = isSuccess ? '<i class="fa-solid fa-circle-check" style="font-size:18px;"></i>' : '<i class="fa-solid fa-circle-exclamation" style="font-size:18px;"></i>';
    toast.innerHTML = `${icon} <span>${message}</span>`;
    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
    });

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-12px)';
        setTimeout(() => toast.remove(), 300);
    }, 3200);
}
