/** @type {import('tailwindcss').Config} */
export default {
	content: [
		"./node_modules/flowbite/**/*.js",
		"./resources/**/*.blade.php",
		"./resources/**/*.vue",
		"./resources/**/*.js",
	],
	theme: {
		fontFamily: {
			primary: ["Poppins"],
		},
		extend: {
			colors: {
				primary: "#B18E63",
			},
		},
	},
};
