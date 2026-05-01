import "@mdi/font/css/materialdesignicons.css"
import "vuetify/styles"

import { createVuetify } from "vuetify"
import * as components from "vuetify/components"
import * as directives from "vuetify/directives"

const savedTheme = localStorage.getItem("theme") || "light"

export default createVuetify({
  components,
  directives,
  theme: {
    defaultTheme: savedTheme,
    themes: {
      light: {
        dark: false,
        colors: {
          background: "#f5f5f7",
          surface: "#ffffff",
          primary: "#1976D2",
        },
      },
      dark: {
        dark: true,
        colors: {
          background: "#121212",
          surface: "#1e1e1e",
          primary: "#90CAF9",
        },
      },
    },
  },
})