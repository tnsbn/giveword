import js from "@eslint/js";

export default [
  js.configs.recommended,
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
