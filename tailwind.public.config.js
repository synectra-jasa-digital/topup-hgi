/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/Views/catalog/**/*.php',
    './app/Views/checkout/**/*.php',
    './app/Views/layouts/**/*.php',
  ],
  theme: {
    extend: {
      colors: {
        background: '#f1f5f9',
        surface: '#ffffff',
        'surface-card': '#ffffff',
        'surface-border': '#e2e8f0',
        primary: {
          DEFAULT: '#2563eb',
          dark: '#1d4ed8',
          light: '#dbeafe',
        },
        accent: {
          gold: '#f59e0b',
          'gold-light': '#fef3c7',
          cyan: '#06b6d4',
          emerald: '#10b981',
          purple: '#8b5cf6',
        },
        neutral: {
          950: '#020617',
          900: '#0f172a',
          800: '#1e293b',
          700: '#334155',
          600: '#475569',
          500: '#64748b',
          400: '#94a3b8',
          300: '#cbd5e1',
          200: '#e2e8f0',
          100: '#f1f5f9',
          50: '#f8fafc',
        },
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        display: ['"Plus Jakarta Sans"', 'Inter', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'glow-blue': '0 0 20px rgba(37, 99, 235, 0.35)',
        'glow-gold': '0 0 20px rgba(245, 158, 11, 0.35)',
        'glow-emerald': '0 0 20px rgba(16, 185, 129, 0.35)',
      },
      borderRadius: {
        DEFAULT: '0.375rem',
        md: '0.5rem',
        lg: '0.75rem',
        xl: '1rem',
        '2xl': '1.25rem',
        '3xl': '1.5rem',
        full: '9999px',
      },
    },
  },
  plugins: [],
}
