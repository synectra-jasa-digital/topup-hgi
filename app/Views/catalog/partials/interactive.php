<!-- Interactive & Logic State Handler -->
<script>
  (function() {
    const adminWhatsapp = <?= json_encode($adminWhatsapp ?? '') ?>;
    
    // Main Mode Switcher Tabs (Top Up / Beli vs Jual / Bongkar)
    const tabModeBuy = document.getElementById('tab-mode-buy');
    const tabModeSell = document.getElementById('tab-mode-sell');
    const viewModeBuy = document.getElementById('view-mode-buy');
    const viewModeSell = document.getElementById('view-mode-sell');

    function switchMode(mode) {
      const mobileFooter = document.querySelector('.lg\\:hidden.fixed.bottom-0');
      if (mode === 'sell') {
        tabModeSell?.classList.add('active', 'bg-amber-500', 'text-neutral-950', 'shadow-xs', 'border-amber-400');
        tabModeSell?.classList.remove('text-slate-300');
        tabModeBuy?.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-xs', 'border-blue-500');
        tabModeBuy?.classList.add('text-slate-300');

        viewModeBuy?.classList.add('tab-mode-hidden');
        viewModeSell?.classList.remove('tab-mode-hidden');
        if (mobileFooter) mobileFooter?.classList.add('tab-mode-hidden');
      } else {
        tabModeBuy?.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-xs', 'border-blue-500');
        tabModeBuy?.classList.remove('text-slate-300');
        tabModeSell?.classList.remove('active', 'bg-amber-500', 'text-neutral-950', 'shadow-xs', 'border-amber-400');
        tabModeSell?.classList.add('text-slate-300');

        viewModeSell?.classList.add('tab-mode-hidden');
        viewModeBuy?.classList.remove('tab-mode-hidden');
        if (mobileFooter) mobileFooter?.classList.remove('tab-mode-hidden');
      }
    }

    tabModeBuy?.addEventListener('click', () => switchMode('buy'));
    tabModeSell?.addEventListener('click', () => switchMode('sell'));

    if (window.location.hash === '#jual' || window.location.hash === '#bongkar') {
      switchMode('sell');
    } else {
      switchMode('buy');
    }

    const state = {
      productId: null,
      itemTitle: "Belum memilih produk",
      category: "",
      categoryIcon: "",
      basePrice: 0,
      unitRate: "Silakan pilih nominal produk di samping",
      userId: "",
      whatsapp: "",
      payMethod: "QRIS Resmi",
      adminFee: 0,
      discount: 0,
      couponApplied: false,
      couponCode: "",
      bongkarCatalogId: null,
      bongkarType: "",
      bongkarRate: 0,
      bongkarUnit: "kartu",
      bongkarQty: 1,
      bongkarPayout: "BCA"
    };

    function formatRupiah(num) {
      return 'Rp' + num.toLocaleString('id-ID');
    }

    function updateReceiptUI() {
      const grandTotal = Math.max(0, state.basePrice + state.adminFee - state.discount);

      const receiptItemName = document.getElementById('receipt-item-name');
      const receiptUnitRate = document.getElementById('receipt-unit-rate');
      const receiptItemPrice = document.getElementById('receipt-item-price');
      const receiptIconContainer = document.getElementById('receipt-category-icon-container');
      const receiptUserId = document.getElementById('receipt-user-id');
      const receiptWa = document.getElementById('receipt-wa');
      const receiptMethod = document.getElementById('receipt-method');
      const calcSubtotal = document.getElementById('calc-subtotal');
      const calcAdminFee = document.getElementById('calc-admin-fee');
      const calcDiscountRow = document.getElementById('calc-discount-row');
      const calcDiscountVal = document.getElementById('calc-discount-val');
      const calcGrandTotal = document.getElementById('calc-grand-total');
      const mobileBottomTotal = document.getElementById('mobile-bottom-total');

      if (receiptItemName) receiptItemName.textContent = state.productId ? state.itemTitle : 'Belum memilih produk';
      if (receiptUnitRate) receiptUnitRate.textContent = state.productId ? state.unitRate : 'Silakan pilih nominal produk di samping';
      if (receiptItemPrice) receiptItemPrice.textContent = state.productId ? formatRupiah(state.basePrice) : 'Rp0';
      if (receiptIconContainer) {
        if (state.productId && state.categoryIcon) {
          receiptIconContainer.innerHTML = `<img src="${state.categoryIcon}" alt="" class="w-full h-full object-contain">`;
        } else {
          receiptIconContainer.innerHTML = `<span class="material-symbols-outlined text-[22px]">sports_esports</span>`;
        }
      }
      if (receiptUserId) receiptUserId.textContent = state.userId || '-';
      if (receiptWa) receiptWa.textContent = state.whatsapp || '-';
      if (receiptMethod) receiptMethod.textContent = state.payMethod || 'QRIS Resmi';
      if (calcSubtotal) calcSubtotal.textContent = formatRupiah(state.basePrice);
      if (calcAdminFee) {
        calcAdminFee.textContent = state.adminFee > 0 ? formatRupiah(state.adminFee) : 'Rp0 (Gratis)';
        calcAdminFee.className = state.adminFee > 0 ? 'font-bold text-slate-900 font-mono' : 'font-bold text-emerald-700 font-mono';
      }

      if (calcDiscountRow && calcDiscountVal) {
        if (state.discount > 0) {
          calcDiscountRow.classList.remove('hidden');
          calcDiscountVal.textContent = '-' + formatRupiah(state.discount);
        } else {
          calcDiscountRow.classList.add('hidden');
        }
      }

      if (calcGrandTotal) calcGrandTotal.textContent = formatRupiah(grandTotal);
      if (mobileBottomTotal) mobileBottomTotal.textContent = formatRupiah(grandTotal);
    }

    // Step 1: Category Pill Tabs & Quick Search
    const catPills = document.querySelectorAll('.category-pill');
    const categoryGroups = document.querySelectorAll('[data-category-group]');
    const catalogSearchInput = document.getElementById('catalog-search-input');

    function showCategoryGroup(slug) {
      categoryGroups.forEach(group => {
        const match = group.getAttribute('data-category-group') === slug;
        group.classList.toggle('hidden', !match);
      });
    }

    catPills.forEach(pill => {
      pill.addEventListener('click', () => {
        const slug = pill.getAttribute('data-cat');
        catPills.forEach(p => {
          p.classList.remove('active');
          p.classList.add('text-slate-700', 'bg-slate-100/90');
        });
        pill.classList.add('active');
        pill.classList.remove('text-slate-700', 'bg-slate-100/90');
        showCategoryGroup(slug);
        if (catalogSearchInput) catalogSearchInput.value = '';
      });
    });

    if (catalogSearchInput) {
      catalogSearchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        const productCards = document.querySelectorAll('.product-card');
        productCards.forEach(card => {
          const title = (card.getAttribute('data-title') || '').toLowerCase();
          const unit = (card.getAttribute('data-unit') || '').toLowerCase();
          const match = title.includes(query) || unit.includes(query);
          card.classList.toggle('hidden', !match && query !== '');
        });
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
        });
        card.classList.add('active', 'product-card-selected');
        card.classList.remove('border-slate-200', 'bg-white');

        state.productId = card.getAttribute('data-id') || null;
        state.itemTitle = card.getAttribute('data-title') || "";
        state.basePrice = parseInt(card.getAttribute('data-price') || "0", 10);
        state.unitRate = card.getAttribute('data-unit') || "";
        state.categoryIcon = card.getAttribute('data-icon') || "";
        updateReceiptUI();
      });
    });

    // Step 3: User ID & WhatsApp Binding
    const inputUserId = document.getElementById('input-user-id');
    const inputWa = document.getElementById('input-whatsapp');

    if (inputUserId) {
      inputUserId.addEventListener('input', (e) => {
        state.userId = e.target.value.trim();
        updateReceiptUI();
      });
    }

    if (inputWa) {
      inputWa.addEventListener('input', (e) => {
        state.whatsapp = e.target.value.trim();
        updateReceiptUI();
      });
    }

    // Step 4: Payment Method Selection
    const payMethodCards = document.querySelectorAll('.pay-method-card');
    payMethodCards.forEach(payCard => {
      payCard.addEventListener('click', () => {
        payMethodCards.forEach(p => {
          p.classList.remove('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          p.classList.add('border-slate-200', 'bg-white');
        });
        payCard.classList.add('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        payCard.classList.remove('border-slate-200', 'bg-white');

        state.payMethod = payCard.getAttribute('data-method') || "QRIS Resmi";
        state.adminFee = parseInt(payCard.getAttribute('data-fee') || "0", 10);
        updateReceiptUI();
      });
    });

    // Promo Coupon Logic
    const btnSampleCoupon = document.getElementById('btn-sample-coupon');
    const receiptPromoInput = document.getElementById('receipt-promo-input');
    const btnApplyCoupon = document.getElementById('btn-apply-coupon');
    const promoStatus = document.getElementById('promo-status');

    if (btnSampleCoupon && receiptPromoInput) {
      btnSampleCoupon.addEventListener('click', () => {
        receiptPromoInput.value = 'AYONGHEMAT';
      });
    }

    if (btnApplyCoupon && receiptPromoInput) {
      btnApplyCoupon.addEventListener('click', () => {
        const code = receiptPromoInput.value.trim().toUpperCase();
        if (code === 'AYONGHEMAT') {
          state.discount = 1000;
          state.couponApplied = true;
          state.couponCode = code;
          if (promoStatus) {
            promoStatus.classList.remove('hidden', 'text-rose-600');
            promoStatus.classList.add('text-emerald-600');
            promoStatus.textContent = '✓ Potongan Rp1.000 berhasil diterapkan!';
          }
        } else if (code !== '') {
          state.discount = 0;
          state.couponApplied = false;
          state.couponCode = '';
          if (promoStatus) {
            promoStatus.classList.remove('hidden', 'text-emerald-600');
            promoStatus.classList.add('text-rose-600');
            promoStatus.textContent = '✗ Kode voucher tidak valid / kadaluarsa';
          }
        }
        updateReceiptUI();
      });
    }

    // Modal Guide ID
    const btnGuideId = document.getElementById('btn-guide-id');
    const guideModal = document.getElementById('guide-modal');
    const btnCloseGuideModal = document.getElementById('btn-close-guide-modal');
    const btnUnderstandGuide = document.getElementById('btn-understand-guide');

    function toggleGuideModal(show) {
      if (guideModal) {
        guideModal.classList.toggle('hidden', !show);
      }
    }

    btnGuideId?.addEventListener('click', () => toggleGuideModal(true));
    btnCloseGuideModal?.addEventListener('click', () => toggleGuideModal(false));
    btnUnderstandGuide?.addEventListener('click', () => toggleGuideModal(false));

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

    function openCheckoutModal() {
      if (!state.productId) {
        alert('Silakan pilih nominal produk koin/item yang ingin Anda beli terlebih dahulu.');
        return;
      }
      if (!state.userId) {
        alert('Silakan masukkan User ID Game Anda.');
        inputUserId?.focus();
        return;
      }
      if (!state.whatsapp) {
        alert('Silakan masukkan nomor WhatsApp Anda untuk mengirimkan bukti transaksi.');
        inputWa?.focus();
        return;
      }

      const grandTotal = Math.max(0, state.basePrice + state.adminFee - state.discount);
      if (modalItem) modalItem.textContent = state.itemTitle;
      if (modalId) modalId.textContent = state.userId;
      if (modalMethod) modalMethod.textContent = state.payMethod || 'QRIS Resmi';
      if (modalTotal) modalTotal.textContent = formatRupiah(grandTotal);

      checkoutModal?.classList.remove('hidden');
    }

    function closeCheckoutModal() {
      checkoutModal?.classList.add('hidden');
    }

    btnPayNow?.addEventListener('click', openCheckoutModal);
    btnMobileCheckout?.addEventListener('click', openCheckoutModal);
    btnCloseModal?.addEventListener('click', closeCheckoutModal);
    btnCancelCheckout?.addEventListener('click', closeCheckoutModal);

    // Form Submission Trigger
    btnSubmitPay?.addEventListener('click', () => {
      if (!state.productId) return;
      const form = document.getElementById('backend-checkout-form');
      const hiddenGameId = document.getElementById('hidden-game-id');
      const hiddenWa = document.getElementById('hidden-whatsapp');
      const hiddenVoucher = document.getElementById('hidden-voucher');

      if (form) {
        form.action = '<?= base_url("checkout/") ?>' + state.productId;
        if (hiddenGameId) hiddenGameId.value = state.userId;
        if (hiddenWa) hiddenWa.value = state.whatsapp;
        if (hiddenVoucher) hiddenVoucher.value = state.couponCode || '';
        form.submit();
      }
    });

    // --- Bongkar / Jual Mode Interactive Script ---
    const bongkarCards = document.querySelectorAll('.bongkar-card');
    const bongkarQtyInput = document.getElementById('input-card-qty');
    const bongkarWaInput = document.getElementById('input-sell-wa');
    const bongkarGameIdInput = document.getElementById('input-card-game-id');
    const bongkarPayoutAccountInput = document.getElementById('input-payout-account');
    const bongkarPayoutNameInput = document.getElementById('input-payout-name');
    const bongkarPayoutButtons = document.querySelectorAll('.bongkar-payout-btn');
    const btnSubmitBongkar = document.getElementById('btn-submit-bongkar');
    const btnQtyMinus = document.getElementById('btn-qty-minus');
    const btnQtyPlus = document.getElementById('btn-qty-plus');

    const firstBongkarCard = document.querySelector('.bongkar-card');
    if (firstBongkarCard) {
      state.bongkarCatalogId = firstBongkarCard.getAttribute('data-bongkar-catalog-id');
      state.bongkarType = firstBongkarCard.getAttribute('data-label') || '';
      state.bongkarRate = parseInt(firstBongkarCard.getAttribute('data-rate') || '0', 10);
      state.bongkarUnit = firstBongkarCard.getAttribute('data-unit') || 'kartu';
    }

    function refreshBongkarUI() {
      const rate = state.bongkarRate || 0;
      const qty = state.bongkarQty || 1;
      const total = qty * rate;

      const bongkarEstimated = document.getElementById('bongkar-estimated');
      const bongkarReceiptLabel = document.getElementById('bongkar-receipt-label');
      const bongkarReceiptRate = document.getElementById('bongkar-receipt-rate');
      const bongkarReceiptQty = document.getElementById('bongkar-receipt-qty');
      const bongkarReceiptPayout = document.getElementById('bongkar-receipt-payout');
      const bongkarReceiptWa = document.getElementById('bongkar-receipt-wa');
      const bongkarUnitLabel = document.getElementById('bongkar-unit-label');

      if (bongkarEstimated) bongkarEstimated.textContent = formatRupiah(total);
      if (bongkarReceiptLabel) bongkarReceiptLabel.textContent = state.bongkarType || 'Belum memilih item';
      if (bongkarReceiptRate) bongkarReceiptRate.textContent = rate > 0 ? `${formatRupiah(rate)} / ${state.bongkarUnit}` : '-';
      if (bongkarReceiptQty) bongkarReceiptQty.textContent = `${qty} ${state.bongkarUnit}`;
      if (bongkarReceiptPayout) bongkarReceiptPayout.textContent = state.bongkarPayout || 'BCA';
      if (bongkarReceiptWa) bongkarReceiptWa.textContent = (bongkarWaInput ? bongkarWaInput.value.trim() : '') || '-';
      if (bongkarUnitLabel) bongkarUnitLabel.textContent = state.bongkarUnit || 'kartu';
    }

    bongkarCards.forEach(card => {
      card.addEventListener('click', () => {
        bongkarCards.forEach(c => {
          c.classList.remove('selected', 'border-2', 'border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/20');
          c.classList.add('border-slate-200', 'bg-white');
        });
        card.classList.add('selected', 'border-2', 'border-amber-500', 'bg-amber-50/70', 'ring-2', 'ring-amber-400/20');
        card.classList.remove('border-slate-200', 'bg-white');

        state.bongkarCatalogId = card.getAttribute('data-bongkar-catalog-id');
        state.bongkarType = card.getAttribute('data-label') || '';
        state.bongkarRate = parseInt(card.getAttribute('data-rate') || '0', 10);
        state.bongkarUnit = card.getAttribute('data-unit') || 'kartu';
        refreshBongkarUI();
      });
    });

    if (btnQtyMinus && bongkarQtyInput) {
      btnQtyMinus.addEventListener('click', () => {
        let val = parseInt(bongkarQtyInput.value || '1', 10);
        if (val > 1) {
          bongkarQtyInput.value = val - 1;
          state.bongkarQty = val - 1;
          refreshBongkarUI();
        }
      });
    }

    if (btnQtyPlus && bongkarQtyInput) {
      btnQtyPlus.addEventListener('click', () => {
        let val = parseInt(bongkarQtyInput.value || '1', 10);
        if (val < 1000) {
          bongkarQtyInput.value = val + 1;
          state.bongkarQty = val + 1;
          refreshBongkarUI();
        }
      });
    }

    if (bongkarQtyInput) {
      bongkarQtyInput.addEventListener('input', (e) => {
        let val = parseInt(e.target.value || '1', 10);
        if (val < 1) val = 1;
        if (val > 1000) val = 1000;
        state.bongkarQty = val;
        refreshBongkarUI();
      });
    }

    if (bongkarWaInput) {
      bongkarWaInput.addEventListener('input', () => {
        refreshBongkarUI();
      });
    }

    bongkarPayoutButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        bongkarPayoutButtons.forEach(b => {
          b.classList.remove('selected', 'border-2', 'border-amber-500', 'bg-amber-50', 'font-bold', 'text-amber-900');
          b.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
        });
        btn.classList.add('selected', 'border-2', 'border-amber-500', 'bg-amber-50', 'font-bold', 'text-amber-900');
        btn.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');

        state.bongkarPayout = btn.getAttribute('data-method') || 'BCA';
        refreshBongkarUI();
      });
    });

    btnSubmitBongkar?.addEventListener('click', async () => {
      if (!state.bongkarCatalogId) {
        alert('Silakan pilih jenis kartu/koin yang ingin dijual terlebih dahulu.');
        return;
      }

      const wa = bongkarWaInput ? bongkarWaInput.value.trim() : '';
      const account = bongkarPayoutAccountInput ? bongkarPayoutAccountInput.value.trim() : '';
      const name = bongkarPayoutNameInput ? bongkarPayoutNameInput.value.trim() : '';
      const gameId = bongkarGameIdInput ? bongkarGameIdInput.value.trim() : '';

      if (!wa) {
        alert('Silakan masukkan nomor WhatsApp Anda.');
        bongkarWaInput?.focus();
        return;
      }

      if (!account || !name) {
        alert('Silakan lengkapi nomor rekening/e-wallet dan nama pemilik rekening tujuan pencairan.');
        bongkarPayoutAccountInput?.focus();
        return;
      }

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
          if (resData.wa_url) {
            window.location.href = resData.wa_url;
          } else {
            alert('Pengajuan bongkar berhasil dikirim! Tim CS kami akan segera menghubungi Anda via WhatsApp.');
          }
        } else {
          alert(resData.messages?.error || resData.error || 'Pengajuan bongkar gagal disimpan. Silakan periksa kembali data Anda.');
        }
      } catch (err) {
        alert('Terjadi kesalahan koneksi. Silakan coba lagi.');
      }
    });

    // Initial state refresh
    updateReceiptUI();
    refreshBongkarUI();
  })();
</script>
