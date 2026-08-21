/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  content: [
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./lib/**/*.{js,ts,jsx,tsx,mdx}"
  ],
  theme: {
    screens: {
      sm: "640px",
      md: "768px",
      lg: "1024px",
      xl: "1280px",
      "2xl": "1536px"
    },
    extend: {
      colors: {
        canvas: "rgb(var(--canvas) / <alpha-value>)",
        surface: "rgb(var(--surface) / <alpha-value>)",
        elevated: "rgb(var(--elevated) / <alpha-value>)",
        ink: "rgb(var(--ink) / <alpha-value>)",
        muted: "rgb(var(--muted) / <alpha-value>)",
        line: "rgb(var(--line) / <alpha-value>)",
        acid: {
          200: "#ecffb5",
          400: "#cfff47",
          500: "#a9dc28",
          700: "#4f6f00"
        },
        signal: {
          400: "#91a9ff",
          500: "#4f6cff",
          600: "#3b55e6",
          700: "#2942bf"
        }
      },
      fontFamily: {
        sans: ["var(--font-manrope)", "Manrope", "ui-sans-serif", "system-ui"],
        display: ["var(--font-cormorant)", "Cormorant Garamond", "Georgia", "serif"]
      },
      fontSize: {
        xs: ["0.75rem", { lineHeight: "1rem" }],
        sm: ["0.875rem", { lineHeight: "1.25rem" }],
        base: ["1rem", { lineHeight: "1.625rem" }],
        lg: ["1.25rem", { lineHeight: "1.75rem" }],
        xl: ["1.563rem", { lineHeight: "2rem" }],
        "2xl": ["1.953rem", { lineHeight: "2.25rem" }],
        "3xl": ["2.441rem", { lineHeight: "2.625rem" }],
        "4xl": ["3.052rem", { lineHeight: "3.1rem" }],
        "5xl": ["3.815rem", { lineHeight: "3.75rem" }]
      },
      borderRadius: {
        xl: "0.75rem",
        "2xl": "1rem",
        "3xl": "1.5rem"
      },
      boxShadow: {
        luxury: "0 28px 80px -40px rgb(14 16 24 / 0.42)",
        glow: "0 0 0 1px rgb(79 108 255 / 0.22), 0 22px 54px -28px rgb(79 108 255 / 0.52)"
      },
      transitionDuration: {
        160: "160ms",
        240: "240ms",
        400: "400ms"
      }
    }
  },
  plugins: []
};
