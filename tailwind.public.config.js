/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/Views/catalog/**/*.php',
    './app/Views/checkout/**/*.php',
    './app/Views/layouts/public.php',
    './app/Views/layouts/partials/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        background: '#f8f9ff',
        surface: '#ffffff',
        'surface-card': '#ffffff',
        'surface-border': '#e2e8f0',
        primary: '#004ac6',
        'primary-container': '#2563eb',
        'gold-accent': '#F59E0B',
        'gold-light': '#FEF3C7',
        'emerald-table': '#059669',
        'neutral-900': '#0F172A',
        'neutral-800': '#1E293B',
        'neutral-700': '#334155',
        'neutral-600': '#475569',
        'neutral-500': '#64748B',
        'neutral-200': '#E2E8F0',
        'neutral-100': '#F1F5F9',
        'neutral-50': '#F8FAFC',
        success: '#16A34A',
        warning: '#F59E0B',
        danger: '#DC2626',
      },
      fontFamily: {
        sans: ['Inter', 'sans-serif'],
        display: ['Plus Jakarta Sans', 'sans-serif'],
      },
      borderRadius: {
        DEFAULT: '0.375rem',
        md: '0.5rem',
        lg: '0.75rem',
        xl: '1rem',
        '2xl': '1.25rem',
        full: '9999px',
      },
    },
  },
  plugins: [],
}
