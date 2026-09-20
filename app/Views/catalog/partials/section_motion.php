<!-- Shared by the buy and sell sections: motion styles, step-done badge, and the message toast -->
<style>
  /* While a dialog is open the page behind it does not scroll. */
  html.modal-open {
    overflow: hidden;
  }

  /* Editorial rows: a hairline on top of each. Static by default, animated in the block below. */
  .ruled {
    position: relative;
  }
  .ruled::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: #cbd5e1;
    transform-origin: left;
  }

  /* A finished step swaps its number for a check. Not motion, so it works with reduced motion too. */
  .step-number-badge.is-done {
    background: #047857;
    box-shadow: 0 4px 10px rgba(4, 120, 87, 0.35);
  }

  @media (prefers-reduced-motion: no-preference) {
    /* Sections rise into view once, in order. The hidden start state exists only after JS adds .reveal-ready. */
    .reveal-ready [data-reveal] {
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 650ms cubic-bezier(0.2, 0.8, 0.2, 1), transform 650ms cubic-bezier(0.2, 0.8, 0.2, 1);
      transition-delay: var(--rd, 0ms);
    }
    .reveal-ready [data-reveal].is-visible {
      opacity: 1;
      transform: none;
    }

    /* Switching category: the new nominal cards cascade in. backwards fill, so hover lift still works afterwards. */
    [data-category-group]:not(.hidden) .product-card {
      animation: card-in 420ms cubic-bezier(0.2, 0.8, 0.2, 1) backwards;
      animation-delay: calc(var(--i, 0) * 35ms);
    }
    @keyframes card-in {
      from { opacity: 0; transform: translateY(12px) scale(0.98); }
      to   { opacity: 1; transform: none; }
    }

    /* A value in the summary lights up when it changes, tying the choice on the left to the receipt on the right. */
    .value-flash {
      animation: value-flash 1000ms ease-out;
    }
    @keyframes value-flash {
      0%   { background-color: rgba(250, 204, 21, 0.5); box-shadow: 0 0 0 4px rgba(250, 204, 21, 0.5); }
      100% { background-color: rgba(250, 204, 21, 0); box-shadow: 0 0 0 4px rgba(250, 204, 21, 0); }
    }

    /* Check marks and step badges pop when they change state. */
    .pop-in {
      animation: pop-in 420ms cubic-bezier(0.3, 1.6, 0.5, 1);
    }
    @keyframes pop-in {
      0%   { transform: scale(0.6); }
      60%  { transform: scale(1.15); }
      100% { transform: scale(1); }
    }

    /* The hairline above each row draws itself left to right, and its step number turns blue as the line lands. */
    .reveal-ready .ruled::before {
      transition: transform 900ms cubic-bezier(0.2, 0.8, 0.2, 1);
      transition-delay: var(--rd, 0ms);
    }
    .reveal-ready .ruled:not(.is-visible)::before {
      transform: scaleX(0);
    }
    .reveal-ready .ruled .step-num {
      color: #64748b;
      transition: color 700ms ease;
      transition-delay: calc(var(--rd, 0ms) + 400ms);
    }
    .reveal-ready .ruled.is-visible .step-num {
      color: #1d4ed8;
    }

    /* An FAQ answer eases open instead of appearing at once. */
    details[open] > .faq-body {
      animation: faq-in 340ms cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    @keyframes faq-in {
      from { opacity: 0; transform: translateY(-6px); }
      to   { opacity: 1; transform: none; }
    }

    /* Dialogs: the backdrop fades, the panel rises and settles, and its rows follow one by one. */
    .dialog-backdrop:not(.hidden) {
      animation: backdrop-in 240ms ease-out;
    }
    .dialog-backdrop:not(.hidden) > .dialog-panel {
      animation: dialog-in 340ms cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    .dialog-backdrop:not(.hidden) .dialog-item {
      animation: card-in 420ms cubic-bezier(0.2, 0.8, 0.2, 1) backwards;
      animation-delay: calc(140ms + var(--i, 0) * 70ms);
    }
    @keyframes backdrop-in {
      from { opacity: 0; }
      to   { opacity: 1; }
    }
    @keyframes dialog-in {
      from { opacity: 0; transform: translateY(18px) scale(0.96); }
      to   { opacity: 1; transform: none; }
    }

    /* A message that needs attention shakes once. */
    .shake {
      animation: shake 420ms cubic-bezier(0.36, 0.07, 0.19, 0.97);
    }
    @keyframes shake {
      10%, 90% { transform: translateX(-1px); }
      20%, 80% { transform: translateX(2px); }
      30%, 50%, 70% { transform: translateX(-4px); }
      40%, 60% { transform: translateX(4px); }
    }

    #form-toast:not(.hidden) > div {
      animation: toast-in 320ms cubic-bezier(0.2, 0.8, 0.2, 1);
    }
    @keyframes toast-in {
      from { opacity: 0; transform: translateY(14px); }
      to   { opacity: 1; transform: none; }
    }
  }
</style>

<!-- Validation and result messages. Replaces blocking alert() dialogs; sits above the mobile bars. -->
<div id="form-toast" class="hidden fixed inset-x-4 bottom-[140px] z-[1000] md:inset-x-auto md:bottom-6 md:right-6 md:w-[26rem]" role="alert">
  <div id="form-toast-box" class="flex items-start gap-3 rounded-xl border border-rose-300 bg-white p-4 shadow-lg" data-tone="error">
    <span id="form-toast-icon" class="material-symbols-outlined mt-px text-[22px] text-rose-600" aria-hidden="true">error</span>
    <p id="form-toast-text" class="min-w-0 flex-1 text-sm font-semibold leading-snug text-slate-800"></p>
    <button type="button" id="form-toast-close" class="-m-2 flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-slate-600 transition-colors hover:bg-slate-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600" aria-label="Tutup pesan">
      <span class="material-symbols-outlined text-[20px]" aria-hidden="true">close</span>
    </button>
  </div>
</div>
