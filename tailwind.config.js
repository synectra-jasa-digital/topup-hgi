/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./app/Views/**/*.php'],
  theme: {
    extend: {
      colors: {
        surface: '#f8f9ff',
        'surface-dim': '#d0dbed',
        'surface-bright': '#f8f9ff',
        'surface-container-lowest': '#ffffff',
        'surface-container-low': '#eff4ff',
        'surface-container': '#e6eeff',
        'surface-container-high': '#dee9fc',
        'surface-container-highest': '#d9e3f6',
        'on-surface': '#121c2a',
        'on-surface-variant': '#434655',
        'inverse-surface': '#27313f',
        'inverse-on-surface': '#eaf1ff',
        outline: '#737686',
        'outline-variant': '#c3c6d7',
        'surface-tint': '#0053db',
        'surface-white': '#FFFFFF',
        
        primary: {
          DEFAULT: '#004ac6',
          container: '#2563eb',
          fixed: '#dbe1ff',
          'fixed-dim': '#b4c5ff',
          dark: '#1E3A8A', /* fallback old token */
          light: '#DBEAFE', /* fallback old token */
        },
        'on-primary': '#ffffff',
        'on-primary-container': '#eeefff',
        'inverse-primary': '#b4c5ff',
        'on-primary-fixed': '#00174b',
        'on-primary-fixed-variant': '#003ea8',
        
        secondary: {
          DEFAULT: '#4059aa',
          container: '#8fa7fe',
          fixed: '#dce1ff',
          'fixed-dim': '#b6c4ff',
        },
        'on-secondary': '#ffffff',
        'on-secondary-container': '#1d3989',
        'on-secondary-fixed': '#00164e',
        'on-secondary-fixed-variant': '#264191',
        
        tertiary: {
          DEFAULT: '#485767',
          container: '#606f80',
          fixed: '#d5e4f8',
          'fixed-dim': '#b9c8db',
        },
        'on-tertiary': '#ffffff',
        'on-tertiary-container': '#e9f2ff',
        'on-tertiary-fixed': '#0e1d2b',
        'on-tertiary-fixed-variant': '#3a4858',
        
        error: {
          DEFAULT: '#ba1a1a',
          container: '#ffdad6',
        },
        'on-error': '#ffffff',
        'on-error-container': '#93000a',
        
        background: '#f8f9ff',
        'on-background': '#121c2a',
        'surface-variant': '#d9e3f6',
        
        success: '#16A34A',
        warning: '#F59E0B',
        danger: '#DC2626',
        neutral: {
          50: '#F9FAFB',
          200: '#E5E7EB',
          500: '#6B7280',
          900: '#1F2937',
        },
      },
      borderRadius: {
        sm: '0.25rem',
        DEFAULT: '0.5rem',
        md: '0.75rem',
        lg: '1rem',
        xl: '1.5rem',
        full: '9999px',
      },
      spacing: {
        '1': '4px',
        '2': '8px',
        '3': '12px',
        '4': '16px',
        '6': '24px',
        '8': '32px',
        '12': '48px',
        '16': '64px',
      },
      fontFamily: {
        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
        inter: ['"Inter"', 'sans-serif'],
      },
      maxWidth: {
        content: '1200px',
      },
    },
  },
  plugins: [],
}
