<!-- State & Interactive Script -->
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
        tabModeSell?.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
        tabModeSell?.classList.remove('text-slate-700');
        tabModeBuy?.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
        tabModeBuy?.classList.add('text-slate-700');

        viewModeBuy?.classList.add('tab-mode-hidden');
        viewModeSell?.classList.remove('tab-mode-hidden');
        if (mobileFooter) mobileFooter?.classList.add('tab-mode-hidden');
      } else {
        tabModeBuy?.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
        tabModeBuy?.classList.remove('text-slate-700');
        tabModeSell?.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
        tabModeSell?.classList.add('text-slate-700');

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
      itemTitle: "1B (1 Miliar) Koin Emas",
      category: "Koin Emas",
      basePrice: 63000,
      unitRate: "Rp63.000 / 1B",
      userId: "123456789",
      nickname: "HiggsMaster",
      whatsapp: "081234567890",
      payMethod: "QRIS Resmi",
      adminFee: 0,
      discount: 0,
      couponApplied: false,
      bongkarType: "Kartu Ungu",
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
      const receiptUserId = document.getElementById('receipt-user-id');
      const receiptWa = document.getElementById('receipt-wa');
      const receiptMethod = document.getElementById('receipt-method');
      const calcSubtotal = document.getElementById('calc-subtotal');
      const calcAdminFee = document.getElementById('calc-admin-fee');
      const calcDiscountRow = document.getElementById('calc-discount-row');
      const calcDiscountVal = document.getElementById('calc-discount-val');
      const calcGrandTotal = document.getElementById('calc-grand-total');
      const mobileBottomTotal = document.getElementById('mobile-bottom-total');

      if (receiptItemName) receiptItemName.textContent = state.itemTitle;
      if (receiptUnitRate) receiptUnitRate.textContent = state.unitRate;
      if (receiptItemPrice) receiptItemPrice.textContent = formatRupiah(state.basePrice);
      if (receiptUserId) receiptUserId.textContent = state.userId || '-';
      if (receiptWa) receiptWa.textContent = state.whatsapp || '-';
      if (receiptMethod) receiptMethod.textContent = state.payMethod;
      if (calcSubtotal) calcSubtotal.textContent = formatRupiah(state.basePrice);
      if (calcAdminFee) {
        calcAdminFee.textContent = state.adminFee > 0 ? formatRupiah(state.adminFee) : 'Rp0 (Gratis)';
        calcAdminFee.className = state.adminFee > 0 ? 'font-bold text-neutral-900 font-mono' : 'font-bold text-emerald-700 font-mono';
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

    // Step 1: Category Pill Tabs
    const catPills = document.querySelectorAll('.category-pill');
    const categoryGroups = document.querySelectorAll('[data-category-group]');

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
          p.classList.remove('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
          p.classList.add('text-slate-700');
        });
        pill.classList.add('active', 'bg-blue-600', 'text-white', 'shadow-sm', 'border-blue-700');
        pill.classList.remove('text-slate-700');
        showCategoryGroup(slug);
      });
    });

    const firstCategory = document.querySelector('.category-pill.active');
    if (firstCategory) {
      showCategoryGroup(firstCategory.getAttribute('data-cat'));
    }

    // Step 2: Product Nominal Selection
    const productCards = document.querySelectorAll('.product-card');
    if (productCards.length > 0) {
      const firstActive = document.querySelector('.product-card.active') || productCards[0];
      if (firstActive) {
        state.productId = firstActive.getAttribute('data-id') || null;
        state.itemTitle = firstActive.getAttribute('data-title') || state.itemTitle;
        state.basePrice = parseInt(firstActive.getAttribute('data-price') || "63000", 10);
        state.unitRate = firstActive.getAttribute('data-unit') || state.unitRate;
      }
    }

    productCards.forEach(card => {
      card.addEventListener('click', () => {
        productCards.forEach(c => {
          c.classList.remove('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
          c.classList.add('border-slate-200', 'bg-white');
        });
        card.classList.add('active', 'border-2', 'border-blue-600', 'bg-blue-50/70', 'ring-2', 'ring-blue-500/20');
        card.classList.remove('border-slate-200', 'bg-white');

        state.productId = card.getAttribute('data-id') || null;
        state.itemTitle = card.getAttribute('data-title') || "";
        state.basePrice = parseInt(card.getAttribute('data-price') || "0", 10);
        state.unitRate = card.getAttribute('data-unit') || "";
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

    function normalizePhone(phone) {
      return String(phone || '').replace(/\D+/g, '').replace(/^0/, '62');
    }

    function refreshBongkarEstimate() {
      const rate = parseInt(state.bongkarRate || '65000', 10);
      const qty = state.bongkarQty || 1;
      const unit = state.bongkarUnit || 'kartu';
      const estimated = qty * rate;

      // Main estimated total
      const estimatedEl = document.getElementById('bongkar-estimated');
      if (estimatedEl) estimatedEl.textContent = formatRupiah(estimated);

      // Unit label in qty input
      const unitLabelEl = document.getElementById('bongkar-unit-label');
      if (unitLabelEl) unitLabelEl.textContent = unit;

      // Right column summary card
      const receiptLabel = document.getElementById('bongkar-receipt-label');
      const receiptRate  = document.getElementById('bongkar-receipt-rate');
      const receiptQty   = document.getElementById('bongkar-receipt-qty');
      const receiptPayout = document.getElementById('bongkar-receipt-payout');
      const receiptWa    = document.getElementById('bongkar-receipt-wa');

      if (receiptLabel)  receiptLabel.textContent  = state.bongkarType || 'Kartu Ungu';
      if (receiptRate)   receiptRate.textContent   = `${formatRupiah(rate)} / ${unit}`;
      if (receiptQty)    receiptQty.textContent    = `${qty} ${unit}`;
      if (receiptPayout) receiptPayout.textContent = state.bongkarPayout || 'BCA';
      if (receiptWa)     receiptWa.textContent     = (bongkarWaInput ? bongkarWaInput.value.trim() : '') || '-';
    }

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

    if (btnQtyMinus && bongkarQtyInput) {
      btnQtyMinus.addEventListener('click', () => {
        let val = parseInt(bongkarQtyInput.value || '1', 10);
        if (val > 1) {
          bongkarQtyInput.value = val - 1;
          state.bongkarQty = val - 1;
          refreshBongkarEstimate();
        }
      });
    }

    if (btnQtyPlus && bongkarQtyInput) {
      btnQtyPlus.addEventListener('click', () => {
        let val = parseInt(bongkarQtyInput.value || '1', 10);
        bongkarQtyInput.value = val + 1;
        state.bongkarQty = val + 1;
        refreshBongkarEstimate();
      });
    }

    if (bongkarCards.length > 0) {
      const selectedBongkarCard = document.querySelector('.bongkar-card.selected') || bongkarCards[0];
      if (selectedBongkarCard) {
        state.bongkarType = selectedBongkarCard.getAttribute('data-label') || state.bongkarType;
        state.bongkarRate = selectedBongkarCard.getAttribute('data-rate') || state.bongkarRate;
        state.bongkarUnit = selectedBongkarCard.getAttribute('data-unit') || state.bongkarUnit;
      }
    }
    refreshBongkarEstimate();

    bongkarCards.forEach(card => {
      card.addEventListener('click', () => {
        bongkarCards.forEach(item => {
          item.classList.remove('selected', 'border-2', 'border-blue-600', 'bg-blue-50/80', 'ring-2', 'ring-blue-500/20');
          item.classList.add('border-slate-200', 'bg-white');
        });
        card.classList.add('selected', 'border-2', 'border-blue-600', 'bg-blue-50/80', 'ring-2', 'ring-blue-500/20');
        card.classList.remove('border-slate-200', 'bg-white');
        state.bongkarType = card.getAttribute('data-label') || state.bongkarType;
        state.bongkarRate = card.getAttribute('data-rate') || state.bongkarRate;
        state.bongkarUnit = card.getAttribute('data-unit') || state.bongkarUnit;
        refreshBongkarEstimate();
      });
    });

    if (bongkarQtyInput) {
      bongkarQtyInput.addEventListener('input', (e) => {
        const qty = Math.max(1, parseInt(e.target.value || '1', 10) || 1);
        state.bongkarQty = qty;
        refreshBongkarEstimate();
      });
    }

    if (bongkarWaInput) {
      bongkarWaInput.addEventListener('input', (e) => {
        state.bongkarWa = e.target.value.trim();
        refreshBongkarEstimate();
      });
    }

    bongkarPayoutButtons.forEach(button => {
      button.addEventListener('click', () => {
        bongkarPayoutButtons.forEach(item => {
          item.classList.remove('border-2', 'border-blue-600', 'bg-blue-50/80', 'text-blue-700', 'shadow-2xs');
          item.classList.add('border-slate-200', 'bg-white', 'text-slate-700');
        });
        button.classList.add('border-2', 'border-blue-600', 'bg-blue-50/80', 'text-blue-700', 'shadow-2xs');
        button.classList.remove('border-slate-200', 'bg-white', 'text-slate-700');
        state.bongkarPayout = button.getAttribute('data-bongkar-payout') || button.textContent.trim();
        refreshBongkarEstimate();
      });
    });

    if (btnSubmitBongkar) {
      btnSubmitBongkar.addEventListener('click', () => {
        const phone = normalizePhone(adminWhatsapp);
        const qty = Math.max(1, parseInt(bongkarQtyInput?.value || '1', 10) || 1);
        const sellerWa = bongkarWaInput ? bongkarWaInput.value.trim() : '';
        const sellerGameId = bongkarGameIdInput ? bongkarGameIdInput.value.trim() : '-';
        const accountNo = bongkarPayoutAccountInput ? bongkarPayoutAccountInput.value.trim() : '';
        const accountName = bongkarPayoutNameInput ? bongkarPayoutNameInput.value.trim() : '';
        const payoutBank = state.bongkarPayout || 'BCA';
        const rate = parseInt(state.bongkarRate || '65000', 10);
        const estimated = qty * rate;

        if (!state.bongkarType || !sellerWa) {
          alert('Silakan lengkapi jenis kartu, jumlah, dan nomor WhatsApp konfirmasi terlebih dahulu.');
          return;
        }

        if (!accountNo || !accountName) {
          alert('Silakan isi Nomor Rekening / E-Wallet dan Nama Pemilik Rekening untuk tujuan pencairan saldo.');
          if (!accountNo && bongkarPayoutAccountInput) bongkarPayoutAccountInput.focus();
          else if (!accountName && bongkarPayoutNameInput) bongkarPayoutNameInput.focus();
          return;
        }

        if (!phone) {
          alert('Nomor WhatsApp admin belum diset di pengaturan toko.');
          return;
        }

        const message = [
          '⚡ *PENGAJUAN BONGKAR / JUAL KARTU* ⚡',
          '=============================',
          `• *Jenis Item*: ${state.bongkarType}`,
          `• *Jumlah*: ${qty} ${state.bongkarUnit || 'kartu'}`,
          `• *Rate*: Rp${rate.toLocaleString('id-ID')} / ${state.bongkarUnit || 'kartu'}`,
          `• *Total Estimasi Cair*: *Rp${estimated.toLocaleString('id-ID')}*`,
          '---------------------------------------------',
          `• *User ID Higgs Pengirim*: ${sellerGameId || '-'}`,
          `• *WA Seller*: ${sellerWa}`,
          '---------------------------------------------',
          '🏦 *REKENING PENCAIRAN SALDO*:',
          `• *Bank / E-Wallet*: ${payoutBank}`,
          `• *No. Rekening/HP*: ${accountNo}`,
          `• *Atas Nama*: ${accountName}`,
          '=============================',
          'Mohon info ID Higgs Admin & instruksi transfer kartu.',
          'Terima kasih!'
        ].join('\n');

        window.open(`https://wa.me/${phone}?text=${encodeURIComponent(message)}`, '_blank', 'noopener,noreferrer');
      });
    }

    // Coupon Voucher
    const btnApplyCoupon = document.getElementById('btn-apply-coupon');
    const promoInput = document.getElementById('receipt-promo-input');
    const promoStatus = document.getElementById('promo-status');

    if (btnApplyCoupon && promoInput && promoStatus) {
      btnApplyCoupon.addEventListener('click', () => {
        const code = promoInput.value.trim().toUpperCase();
        promoStatus.classList.remove('hidden');
        if (code === "AYONGHEMAT") {
          state.discount = 5000;
          state.couponApplied = true;
          promoStatus.className = "text-[11px] font-bold text-emerald-700 block";
          promoStatus.textContent = "Kupon AYONGHEMAT berhasil dipakai! Potongan Rp5.000 aktif.";
        } else if (!code) {
          state.discount = 0;
          promoStatus.className = "text-[11px] text-rose-600 block";
          promoStatus.textContent = "Silakan ketikkan kode voucher.";
        } else {
          state.discount = 0;
          promoStatus.className = "text-[11px] text-rose-600 block";
          promoStatus.textContent = "Kode tidak valid atau kuota klaim habis.";
        }
        updateReceiptUI();
      });
    }

    // Modal Simulation & Backend Checkout
    const checkoutModal = document.getElementById('checkout-modal');
    const btnPayNow = document.getElementById('btn-pay-now');
    const btnMobileCheckout = document.getElementById('btn-mobile-checkout');
    const btnCloseModal = document.getElementById('btn-close-modal');
    const btnCancelCheckout = document.getElementById('btn-cancel-checkout');
    const btnSubmitPay = document.getElementById('btn-submit-pay');

    function openModal() {
      if (!state.userId) {
        alert("Silakan ketik ID Akun Higgs Games Island Anda terlebih dahulu!");
        inputUserId?.focus();
        return;
      }

      const grandTotal = Math.max(0, state.basePrice + state.adminFee - state.discount);
      document.getElementById('modal-item').textContent = state.itemTitle;
      document.getElementById('modal-id').textContent = state.userId;
      document.getElementById('modal-method').textContent = state.payMethod;
      document.getElementById('modal-total').textContent = formatRupiah(grandTotal);

      if (checkoutModal) checkoutModal.classList.remove('hidden');
    }

    function closeModal() {
      if (checkoutModal) checkoutModal.classList.add('hidden');
    }

    btnPayNow?.addEventListener('click', openModal);
    btnMobileCheckout?.addEventListener('click', openModal);
    btnCloseModal?.addEventListener('click', closeModal);
    btnCancelCheckout?.addEventListener('click', closeModal);

    btnSubmitPay?.addEventListener('click', () => {
      btnSubmitPay.innerHTML = '<span class="material-symbols-outlined text-[16px] animate-spin">refresh</span> <span>Membuka Pembayaran...</span>';
      
      if (state.productId) {
        const form = document.getElementById('backend-checkout-form');
        document.getElementById('hidden-game-id').value = state.userId;
        document.getElementById('hidden-whatsapp').value = state.whatsapp;
        document.getElementById('hidden-voucher').value = promoInput ? promoInput.value.trim() : '';
        form.action = '<?= base_url("checkout/") ?>' + state.productId;
        form.submit();
        return;
      }

      setTimeout(() => {
        closeModal();
        btnSubmitPay.innerHTML = '<span>Lanjut ke Pembayaran</span> <span class="material-symbols-outlined text-[16px]">arrow_forward</span>';
        alert(`Simulasi Pembayaran Berhasil!\n\nID Pemain: ${state.userId}\nItem: ${state.itemTitle}\nMetode: ${state.payMethod}\nTotal: ${formatRupiah(state.basePrice + state.adminFee - state.discount)}\n\nKoin HGD otomatis diproses & masuk ke akun game Anda dalam 1-3 detik via API Server Resmi.`);
      }, 750);
    });

    updateReceiptUI();
  })();
</script>


