module.exports = {
  purge: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {},

    customForms: theme => ({
      default: {
        'input, textarea, multiselect, select': {
          backgroundColor: theme('colors.gray.700'),
          borderColor: theme('colors.gray.800'),
          '&:focus': {
            backgroundColor: theme('colors.gray.500'),
            boxShadow: theme('boxShadow.outline-indigo'),
            borderColor: theme('colors.indigo.500')
          }
        },
      }})
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
  ],
}
