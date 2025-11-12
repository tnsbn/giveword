import globals from "globals";
import pluginJs from "@eslint/js";


export default [
  {files: ["**/*.js"], languageOptions: {sourceType: "commonjs"}},
  {languageOptions: { globals: globals.browser }},
  pluginJs.configs.recommended,
  {
    ignores: [
      ".config/*",
      "resources/js/bootstrap.bundle.js",
      // "resources/js/bootstrap.js",
      "resources/js/jquery.js",
      // "resources/sass/bootstrap.css",
    ]
  },
  {
    rules: {
      "no-unused-vars": "warn",
      "no-undef": "warn"
    }
  }
];