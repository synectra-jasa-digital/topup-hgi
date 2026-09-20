<!-- Interactive & Logic State Handler -->
<script>
  (function() {
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let ready = false; // false during the first render, so nothing flashes on page load

    // ---------- Motion + feedback helpers ----------

    // Sections rise into view once. The hidden start state only exists after .reveal-ready is set here.
    (function setupReveal() {
      const targets = document.querySelectorAll('[data-reveal]');
      if (reduceMotion.matches || !('IntersectionObserver' in window) || targets.length === 0) return;

      let observed = false;
      const io = new IntersectionObserver((entries) => {
        observed = true;
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

      document.documentElement.classList.add('reveal-ready');
      targets.forEach((el) => io.observe(el));
      // If the browser never reports (background tab), show everything instead of leaving it invisible.
      setTimeout(() => { if (!observed) targets.forEach((el) => el.classList.add('is-visible')); }, 3000);
    })();

    function replay(el, className) {
      if (!el || reduceMotion.matches) return;
      el.classList.remove(className);
      void el.offsetWidth;
      el.classList.add(className);
    }

    // Sets text; a value that changed lights up briefly so the eye can follow it.
    function setText(el, text) {
      if (!el || el.textContent === text) return;
      el.textContent = text;
      if (ready) replay(el, 'value-flash');
    }

    // Counts a rupiah figure from its old value to the new one.
    function animateNumber(el, to) {
      if (!el) return;
      const from = Number(el.dataset.value !== undefined ? el.dataset.value : to);
      el.dataset.value = String(to);
      cancelAnimationFrame(el._raf);
      clearTimeout(el._done);
      if (reduceMotion.matches || document.hidden || from === to) {
        el.textContent = formatRupiah(to);
        return;
      }
      const start = performance.now();
      const duration = 450;
      const step = (now) => {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = formatRupiah(Math.round(from + (to - from) * eased));
        if (p < 1) el._raf = requestAnimationFrame(step);
      };
      el._raf = requestAnimationFrame(step);
      el._done = setTimeout(() => { el.textContent = formatRupiah(to); }, duration + 120); // final value even if frames stall
    }

    // One message area for validation and results (replaces alert()).
    const toast = document.getElementById('form-toast');
    const toastBox = document.getElementById('form-toast-box');
    const toastIcon = document.getElementById('form-toast-icon');
    const toastText = document.getElementById('form-toast-text');

    function hideToast() {
      toast?.classList.add('hidden');
    }

    function showToast(message, tone) {
      if (!toast || !toastBox) return;
      const ok = tone === 'success';
      toastBox.dataset.tone = ok ? 'success' : 'error';
      toastBox.classList.toggle('border-emerald-300', ok);
      toastBox.classList.toggle('border-rose-300', !ok);
      toastIcon.textContent = ok ? 'check_circle' : 'error';
      toastIcon.classList.toggle('text-emerald-700', ok);
      toastIcon.classList.toggle('text-rose-600', !ok);
      toastText.textContent = message;
      toast.classList.remove('hidden');
      if (!ok) replay(toastBox, 'shake');
    }

    document.getElementById('form-toast-close')?.addEventListener('click', hideToast);
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') hideToast(); });

    // Reports a problem, marks the field, and puts the cursor there.
    function fail(message, field) {
      showToast(message, 'error');
      if (field) {
        field.setAttribute('aria-invalid', 'true');
        field.focus({ preventScroll: false });
      }
      return false;
    }

    document.addEventListener('input', (e) => {
      if (e.target instanceof HTMLElement && e.target.getAttribute('aria-invalid') === 'true') {
        e.target.removeAttribute('aria-invalid');
      }
    });

    function formatRupiah(num) {
      return 'Rp' + Number(num).toLocaleString('id-ID');
    }

    // ---------- Main mode switcher (Top Up / Beli vs Jual / Bongkar) ----------
    const tabModeBuy = document.getElementById('tab-mode-buy');
    const tabModeSell = document.getElementById('tab-mode-sell');
    const viewModeBuy = document.getElementById('view-mode-buy');
    const viewModeSell = document.getElementById('view-mode-sell');

    function syncNav(mode) {
      document.querySelectorAll('[data-nav-mode]').forEach((link) => {
        if (link.dataset.navMode === mode) link.setAttribute('aria-current', 'page');
        else link.removeAttribute('aria-current');
      });
    }

    function switchMode(mode) {
      syncNav(mode);
      const sell = mode === 'sell';
      const mobileFooter = document.querySelector('.mobile-only-sticky'); // buy-only "Total Tagihan" bar, overlays.php

      tabModeBuy?.setAttribute('aria-pressed', String(!sell));
      tabModeSell?.setAttribute('aria-pressed', String(sell));
      document.querySelectorAll('[data-hero-copy]').forEach((el) => { el.hidden = el.dataset.heroCopy !== mode; });

      viewModeBuy?.classList.toggle('tab-mode-hidden', sell);
      viewModeSell?.classList.toggle('tab-mode-hidden', !sell);
      mobileFooter?.classList.toggle('tab-mode-hidden', sell);
      hideToast();
    }

    tabModeBuy?.addEventListener('click', () => switchMode('buy'));
    tabModeSell?.addEventListener('click', () => switchMode('sell'));

    const modeFromHash = () => (window.location.hash === '#jual' || window.location.hash === '#bongkar') ? 'sell' : 'buy';
    switchMode(modeFromHash());
    window.addEventListener('hashchange', () => switchMode(modeFromHash()));

    // ---------- State ----------
    const state = {
      productId: null,
      itemTitle: "Belum memilih produk",
      basePrice: 0,
      unitRate: "Silakan pilih nominal produk di samping",
      categoryIcon: "",
      userId: "",
      whatsapp: "",
      payMethod: "",
      payMethodChannelId: null,
      couponCode: "", // sent as-is; the server checks it when the order is created
      bongkarCatalogId: null,
      bongkarType: "",
      bongkarRate: 0,
      bongkarUnit: "kartu",
      bongkarQty: 1,
      bongkarPayout: ""
    };

    // ---------- Buy: steps, summary ----------
    const stepBadges = {};
    document.querySelectorAll('.step-number-badge[data-step]').forEach((badge) => { stepBadges[badge.dataset.step] = badge; });

    function setStepDone(n, done) {
      const badge = stepBadges[n];
      if (!badge || badge.classList.contains('is-done') === done) return;
      badge.classList.toggle('is-done', done);
      const face = badge.querySelector('[aria-hidden]');
      const status = badge.querySelector('[data-step-status]');
      if (face) face.textContent = done ? '✓' : String(n);
      if (status) status.textContent = done ? 'selesai' : '';
      if (ready) replay(badge, 'pop-in');
    }

    function refreshSteps() {
      setStepDone('1', !!document.querySelector('.category-pill.active'));
      setStepDone('2', !!state.productId);
      setStepDone('3', state.userId !== '' && state.whatsapp !== '');
      setStepDone('4', !!state.payMethodChannelId);
    }

    function updateReceiptUI() {
      const grandTotal = Math.max(0, state.basePrice);

      const receiptIconContainer = document.getElementById('receipt-category-icon-container');
      setText(document.getElementById('receipt-item-name'), state.productId ? state.itemTitle : 'Belum memilih produk');
      setText(document.getElementById('receipt-unit-rate'), state.productId ? state.unitRate : 'Silakan pilih nominal produk di samping');
      setText(document.getElementById('receipt-item-price'), state.productId ? formatRupiah(state.basePrice) : 'Rp0');
      if (receiptIconContainer) {
        if (state.productId && state.categoryIcon) {
          receiptIconContainer.innerHTML = `<img src="${state.categoryIcon}" alt="" class="w-full h-full object-contain">`;
        } else {
          receiptIconContainer.innerHTML = `<span class="material-symbols-outlined text-[22px]" aria-hidden="true">sports_esports</span>`;
        }
      }
      setText(document.getElementById('receipt-user-id'), state.userId || '-');
      setText(document.getElementById('receipt-wa'), state.whatsapp || '-');
      setText(document.getElementById('receipt-method'), state.payMethod || '-');

      animateNumber(document.getElementById('calc-subtotal'), state.basePrice);
      animateNumber(document.getElementById('calc-grand-total'), grandTotal);
      animateNumber(document.getElementById('mobile-bottom-total'), grandTotal);
      refreshSteps();
    }

    // Step 1: Category Pill Tabs & Quick Search
    const catPills = document.querySelectorAll('.category-pill');
    const categoryGroups = document.querySelectorAll('[data-category-group]');
    const catalogSearchInput = document.getElementById('catalog-search-input');
    const searchEmpty = document.getElementById('catalog-search-empty');

    function refreshSearchEmpty() {
      if (!searchEmpty) return;
      const query = (catalogSearchInput ? catalogSearchInput.value : '').trim();
      const visible = document.querySelectorAll('[data-category-group]:not(.hidden) .product-card:not(.hidden)').length;
      searchEmpty.classList.toggle('hidden', !(query !== '' && visible === 0));
    }

    function showCategoryGroup(slug) {
      categoryGroups.forEach(group => {
        const match = group.getAttribute('data-category-group') === slug;
        group.classList.toggle('hidden', !match);
      });
      refreshSearchEmpty();
    }

    catPills.forEach(pill => {
      pill.addEventListener('click', () => {
        const slug = pill.getAttribute('data-cat');
        catPills.forEach(p => {
          p.classList.remove('active');
          p.classList.add('text-slate-700', 'bg-slate-100/90');
          p.setAttribute('aria-pressed', 'false');
        });
        pill.classList.add('active');
        pill.classList.remove('text-slate-700', 'bg-slate-100/90');
        pill.setAttribute('aria-pressed', 'true');
        if (catalogSearchInput) catalogSearchInput.value = '';
        document.querySelectorAll('.product-card').forEach(card => card.classList.remove('hidden'));
        showCategoryGroup(slug);
      });
    });

    if (catalogSearchInput) {
      catalogSearchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        document.querySelectorAll('.product-card').forEach(card => {
          const title = (card.getAttribute('data-title') || '').toLowerCase();
          const unit = (card.getAttribute('data-unit') || '').toLowerCase();
          const match = title.includes(query) || unit.includes(query);
          card.classList.toggle('hidden', !match && query !== '');
        });
        refreshSearchEmpty();
      });
    }

    const firstCategory = document.querySelector('.category-pill.active');
    if (firstCategory) {
      showCategoryGroup(firstCategory.getAttribute('data-cat'));
    }

    // Step 2: Product Nominal Selection
    const productCards = document.querySelectorAll('.product-card');
    productCards.forEach(card => {
      card.addEventListener('click', () => {
        productCards.forEach(c => {
          c.classList.remove('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20', 'product-card-selected');
          c.classList.add('border-slate-200', 'bg-white');
          c.setAttribute('aria-pressed', 'false');
        });
        card.classList.add('active', 'product-card-selected');
        card.classList.remove('border-slate-200', 'bg-white');
        card.setAttribute('aria-pressed', 'true');
        replay(card.querySelector('.check-mark'), 'pop-in');

        state.productId = card.getAttribute('data-id') || null;
        state.itemTitle = card.getAttribute('data-title') || "";
        state.basePrice = parseInt(card.getAttribute('data-price') || "0", 10);
        state.unitRate = card.getAttribute('data-unit') || "";
        state.categoryIcon = card.getAttribute('data-icon') || "";
        updateReceiptUI();
      });
    });

    // Step 3: User ID & WhatsApp Binding (values can come back from the server after a failed checkout)
    const inputUserId = document.getElementById('input-user-id');
    const inputWa = document.getElementById('input-whatsapp');
    const promoInput = document.getElementById('receipt-promo-input');
    const promoStatus = document.getElementById('promo-status');
    const promoDefaultText = promoStatus ? promoStatus.textContent : '';

    state.userId = inputUserId ? inputUserId.value.trim() : '';
    state.whatsapp = inputWa ? inputWa.value.trim() : '';
    state.couponCode = promoInput ? promoInput.value.trim().toUpperCase() : '';

    inputUserId?.addEventListener('input', (e) => {
      state.userId = e.target.value.trim();
      updateReceiptUI();
    });

    inputWa?.addEventListener('input', (e) => {
      state.whatsapp = e.target.value.trim();
      updateReceiptUI();
    });

    // Step 4: Payment Method Selection
    const payMethodCards = document.querySelectorAll('.pay-method-card');
    payMethodCards.forEach(payCard => {
      payCard.addEventListener('click', () => {
        payMethodCards.forEach(p => {
          p.classList.remove('selected', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          p.classList.add('border-slate-200');
          p.setAttribute('aria-checked', 'false');
        });
        payCard.classList.add('selected', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        payCard.classList.remove('border-slate-200');
        payCard.setAttribute('aria-checked', 'true');
        replay(payCard.querySelector('.check-mark'), 'pop-in');
        state.payMethod = payCard.getAttribute('data-method') || '';
        state.payMethodChannelId = payCard.getAttribute('data-channel-id');
        updateReceiptUI();
      });
    });

    const defaultPayMethodCard = document.querySelector('.pay-method-card.selected');
    if (defaultPayMethodCard) {
      state.payMethodChannelId = defaultPayMethodCard.getAttribute('data-channel-id');
      state.payMethod = defaultPayMethodCard.getAttribute('data-method') || '';
    }

    // Voucher: no discount is computed here. The code goes to the server with the order, which validates it.
    promoInput?.addEventListener('input', () => {
      state.couponCode = promoInput.value.trim().toUpperCase();
      if (promoStatus) {
        promoStatus.textContent = state.couponCode
          ? `Kode ${state.couponCode} akan dicek saat pesanan dibuat. Potongan harga tampil di invoice.`
          : promoDefaultText;
      }
    });

    // ---------- Dialogs: focus goes in, stays in, and returns; Escape and the backdrop close; the page behind stops scrolling ----------
    const dialogStack = [];
    const FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    function openDialog(dialog, opener) {
      if (!dialog || dialogStack.includes(dialog)) return;
      dialog._opener = opener || document.activeElement;
      dialog.classList.remove('hidden');
      document.documentElement.classList.add('modal-open');
      dialogStack.push(dialog);
      dialog.querySelector('.dialog-panel')?.focus({ preventScroll: true });
    }

    function closeDialog(dialog, restoreFocus = true) {
      if (!dialog || dialog.classList.contains('hidden')) return;
      dialog.classList.add('hidden');
      const at = dialogStack.indexOf(dialog);
      if (at !== -1) dialogStack.splice(at, 1);
      if (dialogStack.length === 0) document.documentElement.classList.remove('modal-open');
      if (restoreFocus && dialog._opener && document.contains(dialog._opener)) dialog._opener.focus();
    }

    document.addEventListener('keydown', (e) => {
      const top = dialogStack[dialogStack.length - 1];
      if (!top) return;
      if (e.key === 'Escape') {
        e.preventDefault();
        closeDialog(top);
        return;
      }
      if (e.key !== 'Tab') return;
      const items = Array.from(top.querySelectorAll(FOCUSABLE)).filter((el) => el.offsetParent !== null);
      if (items.length === 0) { e.preventDefault(); return; }
      const first = items[0];
      const last = items[items.length - 1];
      const panel = top.querySelector('.dialog-panel');
      if (e.shiftKey && (document.activeElement === first || document.activeElement === panel)) {
        e.preventDefault();
        last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault();
        first.focus();
      }
    });

    // A press that starts on the dark backdrop itself (not inside the panel) dismisses the dialog.
    document.querySelectorAll('.dialog-backdrop').forEach((backdrop) => {
      backdrop.addEventListener('mousedown', (e) => { if (e.target === backdrop) closeDialog(backdrop); });
    });

    // Modal Guide ID
    const btnGuideId = document.getElementById('btn-guide-id');
    const guideModal = document.getElementById('guide-modal');
    const btnCloseGuideModal = document.getElementById('btn-close-guide-modal');
    const btnUnderstandGuide = document.getElementById('btn-understand-guide');

    btnGuideId?.addEventListener('click', () => openDialog(guideModal, btnGuideId));
    btnCloseGuideModal?.addEventListener('click', () => closeDialog(guideModal));
    // "Isi ID Sekarang" continues the task: close the guide and put the cursor in the ID field.
    btnUnderstandGuide?.addEventListener('click', () => {
      closeDialog(guideModal, false);
      inputUserId?.focus();
    });

    // Checkout Confirmation Modal
    const btnPayNow = document.getElementById('btn-pay-now');
    const btnMobileCheckout = document.getElementById('btn-mobile-checkout');
    const checkoutModal = document.getElementById('checkout-modal');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnCancelCheckout = document.getElementById('btn-cancel-checkout');
    const btnSubmitPay = document.getElementById('btn-submit-pay');

    const modalItem = document.getElementById('modal-item');
    const modalId = document.getElementById('modal-id');
    const modalMethod = document.getElementById('modal-method');
    const modalTotal = document.getElementById('modal-total');
    const modalVoucherRow = document.getElementById('modal-voucher-row');
    const modalVoucher = document.getElementById('modal-voucher');
    const modalVoucherNote = document.getElementById('modal-voucher-note');

    function openCheckoutModal(e) {
      if (payMethodCards.length === 0) {
        return fail('Belum ada metode pembayaran yang aktif, jadi pesanan belum bisa dibuat.');
      }
      if (!state.productId) {
        const firstCard = document.querySelector('[data-category-group]:not(.hidden) .product-card');
        firstCard?.scrollIntoView({ block: 'center', behavior: reduceMotion.matches ? 'auto' : 'smooth' });
        return fail('Pilih nominal top up terlebih dahulu.', firstCard);
      }
      if (!state.userId) {
        return fail('Masukkan User ID Game Anda.', inputUserId);
      }
      if (!state.whatsapp) {
        return fail('Masukkan nomor WhatsApp Anda untuk menerima invoice.', inputWa);
      }
      if (!/^(08|628)[0-9]{7,12}$/.test(state.whatsapp)) {
        return fail('Nomor WhatsApp harus diawali 08 atau 628 dan tanpa spasi atau tanda hubung.', inputWa);
      }
      if (!state.payMethodChannelId) {
        return fail('Pilih metode pembayaran terlebih dahulu.');
      }

      hideToast();
      if (modalItem) modalItem.textContent = state.itemTitle;
      if (modalId) modalId.textContent = state.userId;
      if (modalMethod) modalMethod.textContent = state.payMethod || '-';
      if (modalTotal) modalTotal.textContent = formatRupiah(Math.max(0, state.basePrice));

      // The voucher is only checked by the server, so the buyer sees the code that will be sent.
      const hasVoucher = state.couponCode !== '';
      if (modalVoucher) modalVoucher.textContent = state.couponCode;
      modalVoucherRow?.classList.toggle('hidden', !hasVoucher);
      modalVoucherRow?.classList.toggle('flex', hasVoucher);
      modalVoucherNote?.classList.toggle('hidden', !hasVoucher);

      openDialog(checkoutModal, e && e.currentTarget ? e.currentTarget : document.activeElement);
    }

    btnPayNow?.addEventListener('click', openCheckoutModal);
    btnMobileCheckout?.addEventListener('click', openCheckoutModal);
    btnCloseModal?.addEventListener('click', () => closeDialog(checkoutModal));
    btnCancelCheckout?.addEventListener('click', () => closeDialog(checkoutModal));

    // Form Submission Trigger. The button locks so a second tap cannot create a second order.
    const payButtonLabel = btnSubmitPay ? btnSubmitPay.innerHTML : '';
    function unlockPayButton() {
      if (!btnSubmitPay) return;
      btnSubmitPay.disabled = false;
      btnSubmitPay.innerHTML = payButtonLabel;
    }
    window.addEventListener('pageshow', () => { // back button restores the page from cache
      unlockPayButton();
      closeDialog(checkoutModal, false);
    });

    btnSubmitPay?.addEventListener('click', () => {
      if (!state.productId || btnSubmitPay.disabled) return;
      const form = document.getElementById('backend-checkout-form');
      const hiddenGameId = document.getElementById('hidden-game-id');
      const hiddenWa = document.getElementById('hidden-whatsapp');
      const hiddenVoucher = document.getElementById('hidden-voucher');
      const hiddenPaymentChannel = document.getElementById('hidden-payment-channel-id');

      if (form) {
        form.action = '<?= base_url("checkout/") ?>' + state.productId;
        if (hiddenGameId) hiddenGameId.value = state.userId;
        if (hiddenWa) hiddenWa.value = state.whatsapp;
        if (hiddenVoucher) hiddenVoucher.value = state.couponCode || '';
        if (hiddenPaymentChannel) hiddenPaymentChannel.value = state.payMethodChannelId || '';
        btnSubmitPay.disabled = true;
        btnSubmitPay.textContent = 'Membuat pesanan...';
        form.submit();
      }
    });

    // ---------- Sell: Bongkar / Jual ----------
    const bongkarCards = document.querySelectorAll('.bongkar-card');
    const bongkarQtyInput = document.getElementById('input-card-qty');
    const bongkarWaInput = document.getElementById('input-sell-wa');
    const bongkarGameIdInput = document.getElementById('input-card-game-id');
    const bongkarPayoutAccountInput = document.getElementById('input-payout-account');
    const bongkarPayoutNameInput = document.getElementById('input-payout-name');
    const bongkarPayoutButtons = document.querySelectorAll('.bongkar-payout-btn');
    const btnSubmitBongkar = document.getElementById('btn-submit-bongkar');
    const btnSubmitBongkarIcon = document.getElementById('btn-submit-bongkar-icon');
    const btnSubmitBongkarLabel = document.getElementById('btn-submit-bongkar-label');
    const btnQtyMinus = document.getElementById('btn-qty-minus');
    const btnQtyPlus = document.getElementById('btn-qty-plus');

    const firstBongkarCard = document.querySelector('.bongkar-card');
    if (firstBongkarCard) {
      state.bongkarCatalogId = firstBongkarCard.getAttribute('data-bongkar-catalog-id');
      state.bongkarType = firstBongkarCard.getAttribute('data-label') || '';
      state.bongkarRate = parseInt(firstBongkarCard.getAttribute('data-rate') || '0', 10);
      state.bongkarUnit = firstBongkarCard.getAttribute('data-unit') || 'kartu';
    }
    const firstPayoutButton = document.querySelector('.bongkar-payout-btn');
    if (firstPayoutButton) {
      state.bongkarPayout = firstPayoutButton.getAttribute('data-method') || '';
    }

    function refreshBongkarUI() {
      const rate = state.bongkarRate || 0;
      const qty = state.bongkarQty || 1;

      animateNumber(document.getElementById('bongkar-estimated'), qty * rate);
      setText(document.getElementById('bongkar-receipt-label'), state.bongkarType || 'Belum memilih item');
      setText(document.getElementById('bongkar-receipt-rate'), rate > 0 ? `${formatRupiah(rate)} / ${state.bongkarUnit}` : '-');
      setText(document.getElementById('bongkar-receipt-qty'), `${qty} ${state.bongkarUnit}`);
      setText(document.getElementById('bongkar-receipt-payout'), state.bongkarPayout || '-');
      setText(document.getElementById('bongkar-receipt-wa'), (bongkarWaInput ? bongkarWaInput.value.trim() : '') || '-');
      const bongkarUnitLabel = document.getElementById('bongkar-unit-label');
      if (bongkarUnitLabel) bongkarUnitLabel.textContent = state.bongkarUnit || 'kartu';
    }

    // After a submit the button says "Terkirim"; any change to the form unlocks it again.
    let submitted = false;
    function unlockBongkarButton() {
      if (!submitted || !btnSubmitBongkar) return;
      submitted = false;
      btnSubmitBongkar.disabled = false;
      btnSubmitBongkarLabel.textContent = 'Kirim Pengajuan Bongkar';
      btnSubmitBongkarIcon.textContent = 'send';
    }
    document.getElementById('view-mode-sell')?.addEventListener('input', unlockBongkarButton);
    document.getElementById('view-mode-sell')?.addEventListener('click', (e) => {
      if (e.target instanceof Element && e.target.closest('.bongkar-card, .bongkar-payout-btn, #btn-qty-minus, #btn-qty-plus')) unlockBongkarButton();
    });

    bongkarCards.forEach(card => {
      card.addEventListener('click', () => {
        bongkarCards.forEach(c => {
          c.classList.remove('selected', 'border-2', 'border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/20');
          c.classList.add('border-slate-200', 'bg-white');
          c.setAttribute('aria-checked', 'false');
        });
        card.classList.add('selected', 'border-2', 'border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/20');
        card.classList.remove('border-slate-200', 'bg-white');
        card.setAttribute('aria-checked', 'true');
        replay(card.querySelector('.check-mark'), 'pop-in');

        state.bongkarCatalogId = card.getAttribute('data-bongkar-catalog-id');
        state.bongkarType = card.getAttribute('data-label') || '';
        state.bongkarRate = parseInt(card.getAttribute('data-rate') || '0', 10);
        state.bongkarUnit = card.getAttribute('data-unit') || 'kartu';
        refreshBongkarUI();
      });
    });

    function setQty(value) {
      const qty = Math.min(1000, Math.max(1, value));
      state.bongkarQty = qty;
      if (bongkarQtyInput) {
        bongkarQtyInput.value = qty;
        replay(bongkarQtyInput, 'pop-in');
      }
      refreshBongkarUI();
    }

    btnQtyMinus?.addEventListener('click', () => setQty(parseInt(bongkarQtyInput.value || '1', 10) - 1));
    btnQtyPlus?.addEventListener('click', () => setQty(parseInt(bongkarQtyInput.value || '1', 10) + 1));

    bongkarQtyInput?.addEventListener('input', (e) => {
      let val = parseInt(e.target.value || '1', 10);
      if (Number.isNaN(val) || val < 1) val = 1;
      if (val > 1000) val = 1000;
      state.bongkarQty = val;
      refreshBongkarUI();
    });

    bongkarWaInput?.addEventListener('input', refreshBongkarUI);

    bongkarPayoutButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        bongkarPayoutButtons.forEach(b => {
          b.classList.remove('selected', 'border-2', 'border-amber-500', 'bg-amber-50', 'font-bold', 'text-amber-900');
          b.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
          b.setAttribute('aria-checked', 'false');
        });
        btn.classList.add('selected', 'border-2', 'border-amber-500', 'bg-amber-50', 'font-bold', 'text-amber-900');
        btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');
        btn.setAttribute('aria-checked', 'true');

        state.bongkarPayout = btn.getAttribute('data-method') || '';
        refreshBongkarUI();
      });
    });

    function setBongkarBusy(busy) {
      if (!btnSubmitBongkar) return;
      btnSubmitBongkar.disabled = busy;
      btnSubmitBongkarLabel.textContent = busy ? 'Mengirim pengajuan...' : 'Kirim Pengajuan Bongkar';
      btnSubmitBongkarIcon.textContent = busy ? 'progress_activity' : 'send';
      btnSubmitBongkarIcon.classList.toggle('animate-spin', busy);
    }

    btnSubmitBongkar?.addEventListener('click', async () => {
      if (!state.bongkarCatalogId) {
        return fail('Pilih jenis kartu atau koin yang ingin dijual terlebih dahulu.', firstBongkarCard);
      }

      const wa = bongkarWaInput ? bongkarWaInput.value.trim() : '';
      const account = bongkarPayoutAccountInput ? bongkarPayoutAccountInput.value.trim() : '';
      const name = bongkarPayoutNameInput ? bongkarPayoutNameInput.value.trim() : '';
      const gameId = bongkarGameIdInput ? bongkarGameIdInput.value.trim() : '';

      if (!wa) {
        return fail('Masukkan nomor WhatsApp Anda.', bongkarWaInput);
      }
      if (!state.bongkarPayout) {
        return fail('Belum ada metode pencairan yang aktif, jadi pengajuan belum bisa dikirim.');
      }
      if (!account) {
        return fail('Isi nomor rekening atau e-wallet tujuan pencairan.', bongkarPayoutAccountInput);
      }
      if (!name) {
        return fail('Isi nama pemilik rekening tujuan pencairan.', bongkarPayoutNameInput);
      }

      hideToast();
      setBongkarBusy(true);

      try {
        const response = await fetch('<?= base_url("bongkar/submit") ?>', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            '<?= csrf_header() ?>': '<?= csrf_hash() ?>'
          },
          body: JSON.stringify({
            bongkar_catalog_id: state.bongkarCatalogId,
            quantity: state.bongkarQty,
            customer_whatsapp: wa,
            payout_method: state.bongkarPayout,
            payout_account_number: account,
            payout_account_name: name,
            customer_note: gameId ? `User ID Game: ${gameId}` : ''
          })
        });

        const resData = await response.json();
        if (resData.success) {
          submitted = true;
          setBongkarBusy(false);
          btnSubmitBongkar.disabled = true;
          btnSubmitBongkarLabel.textContent = 'Pengajuan terkirim';
          btnSubmitBongkarIcon.textContent = 'check_circle';
          replay(btnSubmitBongkarIcon, 'pop-in');
          if (resData.wa_url) {
            showToast('Pengajuan terkirim. Membuka WhatsApp...', 'success');
            window.location.href = resData.wa_url;
          } else {
            showToast('Pengajuan bongkar berhasil dikirim. Tunggu konfirmasi dari admin.', 'success');
          }
        } else {
          setBongkarBusy(false);
          fail(resData.messages?.error || resData.error || 'Pengajuan bongkar gagal disimpan. Periksa kembali data Anda.');
        }
      } catch (err) {
        setBongkarBusy(false);
        fail('Koneksi terputus, pengajuan belum terkirim. Silakan coba lagi.');
      }
    });

    // ---------- Initial render ----------
    updateReceiptUI();
    refreshBongkarUI();
    ready = true;
  })();
</script>
