/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/goodday/**/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                // PRIMARY COLORS
                primary: {
                    normal: "#3FAF4F",
                    dark: "#297935",
                },
                secondary: {
                    normal: "#2A9439",
                    dark: "#15441C",
                },
                loan: {
                    light: '#C6EECB',
                    normal: '#3faf4f',
                    dark: '#297935',
                }
            },
            fontSize: {
                xxs: '0.6rem',
                xs: '0.75rem',
                '15px': '15px',
                '17px': '17px',
            },
            opacity: {
                10: "0.10",
            },
            fontFamily: {
                'manrope': ['Manrope', ...require('tailwindcss/defaultTheme').fontFamily.sans],
            }
        },
    },
    variants: {
        extend: {
            borderWidth: ["hover"],
            opacity: ["disabled"],
            cursor: ["disabled"],
            textColor: ["disabled"],
        },
    },
    plugins: [
        require("@tailwindcss/forms"),
        require("@tailwindcss/typography"),
    ],
}
