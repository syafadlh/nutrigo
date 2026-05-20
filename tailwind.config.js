/** @type {import('tailwindcss').Config} */
export default {
    content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    ],
    theme: {
    extend: {
        colors: {
            'ng-yellow':  '#f1c40f',
            'ng-orange':  '#e8601a',
            'ng-green':   '#6ab04c',
            'ng-dark-green': '#185420',
            'ng-cream':   '#f3e8cc',
            'ng-red':     '#d52618',
        },
        fontFamily: {
            sans: ['Inter', 'sans-serif'],
        },
        },
    },
    plugins: [],
    }